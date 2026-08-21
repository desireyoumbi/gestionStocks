<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Movement;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

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

    // On pagine les résultats (on garde $sales pour la pagination en bas)
    $sales = $query->latest()->paginate(15);

    // 2. GROUPEMENT PAR DATE (pour l'affichage)
    // On transforme uniquement les résultats de la page courante en collection groupée
    $groupedSales = collect($sales->items())->groupBy(function($sale) {
        return $sale->created_at->format('Y-m-d');
    });

    // ==========================================
    // 3. CALCULS POUR LES CARTES STATISTIQUES (TES REQUÊTES)
    // ==========================================
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
        'sales', // On garde $sales pour $sales->links()
        'groupedSales', // La nouvelle variable pour la vue
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
       $boutiqueId = auth()->user()->boutique_id ?? 1; // On garde la sécurité du '?? 1' au cas où
    
    $products = Product::where('boutique_id', $boutiqueId)->get();
    
    return view('sales.create', compact('products'));
    }

    /**
     * Enregistre la vente et met à jour le stock.
     */
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
                // 1. Créer le mouvement global (la facture de vente globale)
                $movement = Movement::create([
                    'boutique_id' => auth()->user()->boutique_id ?? 1,
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

                    // Attacher dans la table pivot
                    $movement->products()->attach($product->id, ['qty' => $item['quantity']]);

                    // Décrémenter le stock
                    $product->decrement('quantity', $item['quantity']);
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
            
            // Restituer le stock (récupération de la quantité depuis la table pivot ou le mouvement selon votre structure)
            $product = Product::find($sale->product_id);
            if ($product) {
                // Si votre pivot stocke la quantité dans 'qty' :
                $pivotQty = $sale->products()->where('product_id', $product->id)->value('pivot_qty') ?? $sale->quantity ?? 1;
                $product->increment('quantity', $pivotQty);
            }

            // Supprimer le mouvement de vente
            $sale->delete();
        });

        return back()->with('success', 'Vente annulée et stock restitué avec succès.');
    }
}