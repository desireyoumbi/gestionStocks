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

        // 1. On récupère les résultats paginés
        $suppliesPaginator = $query->latest()->paginate(15);

        // 2. On récupère uniquement les éléments de la page actuelle sous forme de collection
        $currentItems = collect($suppliesPaginator->items());

        // Calculs basés uniquement sur la page actuelle
        $totalOperations = $currentItems->count();

        $totalArticles = $currentItems->sum(function($movement) {
            return $movement->products->sum('pivot.qty');
        });

        $totalDepenses = $currentItems->sum(function($movement) {
            return $movement->products->sum(function($product) {
                return $product->pivot->qty * $product->purchase_price;
            });
        });

        $totalDepensesFormatted = number_format($totalDepenses, 0, ',', ' ') . ' FCFA';

        // 3. On groupe les éléments de la page actuelle par date
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
        $suppliers = Supplier::where('boutique_id', $boutiqueId)->get();

        return view('supplies.create', compact('products', 'suppliers'));
    }

    /**
     * Enregistre la commande, crée le mouvement, met à jour le stock et la caisse.
     */
    public function store(Request $request)
    {
        $request->validate([
            'supplier_id' => 'required|exists:suppliers,id',
            'motif' => 'nullable|string|max:255',
            'products' => 'required|array|min:1',
            'products.*.id' => 'required|exists:products,id',
            'products.*.qty' => 'required|integer|min:1',
            'products.*.unit_price' => 'required|numeric|min:0',
        ]);

        $user = Auth::user();
        $boutique = $user->boutique; 

        // 1. Calculer le montant total de l'approvisionnement
        $totalAmount = 0;
        foreach ($request->products as $item) {
            $totalAmount += $item['qty'] * $item['unit_price'];
        }

        // 2. Vérifier le solde de la caisse et bloquer via la clé 'cash_error' si insuffisant
        $currentCashBalance = $boutique ? $boutique->getCashBalance() : 0;

        if ($totalAmount > $currentCashBalance) {
            return redirect()->back()
                ->withInput()
                ->with('cash_error', 'Opération bloquée : Fonds insuffisants en caisse (' . number_format($currentCashBalance, 0, ',', ' ') . ' FCFA disponibles pour un montant requis de ' . number_format($totalAmount, 0, ',', ' ') . ' FCFA). Veuillez ajuster la caisse.');
        }

        DB::transaction(function () use ($request, $user, $totalAmount) {
            $boutiqueId = $user->boutique_id;

            // 3. Création de la Commande Future (PurchaseOrder)
            $purchaseOrder = PurchaseOrder::create([
                'supplier_id' => $request->supplier_id,
                'boutique_id' => $boutiqueId,
                'user_id' => Auth::id(),
                'status' => 'recue',
            ]);

            // 4. Enregistrer le mouvement d'entrée
            $movement = Movement::create([
                'boutique_id' => $boutiqueId,
                'type' => 'entree',
                'date' => now()->toDateString(),
                'user_id' => Auth::id(),
                'purchase_order_id' => $purchaseOrder->id,
                'motif' => $request->motif ?? 'Approvisionnement stock via fournisseur',
            ]);

            // 5. Associer les produits et mettre à jour le stock
            foreach ($request->products as $item) {
                $productId = $item['id'];
                $qty = $item['qty'];
                $unitPrice = $item['unit_price'];

                $purchaseOrder->products()->attach($productId, [
                    'qty' => $qty,
                    'unit_price' => $unitPrice,
                ]);

                $movement->products()->attach($productId, [
                    'qty' => $qty,
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

            // 6. Enregistrer la sortie d'argent dans la caisse
            CashMovement::create([
                'boutique_id' => $boutiqueId,
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
        $suppliers = Supplier::where('boutique_id', $boutiqueId)->get();

        return view('supplies.edit', compact('supply', 'products', 'suppliers'));
    }

    /**
     * Met à jour un approvisionnement existant en gérant l'impact sur le stock et la caisse.
     */
    public function update(Request $request, $id)
    {
        $request->validate([
            'supplier_id' => 'required|exists:suppliers,id',
            'motif' => 'nullable|string|max:255',
            'products' => 'required|array|min:1',
            'products.*.id' => 'required|exists:products,id',
            'products.*.qty' => 'required|integer|min:1',
            'products.*.unit_price' => 'required|numeric|min:0',
        ]);

        $boutiqueId = Auth::user()->boutique_id;
        $supply = Movement::where('type', 'entree')->where('boutique_id', $boutiqueId)->findOrFail($id);

        // 1. Calculer le nouveau montant total demandé
        $newTotalAmount = 0;
        foreach ($request->products as $item) {
            $newTotalAmount += $item['qty'] * $item['unit_price'];
        }

        // 2. Calculer l'ancien montant qui avait été débité de la caisse
        $oldTotalAmount = 0;
        foreach ($supply->products as $prod) {
            $oldTotalAmount += $prod->pivot->qty * $prod->pivot->unit_price; 
        }

        // 3. Vérifier si la caisse peut supporter la différence
        $boutique = Auth::user()->boutique;
        $currentCashBalance = $boutique ? $boutique->getCashBalance() : 0;
        
        $projectedBalance = $currentCashBalance + $oldTotalAmount - $newTotalAmount;

        if ($projectedBalance < 0) {
            return redirect()->back()
                ->withInput()
                ->with('cash_error', 'Modification bloquée : Fonds insuffisants en caisse pour couvrir ce nouveau montant (' . number_format($newTotalAmount, 0, ',', ' ') . ' FCFA). Veuillez ajuster la caisse.');
        }

        DB::transaction(function () use ($request, $supply, $newTotalAmount, $boutiqueId) {
            // A. Annuler l'ancien impact sur les stocks
            foreach ($supply->products as $product) {
                $qtyOld = $product->pivot->qty;
                $product->decrement('quantity', $qtyOld);
            }

            // B. Mettre à jour la commande associée
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

            // C. Mettre à jour le mouvement d'entrée
            $supply->update(['motif' => $request->motif]);
            $supply->products()->detach();

            foreach ($request->products as $item) {
                $productId = $item['id'];
                $qty = $item['qty'];
                $unitPrice = $item['unit_price'];

                $supply->products()->attach($productId, ['qty' => $qty]);

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

            // D. Mettre à jour ou recréer le mouvement de caisse lié
            CashMovement::where('boutique_id', $boutiqueId)
                ->where('category', 'restock')
                ->where('motif', 'like', "%#{$supply->purchase_order_id}%")
                ->delete(); 

            CashMovement::create([
                'boutique_id' => $boutiqueId,
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
     * Annule un approvisionnement (retire la quantité du stock et rembourse virtuellement la caisse).
     */
    public function destroy($id)
    {
        DB::transaction(function () use ($id) {
            $boutiqueId = Auth::user()->boutique_id;
            $supply = Movement::where('type', 'entree')->where('boutique_id', $boutiqueId)->findOrFail($id);
            
            $totalRefundAmount = 0;
            foreach ($supply->products as $product) {
                $qty = $product->pivot->qty;
                $unitPrice = $product->purchase_price ?? 0;
                $totalRefundAmount += $qty * $unitPrice;

                $oldStock = $product->quantity;
                $newStock = max(0, $oldStock - $qty);

                $product->update(['quantity' => $newStock]);

                StockMovement::create([
                    'boutique_id' => $boutiqueId,
                    'product_id'  => $product->id,
                    'user_id'     => Auth::id(),
                    'type'        => 'adjustment',
                    'quantity'    => $qty,
                    'old_stock'   => $oldStock,
                    'new_stock'   => $newStock,
                    'reason'      => 'Annulation de l\'approvisionnement #' . $supply->id,
                ]);
            }

            if ($supply->purchase_order_id) {
                $purchaseOrder = PurchaseOrder::find($supply->purchase_order_id);
                if ($purchaseOrder) {
                    $purchaseOrder->products()->detach();
                    $purchaseOrder->delete();
                }
            }

            if ($totalRefundAmount > 0) {
                CashMovement::create([
                    'boutique_id' => $boutiqueId,
                    'user_id' => Auth::id(),
                    'type' => 'in',
                    'category' => 'refund',
                    'amount' => $totalRefundAmount,
                    'motif' => 'Remboursement suite annulation approvisionnement #' . $supply->id,
                    'is_unjustified' => false,
                ]);
            }

            $supply->products()->detach();
            $supply->delete();
        });

        return back()->with('success', 'Approvisionnement annulé et stock corrigé avec succès.');
    }

    // Export PDF de la journée
    public function exportDayPdf(Request $request)
    {
        $boutiqueId = Auth::user()->boutique_id;
        $date = $request->get('date', now()->format('Y-m-d'));

        $supplies = Movement::with(['products', 'user', 'purchaseOrder.supplier'])
            ->where('type', 'entree')
            ->where('boutique_id', $boutiqueId)
            ->whereDate('created_at', $date)
            ->latest()
            ->get();

        $title = "Approvisionnements du " . \Carbon\Carbon::parse($date)->translatedFormat('d F Y');

        $pdf = Pdf::loadView('supplies.pdf', compact('supplies', 'title'));
        
        return $pdf->download('approvisionnements_' . $date . '.pdf');
    }

    // Export PDF de tout l'historique
    public function exportAllPdf()
    {
        $boutiqueId = Auth::user()->boutique_id;

        $supplies = Movement::with(['products', 'user', 'purchaseOrder.supplier'])
            ->where('type', 'entree')
            ->where('boutique_id', $boutiqueId)
            ->latest()
            ->get();

        $title = "Historique Global des Approvisionnements";

        $pdf = Pdf::loadView('supplies.pdf', compact('supplies', 'title'));

        return $pdf->download('tous_les_approvisionnements_' . now()->format('Y-m-d') . '.pdf');
    }
}