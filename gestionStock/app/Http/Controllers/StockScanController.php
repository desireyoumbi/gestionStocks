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
        
        $product = Product::where('boutique_id', $boutiqueId)
            ->where('reference', $request->reference)
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
            'action_type' => 'required|in:sell,supply',
            'quantity' => 'required|integer|min:1'
        ]);

        $boutiqueId = Auth::user()->boutique_id;
        $quantity = (int) $request->quantity;
        $userId = Auth::id();

        try {
            $message = '';

            DB::transaction(function () use ($request, $boutiqueId, $quantity, $userId, &$message) {
                // Verrouillage anti-concurrence pour éviter les conflits de stock simultanés
                $product = Product::where('boutique_id', $boutiqueId)
                    ->where('id', $request->product_id)
                    ->lockForUpdate()
                    ->firstOrFail();

                if ($request->action_type === 'sell') {
                    if ($product->quantity < $quantity) {
                        throw new \Exception('Stock insuffisant ! Stock actuel : ' . $product->quantity);
                    }

                    $oldStock = $product->quantity;
                    $newStock = $oldStock - $quantity;
                    $totalSaleAmount = $quantity * $product->selling_price;

                    // 1. Mettre à jour le stock et le statut
                    $product->update([
                        'quantity' => $newStock,
                        'status' => $newStock > 0 ? 'disponible' : 'rupture'
                    ]);

                    // 2. Créer le mouvement de type 'sortie' (vente)
                    $movement = Movement::create([
                        'boutique_id' => $boutiqueId,
                        'type'        => 'sortie', 
                        'date'        => Carbon::now(),
                        'motif'       => 'Vente rapide par scan',
                        'user_id'     => $userId,
                    ]);

                    $movement->products()->attach($product->id, ['qty' => $quantity]);

                    // 3. Traçabilité globale des stocks
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

                    // 4. Mouvement de caisse (Encaissement)
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

                    $message = 'Vente et encaissement enregistrés avec succès : ' . $quantity . 'x ' . $product->name;

                } elseif ($request->action_type === 'supply') {
                    $oldStock = $product->quantity;
                    $newStock = $oldStock + $quantity;
                    $totalCost = $quantity * $product->purchase_price;

                    // 1. Mettre à jour le stock et le statut
                    $product->update([
                        'quantity' => $newStock,
                        'status' => 'disponible'
                    ]);

                    // 2. Créer le mouvement de type 'entree' (approvisionnement)
                    $movement = Movement::create([
                        'boutique_id' => $boutiqueId,
                        'type'        => 'entree', 
                        'date'        => Carbon::now(),
                        'motif'       => 'Approvisionnement rapide par scan',
                        'user_id'     => $userId,
                    ]);

                    $movement->products()->attach($product->id, ['qty' => $quantity]);

                    // 3. Traçabilité globale des stocks
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

                    // 4. Mouvement de caisse (Sortie d'argent pour l'achat)
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