<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Product;
use App\Models\Movement;
use Carbon\Carbon;

class StockScanController extends Controller
{
    public function index()
    {
        return view('stock.scan');
    }

    public function processScan(Request $request)
    {
        $request->validate([
            'reference' => 'required|string|max:255',
            'action_type' => 'required|in:edit,sell,supply'
        ]);

        $product = Product::where('reference', $request->reference)->first();

        if (!$product) {
            return redirect()->route('stock.scan.index')
                             ->with('error', 'Aucun produit trouvé avec la référence : "' . $request->reference . '"');
        }

        if ($request->action_type === 'edit') {
            return redirect()->route('products.edit', $product->id)
                             ->with('success', 'Produit reconnu : ' . $product->name);
        }

        return view('stock.confirm-movement', [
            'product' => $product,
            'actionType' => $request->action_type
        ]);
    }

    public function storeMovement(Request $request)
    {
        $request->validate([
            'product_id' => 'required|exists:products,id',
            'action_type' => 'required|in:sell,supply',
            'quantity' => 'required|integer|min:1'
        ]);

        $product = Product::findOrFail($request->product_id);
        $quantity = (int) $request->quantity;

        if ($request->action_type === 'sell') {
            if ($product->quantity < $quantity) {
                return redirect()->route('stock.scan.index')
                                 ->with('error', 'Stock insuffisant ! Stock actuel : ' . $product->quantity);
            }

            // 1. Diminuer le stock du produit
            $product->decrement('quantity', $quantity);

            // 2. Créer le mouvement de type 'sortie' (correspondant à une vente)
            $movement = Movement::create([
                'boutique_id' => $product->boutique_id ?? auth()->user()->boutique_id,
                'type'        => 'sortie', 
                'date'        => Carbon::now(),
                'motif'       => 'Vente rapide par scan',
                'user_id'     => auth()->id(),
            ]);

            // 3. Attacher le produit au mouvement via la table pivot
            $movement->products()->attach($product->id, ['qty' => $quantity]);

            return redirect()->route('stock.scan.index')
                             ->with('success', 'Vente enregistrée avec succès : ' . $quantity . 'x ' . $product->name);
        } 
        
        if ($request->action_type === 'supply') {
            // 1. Augmenter le stock
            $product->increment('quantity', $quantity);

            // 2. Créer le mouvement de type 'entree' (correspondant à un approvisionnement)
            $movement = Movement::create([
                'boutique_id' => $product->boutique_id ?? auth()->user()->boutique_id,
                'type'        => 'entree', 
                'date'        => Carbon::now(),
                'motif'       => 'Approvisionnement rapide par scan',
                'user_id'     => auth()->id(),
            ]);

            // 3. Attacher le produit au mouvement via la table pivot
            $movement->products()->attach($product->id, ['qty' => $quantity]);

            return redirect()->route('stock.scan.index')
                             ->with('success', 'Approvisionnement réussi : +' . $quantity . ' ' . $product->name);
        }

        return redirect()->route('stock.scan.index')->with('error', 'Action non reconnue.');
    }
}