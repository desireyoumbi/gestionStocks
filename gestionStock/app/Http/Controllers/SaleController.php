<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Movement;
use App\Models\StockMovement;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Barryvdh\DomPDF\Facade\Pdf;

class SaleController extends Controller
{
    /**
     * Affiche la liste des ventes effectuées.
     */
    public function index(Request $request)
    {
        $boutiqueId = auth()->user()->boutique_id;

        // 1. La requête principale (pour la liste paginée)
        $query = Movement::with(['products', 'user'])
            ->where('boutique_id', $boutiqueId)
            ->where('type', 'sortie');

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('motif', 'like', "%{$search}%")
                  ->orWhereHas('products', function($subQ) use ($search) {
                      $subQ->where('name', 'like', "%{$search}%");
                  });
            });
        }

        // On pagine les résultats
        $sales = $query->latest()->paginate(15);

        // 2. GROUPEMENT PAR DATE (pour l'affichage)
        $groupedSales = collect($sales->items())->groupBy(function($sale) {
            return $sale->created_at->format('Y-m-d');
        });

        // 3. CALCULS POUR LES CARTES STATISTIQUES
        $baseVentesQuery = Movement::where('boutique_id', $boutiqueId)->where('type', 'sortie');
        $totalVentesCount = (clone $baseVentesQuery)->count();

        $totalArticlesVendus = DB::table('movement_products')
            ->join('movements', 'movement_products.movement_id', '=', 'movements.id')
            ->where('movements.boutique_id', $boutiqueId)
            ->where('movements.type', 'sortie')
            ->sum(DB::raw('ABS(qty)')) ?? 0;

        $chiffreAffairesTotal = DB::table('movement_products')
            ->join('movements', 'movement_products.movement_id', '=', 'movements.id')
            ->join('products', 'movement_products.product_id', '=', 'products.id')
            ->where('movements.boutique_id', $boutiqueId)
            ->where('movements.type', 'sortie')
            ->sum(DB::raw('ABS(movement_products.qty) * products.selling_price')) ?? 0;

        $panierMoyen = $totalVentesCount > 0 ? number_format($chiffreAffairesTotal / $totalVentesCount, 0, ',', ' ') . ' FCFA' : '0 FCFA';

        $produitPhareData = DB::table('movement_products')
            ->join('movements', 'movement_products.movement_id', '=', 'movements.id')
            ->join('products', 'movement_products.product_id', '=', 'products.id')
            ->where('movements.boutique_id', $boutiqueId)
            ->where('movements.type', 'sortie')
            ->select('products.name', DB::raw('SUM(ABS(movement_products.qty)) as total_qty'))
            ->groupBy('products.id', 'products.name')
            ->orderByDesc('total_qty')
            ->first();

        $produitPhare = $produitPhareData ? $produitPhareData->name : 'Aucun';

        return view('sales.index', compact(
            'sales',
            'groupedSales',
            'panierMoyen',
            'totalArticlesVendus',
            'produitPhare'
        ));
    }

    /** 
     * Affiche le formulaire de création d'une vente.
     */
    public function create()
    {
        $boutiqueId = auth()->user()->boutique_id ?? 1;
        $products = Product::where('boutique_id', $boutiqueId)->get();
        
        return view('sales.create', compact('products'));
    }

    /**
     * Enregistre la vente et met à jour le stock.
     */
    public function store(Request $request)
    {
        $request->validate([
            'motif' => 'nullable|string|max:255',
            'items' => 'required|array|min:1',
            'items.*.product_id' => 'required|exists:products,id',
            'items.*.quantity' => 'required|integer|min:1',
        ]);

        try {
            DB::transaction(function () use ($request) {
                $boutiqueId = auth()->user()->boutique_id ?? 1;

                // 1. Créer le mouvement global (la facture de vente globale)
                $movement = Movement::create([
                    'boutique_id' => $boutiqueId,
                    'type' => 'sortie',
                    'date' => now(),
                    'user_id' => Auth::id(),
                    'motif' => $request->motif ?? 'Vente',
                ]);

                // 2. Parcourir chaque produit du panier
                foreach ($request->items as $item) {
                    $product = Product::lockForUpdate()->find($item['product_id']);

                    // Vérification du stock disponible
                    if ($product->quantity < $item['quantity']) {
                        throw new \Exception("Stock insuffisant pour le produit : {$product->name}. Stock actuel : {$product->quantity}");
                    }

                    $oldStock = $product->quantity;
                    $quantitySold = $item['quantity'];
                    $newStock = $oldStock - $quantitySold;

                    // Attacher dans la table pivot
                    $movement->products()->attach($product->id, ['qty' => $quantitySold]);

                    // Décrémenter le stock
                    $product->update(['quantity' => $newStock]);

                    // --- ENREGISTREMENT DANS L'HISTORIQUE GLOBAL (StockMovement) ---
                    StockMovement::create([
                        'boutique_id' => $boutiqueId,
                        'product_id'  => $product->id,
                        'user_id'     => Auth::id(),
                        'type'        => 'sale', // ou 'sortie' selon tes conventions d'historique
                        'quantity'    => $quantitySold,
                        'old_stock'   => $oldStock,
                        'new_stock'   => $newStock,
                        'reason'      => $request->motif ?? 'Vente de produits',
                    ]);
                }
            });
        } catch (\Exception $e) {
            return back()->withInput()->withErrors(['quantity' => $e->getMessage()]);
        }

        return redirect()->route('sales.index')
                         ->with('success', 'Vente enregistrée avec succès.');
    }

    /**
     * Annule une vente (supprime le mouvement et restitue le stock).
     */
    public function destroy($id)
    {
        DB::transaction(function () use ($id) {
            $sale = Movement::where('type', 'sortie')->findOrFail($id);
            
            // Parcourir les produits associés à cette vente pour restituer le stock et tracer l'annulation
            foreach ($sale->products as $product) {
                $pivotQty = $product->pivot->qty ?? ($product->pivot->quantity ?? 1);
                $oldStock = $product->quantity;
                $newStock = $oldStock + $pivotQty;

                // Restituer le stock
                $product->update(['quantity' => $newStock]);

                // --- TRACABILITÉ DE L'ANNULATION DANS L'HISTORIQUE ---
                StockMovement::create([
                    'boutique_id' => $sale->boutique_id,
                    'product_id'  => $product->id,
                    'user_id'     => Auth::id(),
                    'type'        => 'adjustment', // Entrée par annulation
                    'quantity'    => $pivotQty,
                    'old_stock'   => $oldStock,
                    'new_stock'   => $newStock,
                    'reason'      => 'Annulation de la vente #' . $sale->id,
                ]);
            }

            // Supprimer le mouvement de vente global
            $sale->delete();
        });

        return back()->with('success', 'Vente annulée et stock restitué avec succès.');
    }

    public function exportPdf()
    {
        $boutiqueId = session('boutique_id') ?? auth()->user()->boutique_id; 
        
        $query = Movement::where('type', 'sortie')->with(['products', 'user']);

        if ($boutiqueId) {
            $query->where('boutique_id', $boutiqueId);
        }

        $sales = $query->latest()->get();

        $totalGeneral = $sales->sum(function ($sale) {
            return $sale->products->sum(function ($product) {
                $qte = $product->pivot->qty ?? ($product->pivot->quantity ?? 0);
                $prix = $product->selling_price ?? 0;
                return $qte * $prix;
            });
        });

        $pdf = Pdf::loadView('sales.pdf-global', compact('sales', 'totalGeneral'));
        return $pdf->download('toutes-les-ventes.pdf');
    }

    public function exportPdfByDate($date)
    {
        $boutiqueId = session('boutique_id') ?? auth()->user()->boutique_id;
        
        $query = Movement::where('type', 'sortie')
                         ->whereDate('created_at', $date)
                         ->with(['products', 'user']);

        if ($boutiqueId) {
            $query->where('boutique_id', $boutiqueId);
        }

        $sales = $query->get();

        $totalGeneral = $sales->sum(function ($sale) {
            return $sale->products->sum(function ($product) {
                $qte = $product->pivot->qty ?? ($product->pivot->quantity ?? 0);
                $prix = $product->selling_price ?? 0;
                return $qte * $prix;
            });
        });

        $pdf = Pdf::loadView('sales.pdf-date', compact('sales', 'date', 'totalGeneral'));
        return $pdf->download('ventes-' . $date . '.pdf');
    }
}