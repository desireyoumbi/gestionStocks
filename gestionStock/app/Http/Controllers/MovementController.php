<?php

namespace App\Http\Controllers;

use App\Models\Movement;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class MovementController extends Controller
{
    /**
     * Affiche la liste des mouvements de la boutique avec recherche et filtrage.
     */
    public function index(Request $request)
{
    $user = Auth::user();

    $query = Movement::where('boutique_id', $user->boutique_id)
        ->with(['user', 'products.category'])
        ->latest();

    // RESTRICTION SELON LE RÔLE : Si c'est un vendeur, il ne voit que ses propres mouvements
    if ($user->role === 'vendeur') {
        $query->where('user_id', $user->id);
    }

    // Filtre par recherche si besoin
    if ($request->filled('search')) {
        $query->where('motif', 'like', '%' . $request->search . '%');
    }

    // Filtre par type si besoin
    if ($request->filled('type')) {
        $query->where('type', $request->type);
    }

    $movements = $query->get();

    // On regroupe les mouvements par date (format YYYY-MM-DD)
    $groupedMovements = $movements->groupBy(function($movement) {
        return \Carbon\Carbon::parse($movement->created_at)->format('Y-m-d');
    });

    return view('movements.index', compact('groupedMovements'));
}
    /**
     * Affiche le formulaire de création d'un mouvement.
     */
    public function create()
    {
        // Récupère uniquement les produits de la boutique de l'utilisateur connecté
        $products = Product::where('boutique_id', Auth::user()->boutique_id)->get();

        return view('movements.create', compact('products'));
    }

    /**
     * Enregistre un nouveau mouvement et met à jour les stocks.
     */
    public function store(Request $request)
    {
        $request->validate([
            'type' => 'required|in:entree,sortie,ajustement',
            'date' => 'required|date',
            'motif' => 'required|string|max:255',
            'items' => 'required|array|min:1',
            'items.*.product_id' => 'required|exists:products,id',
            'items.*.qty' => 'required|integer|min:1',
        ]);

        // 1. Vérification préalable en cas de sortie pour bloquer avant la transaction si le stock est insuffisant
        if ($request->type === 'sortie') {
            foreach ($request->items as $item) {
                $product = Product::where('id', $item['product_id'])
                                  ->where('boutique_id', Auth::user()->boutique_id)
                                  ->firstOrFail();

                if ($product->quantity < $item['qty']) {
                    return back()->withInput()->withErrors([
                        'items' => "Stock insuffisant pour le produit : {$product->name} (Stock actuel : {$product->quantity}, Demandé : {$item['qty']})."
                    ]);
                }
            }
        }

        try {
            DB::transaction(function () use ($request) {
                // 2. Création de l'en-tête du mouvement
                $movement = Movement::create([
                    'boutique_id' => Auth::user()->boutique_id,
                    'type'        => $request->type,
                    'date'        => $request->date,
                    'motif'       => $request->motif,
                    'user_id'     => Auth::id(),
                ]);

                // 3. Traitement des produits liés via la relation Many-to-Many
                foreach ($request->items as $item) {
                    $product = Product::where('id', $item['product_id'])
                                      ->where('boutique_id', Auth::user()->boutique_id)
                                      ->firstOrFail();

                    // Attache le produit au mouvement avec sa quantité dans la table pivot
                    $movement->products()->attach($item['product_id'], [
                        'qty' => $item['qty']
                    ]);

                    // 4. Mise à jour du stock
                    if ($request->type === 'entree') {
                        $product->increment('quantity', $item['qty']);
                    } elseif ($request->type === 'sortie') {
                        $product->decrement('quantity', $item['qty']);
                    } elseif ($request->type === 'ajustement') {
                        // Exemple d'ajustement si besoin :
                        // $product->update(['quantity' => $item['qty']]);
                    }
                }
            });
        } catch (\Exception $e) {
            return back()->withInput()->withErrors(['error' => "Erreur lors de l'enregistrement : " . $e->getMessage()]);
        }

        return redirect()->route('movements.index')->with('success', 'Mouvement enregistré et stocks mis à jour avec succès.');
    }
    /**
     * Affiche les détails d'un mouvement spécifique.
     */
    public function show($id)
    {
        $movement = Movement::where('boutique_id', Auth::user()->boutique_id)
            ->with(['user', 'products'])
            ->findOrFail($id);

        return view('movements.show', compact('movement'));
    }

    /**
     * Affiche le formulaire d'édition (Optionnel, généralement limité au motif).
     */
    public function edit($id)
    {
        $movement = Movement::where('boutique_id', Auth::user()->boutique_id)->findOrFail($id);

        return view('movements.edit', compact('movement'));
    }

    /**
     * Met à jour les informations globales du mouvement (ex: motif).
     */
    public function update(Request $request, $id)
    {
        $movement = Movement::where('boutique_id', Auth::user()->boutique_id)->findOrFail($id);

        $request->validate([
            'motif' => 'required|string|max:255',
            'date'  => 'required|date',
        ]);

        $movement->update([
            'motif'      => $request->motif,
            'date'       => $request->date,
            'updated_by' => Auth::id(),
        ]);

        return redirect()->route('movements.index')->with('success', 'Mouvement mis à jour avec succès.');
    }

    /**
     * Supprime le mouvement et inverse l'impact sur les stocks.
     */
    public function destroy($id)
    {
        DB::transaction(function () use ($id) {
            $movement = Movement::where('boutique_id', Auth::user()->boutique_id)->findOrFail($id);

            // Inverser l'impact sur le stock avant de supprimer
            foreach ($movement->products as $product) {
                $qty = $product->pivot->qty;

                if ($movement->type === 'entree') {
                    $product->decrement('stock', $qty);
                } elseif ($movement->type === 'sortie') {
                    $product->increment('stock', $qty);
                }
            }

            // Supprime le mouvement (la suppression de la table pivot se fait automatiquement grâce au cascadeOnDelete)
            $movement->delete();
        });

        return redirect()->route('movements.index')->with('success', 'Mouvement supprimé et stocks réajustés.');
    }
}