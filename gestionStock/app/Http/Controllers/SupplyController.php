<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Movement;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class SupplyController extends Controller
{
    /**
     * Affiche la liste des approvisionnements.
     */

public function index(Request $request)
{
    $boutiqueId = Auth::user()->boutique_id;

    $query = Movement::with(['products', 'user'])
        ->where('type', 'entree')
        ->where('boutique_id', $boutiqueId);

    if ($request->filled('search')) {
        $search = $request->search;
        $query->where(function($q) use ($search) {
            $q->where('motif', 'like', "%{$search}%")
              ->orWhereHas('products', function($subQ) use ($search) {
                  $subQ->where('name', 'like', "%{$search}%");
              });
        });
    }

    // 1. On récupère les résultats paginés
    $suppliesPaginator = $query->latest()->paginate(15);

    // 2. On récupère TOUS les résultats de la requête actuelle (sans pagination) 
    // pour calculer les totaux globaux exacts dans les cartes statistiques
    $allItems = (clone $query)->get();

    $totalArticles = $allItems->sum(function($movement) {
        return $movement->products->sum('pivot.qty');
    });

    $totalDepenses = $allItems->sum(function($movement) {
        return $movement->products->sum(function($product) {
            return $product->pivot->qty * $product->purchase_price;
        });
    });

    $totalDepensesFormatted = number_format($totalDepenses, 0, ',', ' ') . ' FCFA';

    // 3. On groupe les éléments de la page actuelle (items()) par date
    // .items() renvoie la collection des éléments de la page courante
    $groupedSupplies = collect($suppliesPaginator->items())->groupBy(function($supply) {
    return $supply->created_at->format('Y-m-d');
    });
    
    return view('supplies.index', compact('groupedSupplies', 'suppliesPaginator', 'totalArticles', 'totalDepensesFormatted'));
}
    /**
     * Affiche le formulaire d'approvisionnement.
     */
    public function create()
    {
        $products = Product::all();
        return view('supplies.create', compact('products'));
    }

    /**
     * Enregistre l'approvisionnement et augmente le stock.
     */
    public function store(Request $request)
    {
        $request->validate([
            'product_id' => 'required|exists:products,id',
            'quantity' => 'required|integer|min:1',
            'motif' => 'nullable|string|max:255',
        ]);

        DB::transaction(function () use ($request) {
            // 1. Enregistrer le mouvement d'entrée
            $movement = Movement::create([
                'boutique_id' => Auth::user()->boutique_id,
                'type' => 'entree',
                'date' => now()->toDateString(),
                'user_id' => Auth::id(),
                'motif' => $request->motif ?? 'Approvisionnement stock',
            ]);

            // 2. Attacher le produit dans la table pivot avec sa quantité (qty)
            $movement->products()->attach($request->product_id, [
                'qty' => $request->quantity
            ]);

            // 3. Incrémenter le stock du produit (colonne 'quantity')
            $product = Product::find($request->product_id);
            $product->increment('quantity', $request->quantity);
        });

        return redirect()->route('supplies.index')
                         ->with('success', 'Approvisionnement enregistré avec succès.');
    }

    /**
     * Annule un approvisionnement (retire la quantité du stock et supprime le mouvement).
     */
    public function destroy($id)
    {
        DB::transaction(function () use ($id) {
            $supply = Movement::where('type', 'entree')->findOrFail($id);
            
            // Récupérer la quantité depuis la table pivot avant la suppression
            foreach ($supply->products as $product) {
                $qty = $product->pivot->qty;
                // CORRECTION : Utiliser 'quantity' au lieu de 'stock'
                $product->decrement('quantity', $qty);
            }

            $supply->delete();
        });

        return back()->with('success', 'Approvisionnement annulé et stock corrigé avec succès.');
    }
}