<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Movement;
use App\Models\Supplier;
use App\Models\PurchaseOrder;
use App\Models\StockMovement;
use App\Models\CashMovement;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;
use Barryvdh\DomPDF\Facade\Pdf;

class SupplyController extends Controller
{
    /**
     * Affiche la liste des approvisionnements (basée sur les mouvements d'entrée et leurs commandes).
     */
    public function index(Request $request)
    {
        $boutiqueId = Auth::user()->boutique_id;

        $query = Movement::with(['products', 'user', 'purchaseOrder.supplier'])
            ->where('type', 'entree')
            ->where('boutique_id', $boutiqueId);

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('motif', 'like', "%{$search}%")
                  ->orWhereHas('products', function($subQ) use ($search) {
                      $subQ->where('name', 'like', "%{$search}%");
                  })
                  ->orWhereHas('purchaseOrder.supplier', function($subQ) use ($search) {
                      $subQ->where('name', 'like', "%{$search}%");
                  });
            });
        }

        $suppliesPaginator = $query->latest()->paginate(15);
        $currentItems = collect($suppliesPaginator->items());
        $currentItemsActives = $currentItems->whereNull('annule_at');

        $totalOperations = $currentItemsActives->count();

        $totalArticles = $currentItemsActives->sum(function($movement) {
            return $movement->products->sum('pivot.qty');
        });

        // CORRECTION : On récupère le montant réel exact depuis les mouvements de caisse (CashMovement) 
        // pour éviter le décalage causé par le prix d'achat actuel des produits.
        $activeMovementIds = $currentItemsActives->pluck('id');
        $totalDepenses = CashMovement::where('boutique_id', $boutiqueId)
            ->whereIn('movement_id', $activeMovementIds)
            ->where('type', 'out')
            ->sum('amount');

        $totalDepensesFormatted = number_format($totalDepenses, 0, ',', ' ') . ' FCFA';

        $groupedSupplies = $currentItems->groupBy(function($supply) {
            return $supply->created_at->format('Y-m-d');
        });
        
        return view('supplies.index', compact(
            'groupedSupplies', 
            'suppliesPaginator', 
            'totalOperations', 
            'totalArticles', 
            'totalDepensesFormatted'
        ));
    }

    /**
     * Affiche le formulaire d'approvisionnement.
     */
    public function create()
    {
        $boutiqueId = Auth::user()->boutique_id;
        $products = Product::where('boutique_id', $boutiqueId)->get();
        $suppliers = Supplier::where('boutique_id', $boutiqueId)->with('products')->get();

        return view('supplies.create', compact('products', 'suppliers'));
    }

    /**
     * Enregistre la commande, crée le mouvement, met à jour le stock et la caisse.
     */
    public function store(Request $request)
    {
        $boutiqueId = Auth::user()->boutique_id;

        $request->validate([
            'supplier_id' => [
                'required',
                Rule::exists('suppliers', 'id')->where('boutique_id', $boutiqueId),
            ],
            'motif' => 'nullable|string|max:255',
            'products' => 'required|array|min:1',
            'products.*.id' => [
                'required',
                Rule::exists('products', 'id')->where('boutique_id', $boutiqueId),
                function ($attribute, $value, $fail) use ($request) {
                    if ($request->filled('supplier_id')) {
                        $supplier = Supplier::find($request->supplier_id);
                        if ($supplier && !$supplier->products()->where('product_id', $value)->exists()) {
                            $fail("Le produit sélectionné n'est pas assuré par ce fournisseur.");
                        }
                    }
                },
            ],
            'products.*.qty' => 'required|integer|min:1',
            'products.*.unit_price' => 'required|numeric|min:0.01',
        ], [
            'products.*.unit_price.min' => 'Le prix d\'achat unitaire ne peut pas être égal à 0 ou négatif.',
        ]);

        $totalAmount = 0;
        foreach ($request->products as $item) {
            $totalAmount += $item['qty'] * $item['unit_price'];
        }

        $totalIn = CashMovement::where('boutique_id', $boutiqueId)->where('type', 'in')->sum('amount');
        $totalOut = CashMovement::where('boutique_id', $boutiqueId)->where('type', 'out')->sum('amount');
        $currentCashBalance = $totalIn - $totalOut;

        if ($totalAmount > $currentCashBalance && !$request->has('force_cash')) {
            return back()->withInput()->with([
                'insufficient_cash' => true,
                'required_amount' => $totalAmount,
                'current_balance' => $currentCashBalance,
            ]);
        }

        DB::transaction(function () use ($request, $totalAmount, $boutiqueId) {
            $purchaseOrder = PurchaseOrder::create([
                'supplier_id' => $request->supplier_id,
                'boutique_id' => $boutiqueId,
                'user_id' => Auth::id(),
                'status' => 'recue',
            ]);

            $movement = Movement::create([
                'boutique_id' => $boutiqueId,
                'type' => 'entree',
                'date' => now()->toDateString(),
                'user_id' => Auth::id(),
                'purchase_order_id' => $purchaseOrder->id,
                'motif' => $request->motif ?? 'Approvisionnement stock via fournisseur',
            ]);

            foreach ($request->products as $item) {
                $productId = $item['id'];
                $qty = $item['qty'];
                $unitPrice = $item['unit_price'];

                $purchaseOrder->products()->attach($productId, [
                    'qty' => $qty,
                    'unit_price' => $unitPrice,
                ]);

                // Ajout de unit_price dans le pivot pour l'historique des mouvements
                $movement->products()->attach($productId, [
                    'qty' => $qty,
                    'unit_price' => $unitPrice,
                ]);

                $product = Product::lockForUpdate()->find($productId);
                $oldStock = $product->quantity;
                $newStock = $oldStock + $qty;

                $product->update([
                    'quantity' => $newStock,
                    'purchase_price' => $unitPrice
                ]);

                StockMovement::create([
                    'boutique_id' => $boutiqueId,
                    'product_id'  => $product->id,
                    'user_id'     => Auth::id(),
                    'type'        => 'entry',
                    'quantity'    => $qty,
                    'old_stock'   => $oldStock,
                    'new_stock'   => $newStock,
                    'reason'      => $request->motif ?? 'Approvisionnement fournisseur',
                ]);
            }

            CashMovement::create([
                'boutique_id' => $boutiqueId,
                'movement_id' => $movement->id,
                'user_id' => Auth::id(),
                'type' => 'out',
                'category' => 'restock',
                'amount' => $totalAmount,
                'motif' => $request->motif ?? 'Approvisionnement stock fournisseur #' . $purchaseOrder->id,
                'is_unjustified' => false,
            ]);
        });

        return redirect()->route('supplies.index')
                         ->with('success', 'Approvisionnement enregistré avec succès.');
    }

    /**
     * Affiche le formulaire de modification.
     */
    public function edit($id)
    {
        $boutiqueId = Auth::user()->boutique_id;
        $supply = Movement::with(['products', 'purchaseOrder'])->where('type', 'entree')->where('boutique_id', $boutiqueId)->findOrFail($id);
        $products = Product::where('boutique_id', $boutiqueId)->get();
        $suppliers = Supplier::where('boutique_id', $boutiqueId)->with('products')->get();

        return view('supplies.edit', compact('supply', 'products', 'suppliers'));
    }

    /**
     * Met à jour un approvisionnement existant en gérant l'impact sur le stock et la caisse.
     */
    public function update(Request $request, $id)
    {
        $boutiqueId = Auth::user()->boutique_id;

        $request->validate([
            'supplier_id' => [
                'required',
                Rule::exists('suppliers', 'id')->where('boutique_id', $boutiqueId),
            ],
            'motif' => 'nullable|string|max:255',
            'products' => 'required|array|min:1',
            'products.*.id' => [
                'required',
                Rule::exists('products', 'id')->where('boutique_id', $boutiqueId),
                function ($attribute, $value, $fail) use ($request) {
                    if ($request->filled('supplier_id')) {
                        $supplier = Supplier::find($request->supplier_id);
                        if ($supplier && !$supplier->products()->where('product_id', $value)->exists()) {
                            $fail("Le produit sélectionné n'est pas assuré par ce fournisseur.");
                        }
                    }
                },
            ],
            'products.*.qty' => 'required|integer|min:1',
            'products.*.unit_price' => 'required|numeric|min:0.01',
        ], [
            'products.*.unit_price.min' => 'Le prix d\'achat unitaire ne peut pas être égal à 0 ou négatif.',
        ]);

        $supply = Movement::where('type', 'entree')->where('boutique_id', $boutiqueId)->findOrFail($id);

        if ($supply->annule_at) {
            return back()->with('error', 'Impossible de modifier un approvisionnement déjà annulé.');
        }

        $newTotalAmount = 0;
        foreach ($request->products as $item) {
            $newTotalAmount += $item['qty'] * $item['unit_price'];
        }

        DB::transaction(function () use ($request, $supply, $newTotalAmount, $boutiqueId) {
            foreach ($supply->products as $product) {
                $qtyOld = $product->pivot->qty;
                $product->decrement('quantity', $qtyOld);
            }

            if ($supply->purchase_order_id) {
                $purchaseOrder = PurchaseOrder::find($supply->purchase_order_id);
                if ($purchaseOrder) {
                    $purchaseOrder->update(['supplier_id' => $request->supplier_id]);
                    $purchaseOrder->products()->detach();
                    
                    foreach ($request->products as $item) {
                        $purchaseOrder->products()->attach($item['id'], [
                            'qty' => $item['qty'],
                            'unit_price' => $item['unit_price'],
                        ]);
                    }
                }
            }

            $supply->update(['motif' => $request->motif]);
            $supply->products()->detach();

            foreach ($request->products as $item) {
                $productId = $item['id'];
                $qty = $item['qty'];
                $unitPrice = $item['unit_price'];

                $supply->products()->attach($productId, [
                    'qty' => $qty,
                    'unit_price' => $unitPrice,
                ]);

                $product = Product::lockForUpdate()->find($productId);
                $oldStock = $product->quantity;
                $newStock = $oldStock + $qty;

                $product->update([
                    'quantity' => $newStock,
                    'purchase_price' => $unitPrice
                ]);

                StockMovement::create([
                    'boutique_id' => $boutiqueId,
                    'product_id'  => $product->id,
                    'user_id'     => Auth::id(),
                    'type'        => 'adjustment',
                    'quantity'    => $qty,
                    'old_stock'   => $oldStock,
                    'new_stock'   => $newStock,
                    'reason'      => 'Mise à jour approvisionnement #' . $supply->id,
                ]);
            }

            CashMovement::where('boutique_id', $boutiqueId)
                ->where('movement_id', $supply->id)
                ->delete();

            CashMovement::create([
                'boutique_id' => $boutiqueId,
                'movement_id' => $supply->id,
                'user_id' => Auth::id(),
                'type' => 'out',
                'category' => 'restock',
                'amount' => $newTotalAmount,
                'motif' => $request->motif ?? 'Mise à jour approvisionnement #' . $supply->id,
                'is_unjustified' => false,
            ]);
        });

        return redirect()->route('supplies.index')
                         ->with('success', 'Approvisionnement modifié avec succès.');
    }

    /**
     * Annule un approvisionnement.
     */
    public function destroy($id)
    {
        DB::transaction(function () use ($id) {
            $boutiqueId = Auth::user()->boutique_id;
            $supply = Movement::where('type', 'entree')->where('boutique_id', $boutiqueId)->findOrFail($id);

            if ($supply->annule_at) {
                return;
            }

            foreach ($supply->products as $product) {
                $qty = $product->pivot->qty;

                $productModel = Product::lockForUpdate()->find($product->id);
                $oldStock = $productModel->quantity;
                $newStock = max(0, $oldStock - $qty);

                $productModel->update([
                    'quantity' => $newStock,
                    'status' => $newStock > 0 ? 'disponible' : 'rupture',
                ]);

                StockMovement::create([
                    'boutique_id' => $boutiqueId,
                    'product_id'  => $productModel->id,
                    'user_id'     => Auth::id(),
                    'type'        => 'adjustment',
                    'quantity'    => $qty,
                    'old_stock'   => $oldStock,
                    'new_stock'   => $newStock,
                    'reason'      => 'Annulation de l\'approvisionnement #' . $supply->id,
                ]);
            }

            // ON NE SUPPRIME PLUS LE PURCHASE ORDER ICI POUR GARDER LE NOM DU FOURNISSEUR !
            // if ($supply->purchase_order_id) {
            //     $purchaseOrder = PurchaseOrder::find($supply->purchase_order_id);
            //     if ($purchaseOrder) {
            //         $purchaseOrder->products()->detach();
            //         $purchaseOrder->delete();
            //     }
            // }

            CashMovement::where('boutique_id', $boutiqueId)
                ->where('movement_id', $supply->id)
                ->delete();

            $supply->update(['annule_at' => now()]);
        });

        return back()->with('success', 'Approvisionnement annulé, stock déduit et caisse rééquilibrée avec succès.');
    }

    public function exportDayPdf(Request $request)
    {
        $boutiqueId = Auth::user()->boutique_id;
        $date = $request->get('date', now()->format('Y-m-d'));

        $supplies = Movement::with(['products', 'user', 'purchaseOrder.supplier'])
            ->where('type', 'entree')
            ->where('boutique_id', $boutiqueId)
            ->whereNull('annule_at')
            ->whereDate('created_at', $date)
            ->latest()
            ->get();

        $title = "Approvisionnements du " . \Carbon\Carbon::parse($date)->translatedFormat('d F Y');

        $pdf = Pdf::loadView('supplies.pdf', compact('supplies', 'title'));
        
        return $pdf->download('approvisionnements_' . $date . '.pdf');
    }

    public function exportAllPdf()
    {
        $boutiqueId = Auth::user()->boutique_id;

        $supplies = Movement::with(['products', 'user', 'purchaseOrder.supplier'])
            ->where('type', 'entree')
            ->where('boutique_id', $boutiqueId)
            ->whereNull('annule_at')
            ->latest()
            ->get();

        $title = "Historique Global des Approvisionnements";

        $pdf = Pdf::loadView('supplies.pdf', compact('supplies', 'title'));

        return $pdf->download('tous_les_approvisionnements_' . now()->format('Y-m-d') . '.pdf');
    }
}