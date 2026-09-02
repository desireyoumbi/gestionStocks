<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Product;
use App\Models\Movement;
use App\Models\StockMovement;
use App\Models\CashMovement;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

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

        $boutiqueId = Auth::user()->boutique_id;
        
        // Chargement du produit avec ses fournisseurs pour le cas de l'approvisionnement
        $product = Product::where('boutique_id', $boutiqueId)
            ->where('reference', $request->reference)
            ->with('suppliers')
            ->first();

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
            'reference'  => 'nullable|string|max:255',
            'action_type' => 'required|in:sell,supply',
            'quantity' => 'required|integer|min:1',
            'supplier_id' => 'required_if:action_type,supply|nullable|exists:suppliers,id',
            'selling_price' => 'required_if:action_type,sell|numeric|min:1',
        ]);

        $boutiqueId = Auth::user()->boutique_id;
        $quantity = (int) $request->quantity;
        $userId = Auth::id();

        try {
            $message = '';

            DB::transaction(function () use ($request, $boutiqueId, $quantity, $userId, &$message) {
                $product = Product::where('boutique_id', $boutiqueId)
                    ->where('id', $request->product_id)
                    ->with('suppliers')
                    ->lockForUpdate()
                    ->firstOrFail();

                if ($request->action_type === 'sell') {
                    if ($product->quantity < $quantity) {
                        throw new \Exception('Stock insuffisant ! Stock actuel : ' . $product->quantity);
                    }

                    $oldStock = $product->quantity;
                    $newStock = $oldStock - $quantity;
                    
                    // Utilisation du prix négocié transmis par le formulaire (avec repli de sécurité sur le prix de base)
                    $negotiatedPrice = $request->input('selling_price', $product->selling_price);
                    $totalSaleAmount = $quantity * $negotiatedPrice;

                    $product->update([
                        'quantity' => $newStock,
                        'status' => $newStock > 0 ? 'disponible' : 'rupture'
                    ]);

                    $movement = Movement::create([
                        'boutique_id' => $boutiqueId,
                        'type'        => 'sortie', 
                        'date'        => Carbon::now(),
                        'motif'       => 'Vente rapide par scan',
                        'user_id'     => $userId,
                    ]);

                    $movement->products()->attach($product->id, ['qty' => $quantity]);

                    StockMovement::create([
                        'boutique_id' => $boutiqueId,
                        'product_id'  => $product->id,
                        'user_id'     => $userId,
                        'type'        => 'sale',
                        'quantity'    => $quantity,
                        'old_stock'   => $oldStock,
                        'new_stock'   => $newStock,
                        'reason'      => 'Vente rapide par scan',
                    ]);

                    CashMovement::create([
                        'boutique_id'    => $boutiqueId,
                        'movement_id'    => $movement->id,
                        'user_id'        => $userId,
                        'type'           => 'in',
                        'category'       => 'sale',
                        'amount'         => $totalSaleAmount,
                        'motif'          => 'Encaissement vente rapide par scan #' . $movement->id,
                        'is_unjustified' => false,
                    ]);

                    $message = 'Vente et encaissement enregistrés avec succès : ' . $quantity . 'x ' . $product->name . ' (' . number_format($negotiatedPrice, 0, ',', ' ') . ' FCFA /u)';

                } elseif ($request->action_type === 'supply') {
                    // Double vérification de sécurité : s'assurer que le fournisseur choisi livre bien ce produit
                    $supplierId = $request->supplier_id;
                    $isAuthorized = $product->suppliers->contains('id', $supplierId);

                    if (!$isAuthorized) {
                        throw new \Exception('Ce fournisseur n\'est pas habilité à livrer ce produit.');
                    }

                    $oldStock = $product->quantity;
                    $newStock = $oldStock + $quantity;
                    $totalCost = $quantity * $product->purchase_price;

                    $product->update([
                        'quantity' => $newStock,
                        'status' => 'disponible'
                    ]);

                    $movement = Movement::create([
                        'boutique_id' => $boutiqueId,
                        'type'        => 'entree', 
                        'date'        => Carbon::now(),
                        'motif'       => 'Approvisionnement rapide par scan',
                        'user_id'     => $userId,
                    ]);

                    $movement->products()->attach($product->id, ['qty' => $quantity]);

                    StockMovement::create([
                        'boutique_id' => $boutiqueId,
                        'product_id'  => $product->id,
                        'user_id'     => $userId,
                        'type'        => 'entry',
                        'quantity'    => $quantity,
                        'old_stock'   => $oldStock,
                        'new_stock'   => $newStock,
                        'reason'      => 'Approvisionnement rapide par scan',
                    ]);

                    CashMovement::create([
                        'boutique_id'    => $boutiqueId,
                        'movement_id'    => $movement->id,
                        'user_id'        => $userId,
                        'type'           => 'out',
                        'category'       => 'restock',
                        'amount'         => $totalCost,
                        'motif'          => 'Approvisionnement rapide par scan pour : ' . $product->name,
                        'is_unjustified' => false,
                    ]);

                    $message = 'Approvisionnement réussi et caisse mise à jour : +' . $quantity . ' ' . $product->name;
                }
            });

            return redirect()->route('stock.scan.index')->with('success', $message);

        } catch (\Exception $e) {
            return redirect()->route('stock.scan.index')->with('error', $e->getMessage());
        }
    }
}