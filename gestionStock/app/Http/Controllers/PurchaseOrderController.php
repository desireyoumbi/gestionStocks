<?php

namespace App\Http\Controllers;

use App\Models\Movement;
use App\Models\Product;
use App\Models\PurchaseOrder;
use App\Models\Supplier;
use App\Models\StockMovement;
use App\Models\CashMovement;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class PurchaseOrderController extends Controller
{
    public function index(Request $request)
    {
        $status = $request->input('status');

        $query = PurchaseOrder::where('boutique_id', Auth::user()->boutique_id)
            ->with(['supplier', 'user']);

        if ($status && in_array($status, ['en_attente', 'recue', 'annulee'])) {
            $query->where('status', $status);
        }

        $purchaseOrders = $query->latest()->paginate(10)->appends(['status' => $status]);

        return view('purchase_orders.index', compact('purchaseOrders', 'status'));
    }

    public function create()
    {
        if (Auth::user()->role === 'vendeur') {
            abort(403, "Vous n'êtes pas autorisé à créer cet élément.");
        }

        $suppliers = Supplier::where('boutique_id', Auth::user()->boutique_id)->get();
        $products = Product::where('boutique_id', Auth::user()->boutique_id)->get();

        return view('purchase_orders.create', compact('suppliers', 'products'));
    }

    public function store(Request $request)
    {
        if (Auth::user()->role === 'vendeur') {
            abort(403, "Vous n'êtes pas autorisé à effectuer cette action.");
        }

        // Sécurisation stricte des règles et des limites anti-débordement
        $validated = $request->validate([
            'supplier_id' => [
                'required',
                Rule::exists('suppliers', 'id')->where('boutique_id', Auth::user()->boutique_id),
            ],
            'products' => 'required|array|min:1',
            'products.*.id' => [
                'required',
                Rule::exists('products', 'id')->where('boutique_id', Auth::user()->boutique_id),
            ],
            'products.*.qty' => 'required|integer|min:1|max:2147483647',
            'products.*.unit_price' => 'required|numeric|min:0|max:999999999999999999.99',
        ], [
            'supplier_id.exists' => 'Le fournisseur sélectionné est invalide.',
            'products.*.id.exists' => 'Un ou plusieurs produits sélectionnés sont invalides.',
            'products.*.qty.max' => 'La quantité d\'un produit est trop élevée.',
            'products.*.unit_price.max' => 'Le prix unitaire d\'un produit est trop élevé.',
        ]);

        DB::transaction(function () use ($validated) {
            $boutiqueId = Auth::user()->boutique_id;

            $purchaseOrder = PurchaseOrder::create([
                'boutique_id' => $boutiqueId,
                'supplier_id' => $validated['supplier_id'],
                'user_id' => Auth::id(),
                'status' => 'en_attente',
            ]);

            foreach ($validated['products'] as $line) {
                $purchaseOrder->products()->attach($line['id'], [
                    'qty' => $line['qty'],
                    'unit_price' => $line['unit_price'],
                ]);
            }
        });

        return redirect()->route('purchase_orders.index')->with('success', 'Commande d\'achat enregistrée avec succès.');
    }

    public function destroy(PurchaseOrder $purchaseOrder)
    {
        if ($purchaseOrder->boutique_id !== Auth::user()->boutique_id) {
            abort(404, "Élément introuvable.");
        }

        if (Auth::user()->role !== 'administrateur') {
            abort(403, "Action non autorisée.");
        }

        if ($purchaseOrder->status === 'recue') {
            return redirect()->back()->with('error', 'Impossible de supprimer une commande déjà reçue et validée.');
        }

        return DB::transaction(function () use ($purchaseOrder) {
            $purchaseOrder->products()->detach();
            $purchaseOrder->delete();

            return redirect()->route('purchase_orders.index')->with('success', 'Commande supprimée avec succès.');
        });
    }

    /**
     * Valide une commande fournisseur : génère le mouvement d'entrée, met à jour les stocks
     * (quantité + statut), trace l'opération dans l'historique, synchronise le prix d'achat,
     * et enregistre la sortie de caisse correspondante.
     */
    public function receive(PurchaseOrder $purchaseOrder)
    {
        if ($purchaseOrder->boutique_id !== Auth::user()->boutique_id) {
            abort(404, "Commande introuvable.");
        }

        if (Auth::user()->role === 'vendeur') {
            abort(403, "Vous n'êtes pas autorisé à valider cette commande.");
        }

        // Utilisation d'un verrouillage de ligne pour éviter les doubles validations simultanées
        return DB::transaction(function () use ($purchaseOrder) {
            $lockedOrder = PurchaseOrder::where('id', $purchaseOrder->id)->lockForUpdate()->first();

            if ($lockedOrder->status !== 'en_attente') {
                return redirect()->back()->with('error', 'Cette commande a déjà été traitée.');
            }

            // Charger les produits liés via la table pivot
            $lockedOrder->load('products');

            // Étape A : Créer le mouvement de stock d'entrée avec l'ID de la commande
            $movement = Movement::create([
                'boutique_id'       => $lockedOrder->boutique_id,
                'purchase_order_id' => $lockedOrder->id,
                'type'              => 'entree',
                'date'              => now(),
                'motif'             => 'Validation commande fournisseur #' . $lockedOrder->id,
                'user_id'           => Auth::id(),
            ]);

            $totalAmount = 0;

            // Étape B : Parcourir les produits de la commande pour alimenter le mouvement et le stock
            foreach ($lockedOrder->products as $product) {
                $qty = $product->pivot->qty;
                $unitPrice = $product->pivot->unit_price ?? 0;

                // Associer le produit au mouvement dans la table pivot des mouvements
                $movement->products()->attach($product->id, [
                    'qty' => $qty,
                ]);

                // Étape C : Mettre à jour le stock réel du produit avec verrouillage pour la concurrence
                $productToUpdate = Product::where('id', $product->id)->lockForUpdate()->first();

                if ($productToUpdate) {
                    $oldStock = $productToUpdate->quantity;
                    $newStock = $oldStock + $qty;

                    // Quantité, statut recalculé, et prix d'achat synchronisé avec ce qui a été payé
                    $productToUpdate->update([
                        'quantity' => $newStock,
                        'status' => $newStock > 0 ? 'disponible' : 'rupture',
                        'purchase_price' => $unitPrice,
                    ]);

                    // Traçabilité dans l'historique global des stocks, comme pour tout autre type d'entrée
                    StockMovement::create([
                        'boutique_id' => $lockedOrder->boutique_id,
                        'product_id'  => $productToUpdate->id,
                        'user_id'     => Auth::id(),
                        'type'        => 'entry',
                        'quantity'    => $qty,
                        'old_stock'   => $oldStock,
                        'new_stock'   => $newStock,
                        'reason'      => 'Réception commande fournisseur #' . $lockedOrder->id,
                    ]);

                    $totalAmount += $qty * $unitPrice;
                }
            }

            // Étape D : Enregistrer la sortie de caisse correspondant à cet achat,
            // liée au mouvement via movement_id pour rester cohérent avec SupplyController.
            if ($totalAmount > 0) {
                CashMovement::create([
                    'boutique_id' => $lockedOrder->boutique_id,
                    'movement_id' => $movement->id,
                    'user_id'     => Auth::id(),
                    'type'        => 'out',
                    'category'    => 'restock',
                    'amount'      => $totalAmount,
                    'motif'       => 'Réception commande fournisseur #' . $lockedOrder->id,
                    'is_unjustified' => false,
                ]);
            }

            // Étape E : Mettre à jour le statut de la commande
            $lockedOrder->update([
                'status'     => 'recue',
                'updated_by' => Auth::id(),
            ]);

            return redirect()->back()->with('success', 'Commande validée avec succès : le stock a été mis à jour et le mouvement d\'entrée a été généré.');
        });
    }
}