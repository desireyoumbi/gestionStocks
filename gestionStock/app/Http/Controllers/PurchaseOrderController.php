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
            ->with([
                'supplier',
                'user'
            ]);

        if ($status && in_array($status, ['en_attente', 'recue', 'annulee'])) {
            $query->where('status', $status);
        }

        $purchaseOrders = $query
            ->latest()
            ->paginate(10)
            ->appends(['status' => $status]);

        return view('purchase_orders.index', compact(
            'purchaseOrders',
            'status'
        ));
    }

    public function create()
    {
        if (Auth::user()->role === 'vendeur') {
            abort(403, "Vous n'êtes pas autorisé à créer cet élément.");
        }

        $suppliers = Supplier::where(
            'boutique_id',
            Auth::user()->boutique_id
        )->get();

        $products = Product::where(
            'boutique_id',
            Auth::user()->boutique_id
        )->get();

        return view('purchase_orders.create', compact(
            'suppliers',
            'products'
        ));
    }

    public function store(Request $request)
    {
        if (Auth::user()->role === 'vendeur') {
            abort(403, "Vous n'êtes pas autorisé à effectuer cette action.");
        }

        $validated = $request->validate([
            'supplier_id' => [
                'required',
                Rule::exists('suppliers', 'id')
                    ->where('boutique_id', Auth::user()->boutique_id),
            ],

            'products' => 'required|array|min:1',

            'products.*.id' => [
                'required',
                Rule::exists('products', 'id')
                    ->where('boutique_id', Auth::user()->boutique_id),
            ],

            'products.*.qty' => [
                'required',
                'integer',
                'min:1',
                'max:2147483647',
            ],

            'products.*.unit_price' => [
                'required',
                'numeric',
                'min:0',
                'max:999999999999999999.99',
            ],
        ], [
            'supplier_id.exists' =>
                'Le fournisseur sélectionné est invalide.',

            'products.*.id.exists' =>
                'Un ou plusieurs produits sélectionnés sont invalides.',

            'products.*.qty.max' =>
                'La quantité d\'un produit est trop élevée.',

            'products.*.unit_price.max' =>
                'Le prix unitaire d\'un produit est trop élevé.',
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

                $purchaseOrder->products()->attach(
                    $line['id'],
                    [
                        'qty' => $line['qty'],
                        'unit_price' => $line['unit_price'],
                    ]
                );
            }
        });

        return redirect()
            ->route('purchase_orders.index')
            ->with(
                'success',
                'Commande d\'achat enregistrée avec succès.'
            );
    }

    /**
     * Affiche les détails d'une commande fournisseur.
     *
     * Une commande peut être :
     * - en_attente : commande future
     * - recue      : commande réceptionnée
     * - annulee    : commande annulée
     *
     * Lorsqu'elle est reçue, le mouvement d'entrée
     * associé à cette commande est également récupéré.
     */
    public function show($id)
    {
        $boutiqueId = Auth::user()->boutique_id;

        /*
         * On récupère toujours le PurchaseOrder.
         *
         * Le bouton "Voir détail" de index.blade.php
         * envoie l'identifiant de la commande fournisseur.
         */
        $purchaseOrder = PurchaseOrder::with([
            'supplier',
            'user',
            'products',
            'updatedBy',
        ])
        ->where('boutique_id', $boutiqueId)
        ->findOrFail($id);

        /*
         * Par défaut, aucune réception n'est associée.
         */
        $supply = null;

        /*
         * Si la commande a été reçue,
         * on récupère le mouvement d'entrée créé
         * lors de la validation de la commande.
         */
        if ($purchaseOrder->status === 'recue') {

            $supply = Movement::with([
                'products',
                'user',
            ])
            ->where('boutique_id', $boutiqueId)
            ->where(
                'purchase_order_id',
                $purchaseOrder->id
            )
            ->where('type', 'entree')
            ->latest()
            ->first();
        }

        return view(
            'purchase_orders.show',
            compact(
                'purchaseOrder',
                'supply'
            )
        );
    }

    /**
     * Valide une commande fournisseur :
     *
     * - crée le mouvement d'entrée
     * - met à jour le stock
     * - met à jour le prix d'achat
     * - crée l'historique du stock
     * - crée la sortie de caisse
     * - passe la commande à "recue"
     */
    public function receive(PurchaseOrder $purchaseOrder)
    {
        if ($purchaseOrder->boutique_id !== Auth::user()->boutique_id) {
            abort(404, "Commande introuvable.");
        }

        if (Auth::user()->role === 'vendeur') {
            abort(
                403,
                "Vous n'êtes pas autorisé à valider cette commande."
            );
        }

        return DB::transaction(function () use ($purchaseOrder) {

            /*
             * Verrouillage de la commande afin d'éviter
             * deux validations simultanées.
             */
            $lockedOrder = PurchaseOrder::where(
                'id',
                $purchaseOrder->id
            )
            ->lockForUpdate()
            ->first();

            if (!$lockedOrder) {
                return redirect()
                    ->back()
                    ->with(
                        'error',
                        'Commande introuvable.'
                    );
            }

            /*
             * Une commande déjà reçue ou annulée
             * ne peut plus être validée.
             */
            if ($lockedOrder->status !== 'en_attente') {

                return redirect()
                    ->back()
                    ->with(
                        'error',
                        'Cette commande a déjà été traitée.'
                    );
            }

            /*
             * Chargement des produits liés à la commande.
             */
            $lockedOrder->load('products');

            /*
             * Création du mouvement d'entrée.
             *
             * IMPORTANT :
             * purchase_order_id permet de retrouver
             * cette réception depuis la commande.
             */
            $movement = Movement::create([
                'boutique_id' => $lockedOrder->boutique_id,
                'purchase_order_id' => $lockedOrder->id,
                'type' => 'entree',
                'date' => now(),
                'motif' =>
                    'Validation commande fournisseur #' .
                    $lockedOrder->id,
                'user_id' => Auth::id(),
            ]);

            $totalAmount = 0;

            /*
             * Traitement de chaque produit.
             */
            foreach ($lockedOrder->products as $product) {

                $qty = $product->pivot->qty;

                $unitPrice = $product->pivot->unit_price ?? 0;

                /*
                 * Association du produit au mouvement.
                 */
                $movement->products()->attach(
                    $product->id,
                    [
                        'qty' => $qty,
                    ]
                );

                /*
                 * Verrouillage du produit pour éviter
                 * les problèmes de concurrence sur le stock.
                 */
                $productToUpdate = Product::where(
                    'id',
                    $product->id
                )
                ->lockForUpdate()
                ->first();

                if ($productToUpdate) {

                    $oldStock = $productToUpdate->quantity;

                    $newStock = $oldStock + $qty;

                    /*
                     * Mise à jour du produit.
                     */
                    $productToUpdate->update([
                        'quantity' => $newStock,

                        'status' =>
                            $newStock > 0
                                ? 'disponible'
                                : 'rupture',

                        'purchase_price' => $unitPrice,
                    ]);

                    /*
                     * Historique global du stock.
                     */
                    StockMovement::create([
                        'boutique_id' =>
                            $lockedOrder->boutique_id,

                        'product_id' =>
                            $productToUpdate->id,

                        'user_id' =>
                            Auth::id(),

                        'type' => 'entry',

                        'quantity' => $qty,

                        'old_stock' => $oldStock,

                        'new_stock' => $newStock,

                        'reason' =>
                            'Réception commande fournisseur #' .
                            $lockedOrder->id,
                    ]);

                    /*
                     * Calcul du montant total de la commande.
                     */
                    $totalAmount +=
                        $qty * $unitPrice;
                }
            }

            /*
             * Enregistrement de la sortie de caisse.
             */
            if ($totalAmount > 0) {

                CashMovement::create([
                    'boutique_id' =>
                        $lockedOrder->boutique_id,

                    'movement_id' =>
                        $movement->id,

                    'user_id' =>
                        Auth::id(),

                    'type' => 'out',

                    'category' => 'restock',

                    'amount' =>
                        $totalAmount,

                    'motif' =>
                        'Réception commande fournisseur #' .
                        $lockedOrder->id,

                    'is_unjustified' => false,
                ]);
            }

            /*
             * La commande devient reçue.
             *
             * updated_by permet de savoir
             * qui a effectué la réception.
             */
            $lockedOrder->update([
                'status' => 'recue',
                'updated_by' => Auth::id(),
            ]);

            return redirect()
                ->back()
                ->with(
                    'success',
                    'Commande validée avec succès : le stock a été mis à jour et le mouvement d\'entrée a été généré.'
                );
        });
    }

    public function destroy(PurchaseOrder $purchaseOrder)
    {
        if ($purchaseOrder->boutique_id !== Auth::user()->boutique_id) {
            abort(404, "Élément introuvable.");
        }

        if (Auth::user()->role !== 'administrateur') {
            abort(403, "Action non autorisée.");
        }

        /*
         * Une commande déjà reçue ne peut pas être supprimée.
         */
        if ($purchaseOrder->status === 'recue') {

            return redirect()
                ->back()
                ->with(
                    'error',
                    'Impossible de supprimer une commande déjà reçue et validée.'
                );
        }

        return DB::transaction(function () use ($purchaseOrder) {

            /*
             * Suppression des lignes de commande.
             */
            $purchaseOrder->products()->detach();

            /*
             * Suppression de la commande.
             */
            $purchaseOrder->delete();

            return redirect()
                ->route('purchase_orders.index')
                ->with(
                    'success',
                    'Commande supprimée avec succès.'
                );
        });
    }

    public function cancel(PurchaseOrder $purchaseOrder)
{
    // Vérifier que la commande appartient à la boutique de l'utilisateur
    if ($purchaseOrder->boutique_id !== Auth::user()->boutique_id) {
        abort(404, "Commande introuvable.");
    }

    // Les vendeurs ne peuvent pas annuler une commande
    if (Auth::user()->role === 'vendeur') {
        abort(403, "Vous n'êtes pas autorisé à annuler cette commande.");
    }

    // Seules les commandes en attente peuvent être annulées
    if ($purchaseOrder->status !== 'en_attente') {
        return redirect()->back()->with(
            'error',
            'Seules les commandes en attente peuvent être annulées.'
        );
    }

    $purchaseOrder->update([
        'status' => 'annulee',
        'updated_by' => Auth::id(),
    ]);

    return redirect()
        ->route('purchase_orders.index')
        ->with('success', 'Commande annulée avec succès.');
}
}