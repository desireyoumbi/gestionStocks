<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Category;
use App\Models\StockMovement;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class ProductController extends Controller
{
    /**
     * Affiche la liste des produits de la boutique connectée avec recherche et pagination.
     */
    public function index(Request $request)
    {
        $search = $request->input('search');
        $categoryId = $request->input('category_id');
        $status = $request->input('status'); // 'disponible' ou 'rupture'

        $boutiqueId = Auth::user()->boutique_id;

        $totalProducts = Product::where('boutique_id', $boutiqueId)->count();

        $stockValue = Product::where('boutique_id', $boutiqueId)
            ->selectRaw('SUM(quantity * selling_price) as total')
            ->value('total') ?? 0;

        $lowStockCount = Product::where('boutique_id', $boutiqueId)
            ->where('quantity', '>', 0)
            ->whereColumn('quantity', '<=', 'alert_threshold')
            ->count();

        $outOfStockCount = Product::where('boutique_id', $boutiqueId)
            ->where('quantity', '<=', 0)
            ->count();

        $query = Product::where('boutique_id', $boutiqueId)
            ->with(['category']);

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'LIKE', "%{$search}%")
                    ->orWhere('reference', 'LIKE', "%{$search}%");
            });
        }

        if ($categoryId) {
            $query->where('category_id', $categoryId);
        }

        if ($status) {
            $query->where('status', $status);
        }

        $categories = Category::where('boutique_id', $boutiqueId)->get();

        $products = $query->latest()->paginate(10)->appends([
            'search' => $search,
            'category_id' => $categoryId,
            'status' => $status,
        ]);

        if (!in_array(Auth::user()->role, ['administrateur', 'vice_administrateur'])) {
            $products->getCollection()->each->makeHidden(['purchase_price']);
        }

        return view('products.index', compact(
            'products',
            'search',
            'categories',
            'categoryId',
            'status',
            'totalProducts',
            'stockValue',
            'lowStockCount',
            'outOfStockCount'
        ));
    }

    public function create()
    {
        if (Auth::user()->role === 'vendeur') {
            abort(403, "Vous n'êtes pas autorisé à accéder à cette page.");
        }

        $categories = Category::where(
            'boutique_id',
            Auth::user()->boutique_id
        )->get();

        return view('products.create', compact('categories'));
    }

    /**
     * Enregistre un nouveau produit dans la base de données.
     */
    public function store(Request $request)
    {
        if (Auth::user()->role === 'vendeur') {
            abort(403, "Vous n'êtes pas autorisé à effectuer cette action.");
        }

        $validated = $request->validate([
            'name' => 'required|string|max:255',

            'reference' => [
                'nullable',
                'string',
                'max:100',
                Rule::unique('products')->where(function ($query) {
                    return $query->where(
                        'boutique_id',
                        Auth::user()->boutique_id
                    );
                }),
            ],

            'purchase_price' =>
                'required|numeric|min:0.01|max:999999999999999999.99',

            'selling_price' =>
                'required|numeric|min:0|max:999999999999999999.99|gte:purchase_price',

            'quantity' =>
                'required|integer|min:0|max:2147483647',

            'alert_threshold' =>
                'required|integer|min:5|max:2147483647',

            'category_id' => [
                'required',
                Rule::exists('categories', 'id')->where(
                    'boutique_id',
                    Auth::user()->boutique_id
                ),
            ],

            'description' =>
                'nullable|string|max:1000',

            'image' =>
                'nullable|image|mimes:jpeg,png,jpg,webp|max:2048',
        ], [
            'reference.unique' =>
                'Cette référence existe déjà pour cette boutique.',

            'purchase_price.min' =>
                'Le prix d\'achat ne peut pas être égal à 0 ou négatif.',

            'selling_price.gte' =>
                'Le prix de vente ne peut pas être inférieur au prix d\'achat.',

            'alert_threshold.min' =>
                'Le seuil d\'alerte ne peut pas être inférieur à 5.',

            'image.image' =>
                'Le fichier doit être une image.',

            'image.max' =>
                'L\'image ne doit pas dépasser 2 Mo.',
        ]);

        return DB::transaction(function () use ($request, $validated) {

            $boutiqueId = Auth::user()->boutique_id;

            if (!empty($validated['reference'])) {

                $exists = Product::where('boutique_id', $boutiqueId)
                    ->where('reference', $validated['reference'])
                    ->lockForUpdate()
                    ->exists();

                if ($exists) {
                    return back()
                        ->withInput()
                        ->withErrors([
                            'reference' =>
                                'Un produit avec cette référence vient d\'être créé par un autre utilisateur.'
                        ]);
                }
            }

            /*
             * ============================================================
             * IMAGE → STOCKAGE LOCAL (disque "public" de Laravel)
             *
             * Sur Hostinger, le disque est persistant : plus besoin
             * d'un stockage externe comme Supabase (utile uniquement
             * sur Render gratuit, dont le disque est éphémère).
             * ============================================================
             */
            if ($request->hasFile('image')) {

                $validated['image'] = $request
                    ->file('image')
                    ->store('products', 'public');
            }

            $initialQuantity = (int) $validated['quantity'];

            $validated['status'] =
                $initialQuantity > 0 ? 'disponible' : 'rupture';

            $validated['boutique_id'] = $boutiqueId;
            $validated['user_id'] = Auth::id();

            $product = Product::create($validated);

            if ($initialQuantity > 0) {

                StockMovement::create([
                    'boutique_id' => $product->boutique_id,
                    'product_id'  => $product->id,
                    'user_id'     => Auth::id(),
                    'type'        => 'entry',
                    'quantity'    => $initialQuantity,
                    'old_stock'   => 0,
                    'new_stock'   => $initialQuantity,
                    'reason'      => 'Stock initial à la création du produit',
                ]);
            }

            return redirect()
                ->route('products.index')
                ->with('success', 'Produit créé avec succès.');
        });
    }

    public function show(Product $product)
    {
        if ($product->boutique_id !== Auth::user()->boutique_id) {
            abort(404, "Produit introuvable.");
        }

        return view('products.show', compact('product'));
    }

    public function edit(Product $product)
    {
        if ($product->boutique_id !== Auth::user()->boutique_id) {
            abort(404, "Produit introuvable.");
        }

        if (Auth::user()->role === 'vendeur') {
            abort(403, "Action non autorisée.");
        }

        $categories = Category::where(
            'boutique_id',
            Auth::user()->boutique_id
        )->get();

        return view('products.edit', compact('product', 'categories'));
    }

    /**
     * Met à jour les informations du produit.
     */
    public function update(Request $request, Product $product)
    {
        if ($product->boutique_id !== Auth::user()->boutique_id) {
            abort(404, "Accès non autorisé.");
        }

        if (Auth::user()->role === 'vendeur') {
            abort(403, "Action non autorisée.");
        }

        $rules = [
            'name' => 'required|string|max:255',

            'reference' => [
                'nullable',
                'string',
                'max:100',
                Rule::unique('products')->where(function ($query) use ($product) {
                    return $query->where(
                        'boutique_id',
                        $product->boutique_id
                    );
                })->ignore($product->id),
            ],

            'selling_price' =>
                'required|numeric|min:0|max:999999999999999999.99',

            'quantity' =>
                'required|integer|min:0|max:2147483647',

            'alert_threshold' =>
                'required|integer|min:5|max:2147483647',

            'category_id' => [
                'required',
                Rule::exists('categories', 'id')->where(
                    'boutique_id',
                    Auth::user()->boutique_id
                ),
            ],

            'image' =>
                'nullable|image|mimes:jpeg,png,jpg,webp|max:2048',
        ];

        if (in_array(
            Auth::user()->role,
            ['administrateur', 'vice_administrateur']
        )) {

            $rules['purchase_price'] =
                'required|numeric|min:0.01|max:999999999999999999.99';
        }

        $validated = $request->validate($rules, [
            'purchase_price.min' =>
                'Le prix d\'achat ne peut pas être égal à 0 ou négatif.',

            'alert_threshold.min' =>
                'Le seuil d\'alerte ne peut pas être inférieur à 5.',
        ]);

        if (Auth::user()->role === 'vendeur') {
            unset($validated['purchase_price']);
        }

        $newStockPreview = (int) $validated['quantity'];

        $validated['status'] =
            $newStockPreview > 0 ? 'disponible' : 'rupture';

        return DB::transaction(function () use (
            $request,
            $product,
            $validated
        ) {

            /*
             * ============================================================
             * NOUVELLE IMAGE → STOCKAGE LOCAL
             * ============================================================
             */
            if ($request->hasFile('image')) {

                // Supprimer l'ancienne image locale si elle existe
                if (
                    $product->image &&
                    Storage::disk('public')->exists($product->image)
                ) {

                    Storage::disk('public')->delete($product->image);
                }

                $validated['image'] = $request
                    ->file('image')
                    ->store('products', 'public');
            }

            $validated['updated_by'] = Auth::id();

            $oldStock = $product->quantity;
            $newStock = (int) $validated['quantity'];
            $difference = $newStock - $oldStock;

            if ($difference !== 0) {

                StockMovement::create([
                    'boutique_id' => $product->boutique_id,
                    'product_id'  => $product->id,
                    'user_id'     => Auth::id(),
                    'type'        => $difference > 0
                        ? 'entry'
                        : 'adjustment',
                    'quantity'    => abs($difference),
                    'old_stock'   => $oldStock,
                    'new_stock'   => $newStock,
                    'reason'      =>
                        'Modification manuelle depuis la modification du produit',
                ]);
            }

            $product->update($validated);

            return redirect()
                ->route('products.index')
                ->with('success', 'Produit mis à jour avec succès.');
        });
    }

    /**
     * Supprime un produit.
     */
    public function destroy(Product $product)
    {
        if (!in_array(
            Auth::user()->role,
            ['administrateur', 'vice_administrateur']
        )) {
            abort(
                403,
                "Vous n'avez pas l'autorisation de supprimer ce produit."
            );
        }

        if ($product->boutique_id !== Auth::user()->boutique_id) {
            abort(404);
        }

        /*
         * ============================================================
         * SUPPRESSION IMAGE → STOCKAGE LOCAL
         * ============================================================
         */
        if (
            $product->image &&
            Storage::disk('public')->exists($product->image)
        ) {

            Storage::disk('public')->delete($product->image);
        }

        $product->delete();

        return redirect()
            ->route('products.index')
            ->with('success', 'Produit supprimé avec succès.');
    }

    /**
     * Exporte la liste des produits en PDF.
     */
    public function exportPdf()
    {
        $boutiqueId = Auth::user()->boutique_id;

        $products = Product::where(
            'boutique_id',
            $boutiqueId
        )
            ->with('category')
            ->get();

        $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView(
            'products.pdf',
            compact('products')
        );

        return $pdf->download(
            'inventaire-produits-' . date('Y-m-d') . '.pdf'
        );
    }
}