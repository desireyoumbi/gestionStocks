<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Movement;
use App\Models\StockMovement;
use App\Models\CashMovement;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;
use Barryvdh\DomPDF\Facade\Pdf;

class SaleController extends Controller
{
    /**
     * Affiche la liste des ventes effectuées.
     */
    public function index(Request $request)
    {
        $boutiqueId = Auth::user()->boutique_id;

        $query = Movement::with(['products', 'user'])
            ->where('boutique_id', $boutiqueId)
            ->where('type', 'sortie');

        if ($request->filled('search')) {
            $search = $request->search;

            $query->where(function ($q) use ($search) {
                $q->where('motif', 'like', "%{$search}%")
                    ->orWhereHas('products', function ($subQ) use ($search) {
                        $subQ->where('name', 'like', "%{$search}%");
                    });
            });
        }

        $sales = $query->latest()->paginate(15);

        $groupedSales = collect($sales->items())->groupBy(function ($sale) {
            return $sale->created_at->format('Y-m-d');
        });

        $baseVentesQuery = Movement::where('boutique_id', $boutiqueId)
            ->where('type', 'sortie')
            ->whereNull('annule_at');

        $totalVentesCount = (clone $baseVentesQuery)->count();

        $totalArticlesVendus = DB::table('movement_products')
            ->join(
                'movements',
                'movement_products.movement_id',
                '=',
                'movements.id'
            )
            ->where('movements.boutique_id', $boutiqueId)
            ->where('movements.type', 'sortie')
            ->whereNull('movements.annule_at')
            ->sum(DB::raw('ABS(qty)')) ?? 0;

        $chiffreAffairesTotal = DB::table('movement_products')
            ->join(
                'movements',
                'movement_products.movement_id',
                '=',
                'movements.id'
            )
            ->where('movements.boutique_id', $boutiqueId)
            ->where('movements.type', 'sortie')
            ->whereNull('movements.annule_at')
            ->sum(
                DB::raw(
                    'ABS(movement_products.qty) * movement_products.unit_price'
                )
            ) ?? 0;

        $panierMoyen = $totalVentesCount > 0
            ? number_format(
                $chiffreAffairesTotal / $totalVentesCount,
                0,
                ',',
                ' '
            ) . ' FCFA'
            : '0 FCFA';

        $produitPhareData = DB::table('movement_products')
            ->join(
                'movements',
                'movement_products.movement_id',
                '=',
                'movements.id'
            )
            ->join(
                'products',
                'movement_products.product_id',
                '=',
                'products.id'
            )
            ->where('movements.boutique_id', $boutiqueId)
            ->where('movements.type', 'sortie')
            ->whereNull('movements.annule_at')
            ->select(
                'products.name',
                DB::raw('SUM(ABS(movement_products.qty)) as total_qty')
            )
            ->groupBy('products.id', 'products.name')
            ->orderByDesc('total_qty')
            ->first();

        $produitPhare = $produitPhareData
            ? $produitPhareData->name
            : 'Aucun';

        return view('sales.index', compact(
            'sales',
            'groupedSales',
            'panierMoyen',
            'totalArticlesVendus',
            'produitPhare'
        ));
    }

    /**
     * Affiche le formulaire de création d'une vente.
     */
    public function create()
    {
        $boutiqueId = Auth::user()->boutique_id;

        $products = Product::where('boutique_id', $boutiqueId)->get();

        return view('sales.create', compact('products'));
    }

    /**
     * Enregistre la vente, met à jour le stock,
     * crédite la caisse et redirige vers l'index.
     *
     * Le prix de vente est saisi par l'utilisateur pour chaque produit
     * et ne peut jamais être inférieur au prix d'achat.
     */
    public function store(Request $request)
    {
        $boutiqueId = Auth::user()->boutique_id;

        $request->validate([
            'motif' => 'nullable|string|max:255',

            'items' => 'required|array|min:1',

            'items.*.product_id' => [
                'required',
                Rule::exists('products', 'id')
                    ->where('boutique_id', $boutiqueId),
            ],

            'items.*.quantity' => [
                'required',
                'integer',
                'min:1',
                'max:2147483647',
            ],

            'items.*.unit_price' => [
                'required',
                'numeric',
                'min:0',
                'max:999999999999999999.99',

                function ($attribute, $value, $fail) use ($request, $boutiqueId) {

                    preg_match(
                        '/items\.(\d+)\.unit_price/',
                        $attribute,
                        $matches
                    );

                    $index = $matches[1] ?? null;

                    if ($index === null) {
                        return;
                    }

                    $productId = $request->input(
                        "items.$index.product_id"
                    );

                    if (!$productId) {
                        return;
                    }

                    $product = Product::where('id', $productId)
                        ->where('boutique_id', $boutiqueId)
                        ->first();

                    if (
                        $product &&
                        $value < $product->purchase_price
                    ) {
                        $fail(
                            "Le prix de vente de « {$product->name} » " .
                            "ne peut pas être inférieur au prix d'achat (" .
                            number_format(
                                $product->purchase_price,
                                0,
                                ',',
                                ' '
                            ) .
                            " FCFA)."
                        );
                    }
                },
            ],
        ], [
            'items.*.product_id.exists' =>
                'Un ou plusieurs produits sélectionnés sont invalides.',

            'items.*.quantity.max' =>
                'La quantité demandée est trop élevée.',

            'items.*.unit_price.required' =>
                'Le prix de vente est obligatoire pour chaque produit.',

            'items.*.unit_price.min' =>
                'Le prix de vente ne peut pas être négatif.',
        ]);

        try {

            DB::transaction(function () use ($request, $boutiqueId) {

                $user = Auth::user();

                $totalSaleAmount = 0;

                $movement = Movement::create([
                    'boutique_id' => $boutiqueId,
                    'type' => 'sortie',
                    'date' => now(),
                    'user_id' => $user->id,
                    'motif' => $request->motif ?? 'Vente',
                ]);

                foreach ($request->items as $item) {

                    $product = Product::where(
                        'boutique_id',
                        $boutiqueId
                    )
                        ->where('id', $item['product_id'])
                        ->lockForUpdate()
                        ->firstOrFail();

                    if ($product->quantity < $item['quantity']) {
                        throw new \Exception(
                            "Stock insuffisant pour le produit : " .
                            "{$product->name}. Stock actuel : " .
                            "{$product->quantity}"
                        );
                    }

                    $oldStock = $product->quantity;

                    $quantitySold = $item['quantity'];

                    $newStock = $oldStock - $quantitySold;

                    $unitPrice = (float) $item['unit_price'];

                    $lineTotal = $quantitySold * $unitPrice;

                    $totalSaleAmount += $lineTotal;

                    $movement->products()->attach(
                        $product->id,
                        [
                            'qty' => $quantitySold,
                            'unit_price' => $unitPrice,
                        ]
                    );

                    $product->update([
                        'quantity' => $newStock,
                        'status' => $newStock > 0
                            ? 'disponible'
                            : 'rupture',
                    ]);

                    StockMovement::create([
                        'boutique_id' => $boutiqueId,
                        'product_id' => $product->id,
                        'user_id' => $user->id,
                        'type' => 'sale',
                        'quantity' => $quantitySold,
                        'old_stock' => $oldStock,
                        'new_stock' => $newStock,
                        'reason' =>
                            $request->motif ??
                            'Vente de produits',
                    ]);
                }

                CashMovement::create([
                    'boutique_id' => $boutiqueId,
                    'movement_id' => $movement->id,
                    'user_id' => $user->id,
                    'type' => 'in',
                    'category' => 'sale',
                    'amount' => $totalSaleAmount,
                    'motif' =>
                        $request->motif ??
                        'Encaissement vente #' . $movement->id,
                    'is_unjustified' => false,
                ]);
            });

        } catch (\Exception $e) {

            return back()
                ->withInput()
                ->withErrors([
                    'quantity' => $e->getMessage()
                ]);
        }

        return redirect()
            ->route('sales.index')
            ->with(
                'success',
                'Vente et encaissement enregistrés avec succès.'
            );
    }

    /**
     * Annule une vente :
     * - restitue le stock ;
     * - supprime l'entrée de caisse liée ;
     * - marque la vente comme annulée.
     *
     * Un vendeur ne peut annuler que ses propres ventes.
     */
    public function destroy($id)
    {
        $boutiqueId = Auth::user()->boutique_id;

        DB::transaction(function () use ($id, $boutiqueId) {

            $sale = Movement::where(
                'boutique_id',
                $boutiqueId
            )
                ->where('type', 'sortie')
                ->findOrFail($id);

            /*
             * Un vendeur ne peut annuler
             * que ses propres ventes.
             */
            if (
                Auth::user()->role === 'vendeur' &&
                $sale->user_id !== Auth::id()
            ) {
                abort(
                    403,
                    'Vous ne pouvez annuler que vos propres ventes.'
                );
            }

            if ($sale->annule_at) {
                return;
            }

            foreach ($sale->products as $product) {

                $productToUpdate = Product::where(
                    'id',
                    $product->id
                )
                    ->where('boutique_id', $boutiqueId)
                    ->lockForUpdate()
                    ->first();

                if ($productToUpdate) {

                    $pivotQty =
                        $product->pivot->qty ??
                        ($product->pivot->quantity ?? 1);

                    $oldStock = $productToUpdate->quantity;

                    $newStock = $oldStock + $pivotQty;

                    $productToUpdate->update([
                        'quantity' => $newStock,
                        'status' => 'disponible',
                    ]);

                    StockMovement::create([
                        'boutique_id' => $boutiqueId,
                        'product_id' => $productToUpdate->id,
                        'user_id' => Auth::id(),
                        'type' => 'adjustment',
                        'quantity' => $pivotQty,
                        'old_stock' => $oldStock,
                        'new_stock' => $newStock,
                        'reason' =>
                            'Annulation de la vente #' .
                            $sale->id,
                    ]);
                }
            }

            CashMovement::where(
                'boutique_id',
                $boutiqueId
            )
                ->where('movement_id', $sale->id)
                ->delete();

            $sale->update([
                'annule_at' => now()
            ]);
        });

        return back()->with(
            'success',
            'Vente annulée, stock restitué et caisse mise à jour avec succès.'
        );
    }

    /**
     * Exporte toutes les ventes en PDF.
     */
    public function exportPdf()
    {
        $boutiqueId = Auth::user()->boutique_id;

        $sales = Movement::where(
            'boutique_id',
            $boutiqueId
        )
            ->where('type', 'sortie')
            ->whereNull('annule_at')
            ->with(['products', 'user'])
            ->latest()
            ->get();

        $totalGeneral = $sales->sum(function ($sale) {

            return $sale->products->sum(function ($product) {

                $qte =
                    $product->pivot->qty ??
                    ($product->pivot->quantity ?? 0);

                $prix =
                    $product->pivot->unit_price ??
                    ($product->selling_price ?? 0);

                return $qte * $prix;
            });
        });

        $pdf = Pdf::loadView(
            'sales.pdf-global',
            compact('sales', 'totalGeneral')
        );

        return $pdf->download(
            'toutes-les-ventes-' .
            date('Y-m-d') .
            '.pdf'
        );
    }

    /**
     * Exporte les ventes d'une date en PDF.
     */
    public function exportPdfByDate($date)
    {
        $boutiqueId = Auth::user()->boutique_id;

        $sales = Movement::where(
            'boutique_id',
            $boutiqueId
        )
            ->where('type', 'sortie')
            ->whereNull('annule_at')
            ->whereDate('created_at', $date)
            ->with(['products', 'user'])
            ->get();

        $totalGeneral = $sales->sum(function ($sale) {

            return $sale->products->sum(function ($product) {

                $qte =
                    $product->pivot->qty ??
                    ($product->pivot->quantity ?? 0);

                $prix =
                    $product->pivot->unit_price ??
                    ($product->selling_price ?? 0);

                return $qte * $prix;
            });
        });

        $pdf = Pdf::loadView(
            'sales.pdf-date',
            compact('sales', 'date', 'totalGeneral')
        );

        return $pdf->download(
            'ventes-' .
            $date .
            '.pdf'
        );
    }

    /**
     * Affiche le formulaire de modification d'une vente.
     */
    public function edit($id)
    {
        $user = Auth::user();

        $boutiqueId = $user->boutique_id;

        $sale = Movement::where(
            'boutique_id',
            $boutiqueId
        )
            ->where('type', 'sortie')
            ->with('products')
            ->findOrFail($id);

        /*
         * Une vente annulée ne peut pas être modifiée.
         */
        if ($sale->annule_at) {
            return back()->with(
                'error',
                'Cette vente a déjà été annulée et ne peut plus être modifiée.'
            );
        }

        /*
         * Un vendeur ne peut modifier
         * que ses propres ventes.
         */
        if (
            $user->role === 'vendeur' &&
            $sale->user_id !== $user->id
        ) {
            abort(
                403,
                'Vous ne pouvez modifier que vos propres ventes.'
            );
        }

        $products = Product::where(
            'boutique_id',
            $boutiqueId
        )
            ->orderBy('name')
            ->get();

        return view(
            'sales.edit',
            compact('sale', 'products')
        );
    }

    /**
     * Modifie une vente existante.
     *
     * Le stock est recalculé en tenant compte
     * de l'ancienne quantité vendue.
     *
     * Le vendeur ne peut modifier que ses propres ventes.
     */
    public function update(Request $request, $id)
    {
        $boutiqueId = Auth::user()->boutique_id;

        $request->validate([
            'motif' => 'nullable|string|max:255',

            'items' => 'required|array|min:1',

            'items.*.product_id' => [
                'required',
                'distinct',

                Rule::exists('products', 'id')
                    ->where(function ($query) use ($boutiqueId) {
                        $query->where(
                            'boutique_id',
                            $boutiqueId
                        );
                    }),
            ],

            'items.*.quantity' => [
                'required',
                'integer',
                'min:1',
                'max:2147483647',
            ],

            'items.*.unit_price' => [
                'required',
                'numeric',
                'min:0',
                'max:999999999999999999.99',
            ],
        ], [
            'items.required' =>
                'La vente doit contenir au moins un produit.',

            'items.min' =>
                'La vente doit contenir au moins un produit.',

            'items.*.product_id.required' =>
                'Le produit est obligatoire.',

            'items.*.product_id.distinct' =>
                'Un même produit ne peut pas apparaître plusieurs fois dans la vente.',

            'items.*.product_id.exists' =>
                'Un ou plusieurs produits sélectionnés sont invalides.',

            'items.*.quantity.required' =>
                'La quantité est obligatoire.',

            'items.*.quantity.min' =>
                'La quantité doit être supérieure à zéro.',

            'items.*.unit_price.required' =>
                'Le prix de vente est obligatoire.',

            'items.*.unit_price.min' =>
                'Le prix de vente ne peut pas être négatif.',
        ]);

        try {

            DB::transaction(function () use (
                $request,
                $boutiqueId,
                $id
            ) {

                /*
                 * 1. Récupérer la vente et la verrouiller.
                 */
                $sale = Movement::where(
                    'boutique_id',
                    $boutiqueId
                )
                    ->where('type', 'sortie')
                    ->with('products')
                    ->lockForUpdate()
                    ->findOrFail($id);

                /*
                 * Une vente annulée ne peut plus être modifiée.
                 */
                if ($sale->annule_at) {
                    throw new \Exception(
                        'Cette vente a déjà été annulée et ne peut plus être modifiée.'
                    );
                }

                /*
                 * Un vendeur ne peut modifier
                 * que ses propres ventes.
                 */
                if (
                    Auth::user()->role === 'vendeur' &&
                    $sale->user_id !== Auth::id()
                ) {
                    throw new \Exception(
                        'Vous ne pouvez modifier que vos propres ventes.'
                    );
                }

                /*
                 * 2. Récupérer les anciennes lignes.
                 */
                $oldItems = [];

                foreach ($sale->products as $product) {

                    $oldItems[$product->id] = [
                        'quantity' =>
                            (int) (
                                $product->pivot->qty ?? 0
                            ),

                        'unit_price' =>
                            (float) (
                                $product->pivot->unit_price ?? 0
                            ),
                    ];
                }

                /*
                 * 3. Récupérer les nouvelles lignes.
                 */
                $newItems = [];

                foreach ($request->items as $item) {

                    $newItems[(int) $item['product_id']] = [
                        'quantity' =>
                            (int) $item['quantity'],

                        'unit_price' =>
                            (float) $item['unit_price'],
                    ];
                }

                /*
                 * 4. Tous les produits concernés.
                 */
                $productIds = collect(
                    array_keys($oldItems)
                )
                    ->merge(array_keys($newItems))
                    ->unique()
                    ->values();

                /*
                 * 5. Verrouiller les produits concernés.
                 */
                $products = Product::where(
                    'boutique_id',
                    $boutiqueId
                )
                    ->whereIn('id', $productIds)
                    ->lockForUpdate()
                    ->get()
                    ->keyBy('id');

                /*
                 * 6. Vérifier que tous les produits existent.
                 */
                foreach ($productIds as $productId) {

                    if (!isset($products[$productId])) {

                        throw new \Exception(
                            "Le produit #{$productId} " .
                            "n'existe plus dans cette boutique."
                        );
                    }
                }

                /*
                 * 7. Vérifier les prix de vente.
                 */
                foreach ($newItems as $productId => $item) {

                    $product = $products[$productId];

                    if (
                        $item['unit_price'] <
                        (float) $product->purchase_price
                    ) {

                        throw new \Exception(
                            "Le prix de vente de « {$product->name} » " .
                            "ne peut pas être inférieur au prix d'achat (" .
                            number_format(
                                $product->purchase_price,
                                0,
                                ',',
                                ' '
                            ) .
                            " FCFA)."
                        );
                    }
                }

                /*
                 * 8. Recalculer les stocks.
                 */
                foreach ($productIds as $productId) {

                    $product = $products[$productId];

                    $oldQuantity =
                        $oldItems[$productId]['quantity'] ?? 0;

                    $newQuantity =
                        $newItems[$productId]['quantity'] ?? 0;

                    /*
                     * On restitue virtuellement
                     * l'ancienne quantité vendue.
                     */
                    $availableStock =
                        $product->quantity +
                        $oldQuantity;

                    /*
                     * Vérifier le stock disponible.
                     */
                    if ($newQuantity > $availableStock) {

                        throw new \Exception(
                            "Stock insuffisant pour « {$product->name} ». " .
                            "Stock disponible pour cette modification : " .
                            "{$availableStock}, " .
                            "quantité demandée : {$newQuantity}."
                        );
                    }

                    /*
                     * Différence de stock.
                     */
                    $delta =
                        $oldQuantity -
                        $newQuantity;

                    if ($delta === 0) {
                        continue;
                    }

                    $oldStock =
                        $product->quantity;

                    $newStock =
                        $oldStock + $delta;

                    $product->update([
                        'quantity' => $newStock,

                        'status' =>
                            $newStock > 0
                                ? 'disponible'
                                : 'rupture',
                    ]);

                    /*
                     * Historique du changement de stock.
                     */
                    StockMovement::create([
                        'boutique_id' => $boutiqueId,

                        'product_id' =>
                            $product->id,

                        'user_id' =>
                            Auth::id(),

                        'type' =>
                            'adjustment',

                        'quantity' =>
                            abs($delta),

                        'old_stock' =>
                            $oldStock,

                        'new_stock' =>
                            $newStock,

                        'reason' =>
                            'Modification de la vente #' .
                            $sale->id,
                    ]);
                }

                /*
                 * 9. Calculer le nouveau montant total.
                 */
                $newTotal = 0;

                foreach ($newItems as $item) {

                    $newTotal +=
                        $item['quantity'] *
                        $item['unit_price'];
                }

                /*
                 * 10. Synchroniser les produits
                 * de la vente.
                 */
                $syncData = [];

                foreach ($newItems as $productId => $item) {

                    $syncData[$productId] = [
                        'qty' =>
                            $item['quantity'],

                        'unit_price' =>
                            $item['unit_price'],
                    ];
                }

                $sale->products()->sync($syncData);

                /*
                 * 11. Mettre à jour la vente.
                 *
                 * user_id reste le créateur original.
                 * updated_by correspond à la personne
                 * qui effectue la modification.
                 */
                $sale->update([
                    'motif' =>
                        $request->motif ??
                        $sale->motif,

                    'updated_by' =>
                        Auth::id(),
                ]);

                /*
                 * 12. Mettre à jour la caisse.
                 */
                $cashMovement = CashMovement::where(
                    'boutique_id',
                    $boutiqueId
                )
                    ->where(
                        'movement_id',
                        $sale->id
                    )
                    ->lockForUpdate()
                    ->first();

                if ($cashMovement) {

                    $cashMovement->update([
                        'amount' => $newTotal,
                    ]);

                } else {

                    /*
                     * Sécurité pour une ancienne vente
                     * qui n'aurait pas de mouvement de caisse.
                     */
                    CashMovement::create([
                        'boutique_id' =>
                            $boutiqueId,

                        'movement_id' =>
                            $sale->id,

                        'user_id' =>
                            $sale->user_id,

                        'type' =>
                            'in',

                        'category' =>
                            'sale',

                        'amount' =>
                            $newTotal,

                        'motif' =>
                            $sale->motif ??
                            'Encaissement vente #' .
                            $sale->id,

                        'is_unjustified' =>
                            false,
                    ]);
                }
            });

        } catch (\Exception $e) {

            return back()
                ->withInput()
                ->withErrors([
                    'items' =>
                        $e->getMessage()
                ]);
        }

        return redirect()
            ->route('sales.index')
            ->with(
                'success',
                'Vente modifiée avec succès. ' .
                'Le stock et la caisse ont été mis à jour.'
            );
    }
}
