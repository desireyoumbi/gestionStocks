<?php
namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Movement;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class AdjustmentController extends Controller
{
    public function index()
    {
        // On affiche uniquement les ajustements de la boutique
        $adjustments = Movement::where('type', 'ajustement')
            ->where('boutique_id', auth()->user()->boutique_id)
            ->latest()
            ->paginate(15);
            
        return view('adjustments.index', compact('adjustments'));
    }

    public function create()
    {
        // Liste des produits de la boutique pour le menu déroulant
        $products = Product::where('boutique_id', auth()->user()->boutique_id)->get();
        return view('adjustments.create', compact('products'));
    }


public function store(Request $request)
{
    $request->validate([
        'product_id' => 'required|exists:products,id',
        'quantity' => 'required|integer', // Accepte les positifs et négatifs
        'motif' => 'required|string|max:255',
    ]);

    DB::transaction(function () use ($request) {
        $product = Product::where('id', $request->product_id)
                          ->where('boutique_id', auth()->user()->boutique_id)
                          ->lockForUpdate()
                          ->firstOrFail();

        // 1. Enregistrement dans la table des mouvements (type 'ajustement')
        $movement = Movement::create([
            'boutique_id' => auth()->user()->boutique_id,
            'type' => 'ajustement',
            'date' => now(),
            'user_id' => Auth::id(),
            'motif' => $request->motif,
        ]);

        // 2. Association dans la table pivot avec la quantité (+ ou -)
        $movement->products()->attach($product->id, [
            'qty' => $request->quantity
        ]);

        // 3. Mise à jour effective du stock du produit
        // Le stock actuel s'additionne avec la quantité (ex: stock 10 + (-1) = 9)
        $product->quantity += $request->quantity;
        $product->save();
    });

    return redirect()->route('adjustments.index')->with('success', 'Ajustement de stock enregistré avec succès.');
}
}