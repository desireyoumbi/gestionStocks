<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Movement;
use App\Models\StockMovement;
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
            $boutiqueId = auth()->user()->boutique_id;

            $product = Product::where('id', $request->product_id)
                              ->where('boutique_id', $boutiqueId)
                              ->lockForUpdate()
                              ->firstOrFail();

            $oldStock = $product->quantity;
            $adjustmentQty = (int) $request->quantity; // Peut être +5 ou -2
            $newStock = $oldStock + $adjustmentQty;

            // Sécurité pour éviter un stock négatif lors d'un ajustement négatif
            if ($newStock < 0) {
                throw new \Exception("L'ajustement entraînerait un stock négatif ({$newStock}) pour ce produit.");
            }

            // 1. Enregistrement dans la table des mouvements (type 'ajustement')
            $movement = Movement::create([
                'boutique_id' => $boutiqueId,
                'type' => 'ajustement',
                'date' => now(),
                'user_id' => Auth::id(),
                'motif' => $request->motif,
            ]);

            // 2. Association dans la table pivot avec la quantité (+ ou -)
            $movement->products()->attach($product->id, [
                'qty' => $adjustmentQty
            ]);

            // 3. Mise à jour effective du stock du produit
            $product->update(['quantity' => $newStock]);

            // 4. --- ENREGISTREMENT DANS L'HISTORIQUE GLOBAL (StockMovement) ---
            StockMovement::create([
                'boutique_id' => $boutiqueId,
                'product_id'  => $product->id,
                'user_id'     => Auth::id(),
                'type'        => $adjustmentQty >= 0 ? 'entry' : 'adjustment',
                'quantity'    => abs($adjustmentQty),
                'old_stock'   => $oldStock,
                'new_stock'   => $newStock,
                'reason'      => $request->motif,
            ]);
        });

        return redirect()->route('adjustments.index')->with('success', 'Ajustement de stock enregistré avec succès.');
    }
}