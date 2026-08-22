<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Movement;
use App\Models\Supplier;
use App\Models\PurchaseOrder;
use App\Models\StockMovement;
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
     * Affiche le formulaire d'approvisionnement (avec la liste des fournisseurs et des produits).
     */
    public function create()
    {
        $boutiqueId = Auth::user()->boutique_id;
        $products = Product::where('boutique_id', $boutiqueId)->get();
        $suppliers = Supplier::where('boutique_id', $boutiqueId)->get();

        return view('supplies.create', compact('products', 'suppliers'));
    }

    /**
     * Enregistre la commande future, crée le mouvement d'entrée et augmente le stock.
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

        DB::transaction(function () use ($request) {
            $boutiqueId = Auth::user()->boutique_id;

            // 1. Création de la Commande Future (PurchaseOrder)
            $purchaseOrder = PurchaseOrder::create([
                'supplier_id' => $request->supplier_id,
                'boutique_id' => $boutiqueId,
                'user_id' => Auth::id(),
                'status' => 'recue',
            ]);

            // 2. Enregistrer le mouvement d'entrée lié à cette commande future
            $movement = Movement::create([
                'boutique_id' => $boutiqueId,
                'type' => 'entree',
                'date' => now()->toDateString(),
                'user_id' => Auth::id(),
                'purchase_order_id' => $purchaseOrder->id,
                'motif' => $request->motif ?? 'Approvisionnement stock via fournisseur',
            ]);

            // 3. Associer les produits à la commande future ET au mouvement
            foreach ($request->products as $item) {
                $productId = $item['id'];
                $qty = $item['qty'];
                $unitPrice = $item['unit_price'];

                // Liaison dans la table pivot purchase_order_products
                $purchaseOrder->products()->attach($productId, [
                    'qty' => $qty,
                    'unit_price' => $unitPrice,
                ]);

                // Liaison dans la table pivot movement_products
                $movement->products()->attach($productId, [
                    'qty' => $qty,
                ]);

                // 4. Récupérer le produit avec verrouillage pour mise à jour sécurisée
                $product = Product::lockForUpdate()->find($productId);
                $oldStock = $product->quantity;
                $newStock = $oldStock + $qty;

                // Incrémenter le stock et mettre à jour le prix d'achat
                $product->update([
                    'quantity' => $newStock,
                    'purchase_price' => $unitPrice
                ]);

                // --- ENREGISTREMENT DANS L'HISTORIQUE GLOBAL (StockMovement) ---
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

                // Nettoyage des alertes de stock si le stock dépasse le seuil
                if ($newStock > $product->alert_threshold) {
                    \App\Models\Notification::where('boutique_id', $boutiqueId)
                        ->where('title', 'Stock critique')
                        ->where('message', 'like', "%{$product->name}%")
                        ->delete();
                }
            }
        });

        return redirect()->route('supplies.index')
                         ->with('success', 'Approvisionnement et commande fournisseur enregistrés avec succès.');
    }

    /**
     * Annule un approvisionnement (retire la quantité du stock, supprime le mouvement et la commande associée).
     */
    public function destroy($id)
    {
        DB::transaction(function () use ($id) {
            $supply = Movement::where('type', 'entree')->findOrFail($id);
            
            // Retirer la quantité du stock pour chaque produit et tracer l'annulation
            foreach ($supply->products as $product) {
                $qty = $product->pivot->qty;
                $oldStock = $product->quantity;
                $newStock = max(0, $oldStock - $qty); // Évite un stock négatif

                $product->update(['quantity' => $newStock]);

                // --- TRACABILITÉ DE L'ANNULATION DANS L'HISTORIQUE ---
                StockMovement::create([
                    'boutique_id' => $supply->boutique_id,
                    'product_id'  => $product->id,
                    'user_id'     => Auth::id(),
                    'type'        => 'adjustment', // ou 'sortie' selon tes préférences
                    'quantity'    => $qty,
                    'old_stock'   => $oldStock,
                    'new_stock'   => $newStock,
                    'reason'      => 'Annulation de l\'approvisionnement #' . $supply->id,
                ]);
            }

            // Supprimer la commande future (PurchaseOrder) liée si elle existe
            if ($supply->purchase_order_id) {
                $purchaseOrder = PurchaseOrder::find($supply->purchase_order_id);
                if ($purchaseOrder) {
                    $purchaseOrder->products()->detach();
                    $purchaseOrder->delete();
                }
            }

            // Détacher les produits du mouvement et supprimer le mouvement
            $supply->products()->detach();
            $supply->delete();
        });

        return back()->with('success', 'Approvisionnement annulé, stock corrigé et commande supprimée avec succès.');
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