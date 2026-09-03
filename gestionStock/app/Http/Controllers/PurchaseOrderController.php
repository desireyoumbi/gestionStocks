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
use Illuminate\Validation\ValidationException;

class PurchaseOrderController extends Controller
{
    /**
     * Liste des commandes fournisseurs.
     */
    public function index(Request $request)
    {
        $status = $request->input('status');

        $query = PurchaseOrder::where(
            'boutique_id',
            Auth::user()->boutique_id
        )->with([
            'supplier',
            'user'
        ]);

        if ($status && in_array($status, [
            'en_attente',
            'recue',
            'annulee'
        ])) {
            $query->where('status', $status);
        }

        $purchaseOrders = $query
            ->latest()
            ->paginate(10)
            ->appends([
                'status' => $status
            ]);

        return view(
            'purchase_orders.index',
            compact(
                'purchaseOrders',
                'status'
            )
        );
    }


    /**
     * Formulaire de création d'une commande fournisseur.
     *
     * Chaque fournisseur est chargé avec les produits
     * qui lui sont associés dans product_supplier.
     */
    public function create()
    {
        if (Auth::user()->role === 'vendeur') {
            abort(
                403,
                "Vous n'êtes pas autorisé à créer cet élément."
            );
        }

        $suppliers = Supplier::where(
            'boutique_id',
            Auth::user()->boutique_id
        )
        ->with([
            'products'
        ])
        ->get();

        return view(
            'purchase_orders.create',
            compact(
                'suppliers'
            )
        );
    }


    /**
     * Enregistre une nouvelle commande fournisseur.
     *
     * Règle importante :
     * un produit commandé doit obligatoirement appartenir
     * au fournisseur sélectionné.
     */
    public function store(Request $request)
    {
        if (Auth::user()->role === 'vendeur') {
            abort(
                403,
                "Vous n'êtes pas autorisé à effectuer cette action."
            );
        }

        $boutiqueId = Auth::user()->boutique_id;

        $validated = $request->validate([

            'supplier_id' => [
                'required',

                Rule::exists('suppliers', 'id')
                    ->where(
                        'boutique_id',
                        $boutiqueId
                    ),
            ],

            'products' => [
                'required',
                'array',
                'min:1',
            ],

            'products.*.id' => [
                'required',

                Rule::exists('products', 'id')
                    ->where(
                        'boutique_id',
                        $boutiqueId
                    ),
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

            'supplier_id.required' =>
                'Veuillez sélectionner un fournisseur.',

            'supplier_id.exists' =>
                'Le fournisseur sélectionné est invalide.',

            'products.required' =>
                'Veuillez ajouter au moins un produit.',

            'products.min' =>
                'Veuillez ajouter au moins un produit.',

            'products.*.id.required' =>
                'Veuillez sélectionner un produit.',

            'products.*.id.exists' =>
                'Un ou plusieurs produits sélectionnés sont invalides.',

            'products.*.qty.required' =>
                'La quantité est obligatoire.',

            'products.*.qty.min' =>
                'La quantité doit être au minimum de 1.',

            'products.*.qty.max' =>
                'La quantité d\'un produit est trop élevée.',

            'products.*.unit_price.required' =>
                'Le prix unitaire est obligatoire.',

            'products.*.unit_price.min' =>
                'Le prix unitaire ne peut pas être négatif.',

            'products.*.unit_price.max' =>
                'Le prix unitaire d\'un produit est trop élevé.',
        ]);


        /*
         * Récupération du fournisseur avec ses produits.
         */
        $supplier = Supplier::where(
            'id',
            $validated['supplier_id']
        )
        ->where(
            'boutique_id',
            $boutiqueId
        )
        ->with('products')
        ->firstOrFail();


        /*
         * Liste des produits réellement fournis
         * par ce fournisseur.
         */
        $allowedProductIds = $supplier->products
            ->pluck('id')
            ->map(fn ($id) => (int) $id)
            ->toArray();


        /*
         * Vérification des produits.
         *
         * Même si quelqu'un modifie le HTML ou le JavaScript,
         * il ne pourra pas commander un produit qui n'appartient
         * pas à ce fournisseur.
         */
        $selectedProductIds = [];

        foreach ($validated['products'] as $line) {

            $productId = (int) $line['id'];

            /*
             * Vérification fournisseur -> produit.
             */
            if (!in_array(
                $productId,
                $allowedProductIds,
                true
            )) {

                throw ValidationException::withMessages([
                    'products' =>
                        'Le produit sélectionné n\'est pas fourni par le fournisseur choisi.'
                ]);
            }


            /*
             * Empêcher le même produit d'apparaître
             * plusieurs fois dans la commande.
             */
            if (in_array(
                $productId,
                $selectedProductIds,
                true
            )) {

                throw ValidationException::withMessages([
                    'products' =>
                        'Un même produit ne peut pas apparaître plusieurs fois dans une commande.'
                ]);
            }

            $selectedProductIds[] = $productId;
        }


        /*
         * Création de la commande.
         */
        DB::transaction(function () use (
            $validated,
            $boutiqueId
        ) {

            $purchaseOrder = PurchaseOrder::create([
                'boutique_id' => $boutiqueId,

                'supplier_id' =>
                    $validated['supplier_id'],

                'user_id' =>
                    Auth::id(),

                'status' =>
                    'en_attente',
            ]);


            /*
             * Ajout des produits à la commande.
             */
            foreach ($validated['products'] as $line) {

                $purchaseOrder->products()->attach(
                    $line['id'],
                    [
                        'qty' =>
                            $line['qty'],

                        'unit_price' =>
                            $line['unit_price'],
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
     */
    public function show($id)
    {
        $boutiqueId = Auth::user()->boutique_id;

        /*
         * On récupère toujours le PurchaseOrder.
         */
        $purchaseOrder = PurchaseOrder::with([
            'supplier',
            'user',
            'products',
            'updatedBy',
        ])
        ->where(
            'boutique_id',
            $boutiqueId
        )
        ->findOrFail($id);


        /*
         * Par défaut aucune réception.
         */
        $supply = null;


        /*
         * Si la commande est reçue,
         * on récupère le mouvement d'entrée associé.
         */
        if ($purchaseOrder->status === 'recue') {

            $supply = Movement::with([
                'products',
                'user',
            ])
            ->where(
                'boutique_id',
                $boutiqueId
            )
            ->where(
                'purchase_order_id',
                $purchaseOrder->id
            )
            ->where(
                'type',
                'entree'
            )
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
     * Formulaire de modification d'une commande.
     */
    public function edit(PurchaseOrder $purchaseOrder)
    {
        /*
         * Vérification boutique.
         */
        if (
            $purchaseOrder->boutique_id
            !== Auth::user()->boutique_id
        ) {
            abort(
                404,
                "Commande introuvable."
            );
        }


        /*
         * Les vendeurs ne peuvent pas modifier.
         */
        if (Auth::user()->role === 'vendeur') {
            abort(
                403,
                "Vous n'êtes pas autorisé à modifier cette commande."
            );
        }


        /*
         * Seules les commandes en attente
         * peuvent être modifiées.
         */
        if ($purchaseOrder->status !== 'en_attente') {

            return redirect()
                ->route(
                    'purchase_orders.show',
                    $purchaseOrder
                )
                ->with(
                    'error',
                    'Seules les commandes en attente peuvent être modifiées.'
                );
        }


        /*
         * Chargement des relations de la commande.
         */
        $purchaseOrder->load([
            'products',
            'supplier',
        ]);


        /*
         * Chargement de tous les fournisseurs
         * avec leurs produits.
         *
         * Cela permettra à edit.blade.php
         * de filtrer les produits selon le fournisseur.
         */
        $suppliers = Supplier::where(
            'boutique_id',
            Auth::user()->boutique_id
        )
        ->with([
            'products'
        ])
        ->get();


        return view(
            'purchase_orders.edit',
            compact(
                'purchaseOrder',
                'suppliers'
            )
        );
    }


    /**
     * Mise à jour d'une commande fournisseur.
     *
     * Même sécurité que store() :
     * le produit doit appartenir au fournisseur choisi.
     */
    public function update(
        Request $request,
        PurchaseOrder $purchaseOrder
    ) {

        /*
         * Vérification boutique.
         */
        if (
            $purchaseOrder->boutique_id
            !== Auth::user()->boutique_id
        ) {
            abort(
                404,
                "Commande introuvable."
            );
        }


        /*
         * Les vendeurs ne peuvent pas modifier.
         */
        if (Auth::user()->role === 'vendeur') {
            abort(
                403,
                "Vous n'êtes pas autorisé à modifier cette commande."
            );
        }


        /*
         * Une commande reçue ou annulée
         * ne peut plus être modifiée.
         */
        if ($purchaseOrder->status !== 'en_attente') {

            return redirect()
                ->route(
                    'purchase_orders.show',
                    $purchaseOrder
                )
                ->with(
                    'error',
                    'Seules les commandes en attente peuvent être modifiées.'
                );
        }


        $boutiqueId = Auth::user()->boutique_id;


        /*
         * Validation des données.
         */
        $validated = $request->validate([

            'supplier_id' => [
                'required',

                Rule::exists('suppliers', 'id')
                    ->where(
                        'boutique_id',
                        $boutiqueId
                    ),
            ],

            'products' => [
                'required',
                'array',
                'min:1',
            ],

            'products.*.id' => [
                'required',

                Rule::exists('products', 'id')
                    ->where(
                        'boutique_id',
                        $boutiqueId
                    ),
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

            'supplier_id.required' =>
                'Veuillez sélectionner un fournisseur.',

            'supplier_id.exists' =>
                'Le fournisseur sélectionné est invalide.',

            'products.required' =>
                'Veuillez ajouter au moins un produit.',

            'products.min' =>
                'Veuillez ajouter au moins un produit.',

            'products.*.id.required' =>
                'Veuillez sélectionner un produit.',

            'products.*.id.exists' =>
                'Un ou plusieurs produits sélectionnés sont invalides.',

            'products.*.qty.required' =>
                'La quantité est obligatoire.',

            'products.*.qty.min' =>
                'La quantité doit être au minimum de 1.',

            'products.*.qty.max' =>
                'La quantité d\'un produit est trop élevée.',

            'products.*.unit_price.required' =>
                'Le prix unitaire est obligatoire.',

            'products.*.unit_price.min' =>
                'Le prix unitaire ne peut pas être négatif.',

            'products.*.unit_price.max' =>
                'Le prix unitaire d\'un produit est trop élevé.',
        ]);


        /*
         * Récupération du fournisseur avec ses produits.
         */
        $supplier = Supplier::where(
            'id',
            $validated['supplier_id']
        )
        ->where(
            'boutique_id',
            $boutiqueId
        )
        ->with('products')
        ->firstOrFail();


        /*
         * Produits autorisés pour ce fournisseur.
         */
        $allowedProductIds = $supplier->products
            ->pluck('id')
            ->map(fn ($id) => (int) $id)
            ->toArray();


        /*
         * Vérification des produits.
         */
        $selectedProductIds = [];

        foreach ($validated['products'] as $line) {

            $productId = (int) $line['id'];


            /*
             * Vérification fournisseur -> produit.
             */
            if (!in_array(
                $productId,
                $allowedProductIds,
                true
            )) {

                throw ValidationException::withMessages([
                    'products' =>
                        'Le produit sélectionné n\'est pas fourni par le fournisseur choisi.'
                ]);
            }


            /*
             * Pas de doublon.
             */
            if (in_array(
                $productId,
                $selectedProductIds,
                true
            )) {

                throw ValidationException::withMessages([
                    'products' =>
                        'Un même produit ne peut pas apparaître plusieurs fois dans une commande.'
                ]);
            }

            $selectedProductIds[] = $productId;
        }


        /*
         * Mise à jour dans une transaction.
         */
        DB::transaction(function () use (
            $validated,
            $purchaseOrder
        ) {

            $purchaseOrder->update([
                'supplier_id' =>
                    $validated['supplier_id'],

                'updated_by' =>
                    Auth::id(),
            ]);


            /*
             * Suppression des anciennes lignes.
             */
            $purchaseOrder->products()->detach();


            /*
             * Création des nouvelles lignes.
             */
            foreach ($validated['products'] as $line) {

                $purchaseOrder->products()->attach(
                    $line['id'],
                    [
                        'qty' =>
                            $line['qty'],

                        'unit_price' =>
                            $line['unit_price'],
                    ]
                );
            }
        });


        return redirect()
            ->route(
                'purchase_orders.show',
                $purchaseOrder
            )
            ->with(
                'success',
                'Commande modifiée avec succès.'
            );
    }


    /**
     * Valide une commande fournisseur.
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
        /*
         * Vérification boutique.
         */
        if (
            $purchaseOrder->boutique_id
            !== Auth::user()->boutique_id
        ) {
            abort(
                404,
                "Commande introuvable."
            );
        }


        /*
         * Les vendeurs ne peuvent pas réceptionner.
         */
        if (Auth::user()->role === 'vendeur') {
            abort(
                403,
                "Vous n'êtes pas autorisé à valider cette commande."
            );
        }


        return DB::transaction(
            function () use ($purchaseOrder) {

                /*
                 * Verrouillage de la commande.
                 */
                $lockedOrder = PurchaseOrder::where(
                    'id',
                    $purchaseOrder->id
                )
                ->where(
                    'boutique_id',
                    Auth::user()->boutique_id
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
                 * Une commande déjà traitée
                 * ne peut plus être validée.
                 */
                if (
                    $lockedOrder->status
                    !== 'en_attente'
                ) {

                    return redirect()
                        ->back()
                        ->with(
                            'error',
                            'Cette commande a déjà été traitée.'
                        );
                }


                /*
                 * Chargement des produits.
                 */
                $lockedOrder->load('products');


                /*
                 * Création du mouvement d'entrée.
                 */
                $movement = Movement::create([

                    'boutique_id' =>
                        $lockedOrder->boutique_id,

                    'purchase_order_id' =>
                        $lockedOrder->id,

                    'type' =>
                        'entree',

                    'date' =>
                        now(),

                    'motif' =>
                        'Validation commande fournisseur #'
                        . $lockedOrder->id,

                    'user_id' =>
                        Auth::id(),
                ]);


                $totalAmount = 0;


                /*
                 * Traitement des produits.
                 */
                foreach (
                    $lockedOrder->products
                    as $product
                ) {

                    $qty =
                        $product->pivot->qty;

                    $unitPrice =
                        $product->pivot->unit_price ?? 0;


                    /*
                     * Association produit -> mouvement.
                     */
                    $movement->products()->attach(
                        $product->id,
                        [
                            'qty' =>
                                $qty,
                        ]
                    );


                    /*
                     * Verrouillage du produit.
                     */
                    $productToUpdate = Product::where(
                        'id',
                        $product->id
                    )
                    ->where(
                        'boutique_id',
                        $lockedOrder->boutique_id
                    )
                    ->lockForUpdate()
                    ->first();


                    if ($productToUpdate) {

                        $oldStock =
                            $productToUpdate->quantity;

                        $newStock =
                            $oldStock + $qty;


                        /*
                         * Mise à jour du produit.
                         */
                        $productToUpdate->update([

                            'quantity' =>
                                $newStock,

                            'status' =>
                                $newStock > 0
                                    ? 'disponible'
                                    : 'rupture',

                            'purchase_price' =>
                                $unitPrice,
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

                            'type' =>
                                'entry',

                            'quantity' =>
                                $qty,

                            'old_stock' =>
                                $oldStock,

                            'new_stock' =>
                                $newStock,

                            'reason' =>
                                'Réception commande fournisseur #'
                                . $lockedOrder->id,
                        ]);


                        /*
                         * Calcul du coût total.
                         */
                        $totalAmount +=
                            $qty * $unitPrice;
                    }
                }


                /*
                 * Sortie de caisse.
                 */
                if ($totalAmount > 0) {

                    CashMovement::create([

                        'boutique_id' =>
                            $lockedOrder->boutique_id,

                        'movement_id' =>
                            $movement->id,

                        'user_id' =>
                            Auth::id(),

                        'type' =>
                            'out',

                        'category' =>
                            'restock',

                        'amount' =>
                            $totalAmount,

                        'motif' =>
                            'Réception commande fournisseur #'
                            . $lockedOrder->id,

                        'is_unjustified' =>
                            false,
                    ]);
                }


                /*
                 * La commande devient reçue.
                 */
                $lockedOrder->update([

                    'status' =>
                        'recue',

                    'updated_by' =>
                        Auth::id(),
                ]);


                return redirect()
                    ->back()
                    ->with(
                        'success',
                        'Commande validée avec succès : le stock a été mis à jour et le mouvement d\'entrée a été généré.'
                    );
            }
        );
    }


    /**
     * Annule une commande fournisseur.
     *
     * Une commande annulée reste dans l'historique.
     * Elle n'est donc pas supprimée.
     */
    public function cancel(PurchaseOrder $purchaseOrder)
    {
        /*
         * Vérification boutique.
         */
        if (
            $purchaseOrder->boutique_id
            !== Auth::user()->boutique_id
        ) {
            abort(
                404,
                "Commande introuvable."
            );
        }


        /*
         * Les vendeurs ne peuvent pas annuler.
         */
        if (Auth::user()->role === 'vendeur') {
            abort(
                403,
                "Vous n'êtes pas autorisé à annuler cette commande."
            );
        }


        /*
         * Seules les commandes en attente
         * peuvent être annulées.
         */
        if (
            $purchaseOrder->status
            !== 'en_attente'
        ) {

            return redirect()
                ->back()
                ->with(
                    'error',
                    'Seules les commandes en attente peuvent être annulées.'
                );
        }


        /*
         * Annulation.
         */
        $purchaseOrder->update([

            'status' =>
                'annulee',

            'updated_by' =>
                Auth::id(),
        ]);


        return redirect()
            ->route(
                'purchase_orders.index'
            )
            ->with(
                'success',
                'Commande annulée avec succès.'
            );
    }
}