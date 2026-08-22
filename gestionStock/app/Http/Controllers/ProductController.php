<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Category;
use App\Models\Supplier;
use App\Models\StockMovement;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

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

        // 1. Total des produits
        $totalProducts = Product::where('boutique_id', $boutiqueId)->count();

        // 2. Valeur totale du stock (Quantité * Prix de vente)
        $stockValue = Product::where('boutique_id', $boutiqueId)
            ->selectRaw('SUM(quantity * selling_price) as total')
            ->value('total') ?? 0;

        // 3. Stock faible (quantité > 0 mais <= seuil d'alerte)
        $lowStockCount = Product::where('boutique_id', $boutiqueId)
            ->where('quantity', '>', 0)
            ->whereColumn('quantity', '<=', 'alert_threshold')
            ->count();

        // 4. Rupture de stock (quantité <= 0)
        $outOfStockCount = Product::where('boutique_id', $boutiqueId)
            ->where('quantity', '<=', 0)
            ->count();

        $query = Product::where('boutique_id', $boutiqueId)
            ->with(['category']);

        // Recherche textuelle (sur le nom ou la référence)
        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'LIKE', "%{$search}%")
                    ->orWhere('reference', 'LIKE', "%{$search}%");
            });
        }

        // Filtre par catégorie
        if ($categoryId) {
            $query->where('category_id', $categoryId);
        }

        // Filtre par statut
        if ($status) {
            $query->where('status', $status);
        }

        $categories = Category::where('boutique_id', $boutiqueId)->get();

        $products = $query->latest()->paginate(10)->appends([
            'search' => $search,
            'category_id' => $categoryId,
            'status' => $status,
        ]);

        // SÉCURITÉ : Si l'utilisateur n'est ni admin ni vice-admin, on cache le prix d'achat
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

    /**
     * Affiche le formulaire de création d'un produit.
     */
    public function create()
    {
        $categories = Category::where('boutique_id', Auth::user()->boutique_id)->get();

        return view('products.create', compact('categories'));
    }

    /**
     * Enregistre un nouveau produit dans la base de données.
     */
    public function store(Request $request)
    {
        // Vérification des droits
        if (Auth::user()->role === 'vendeur') {
            return abort(403, "vous n'etes pas autoriser a modifier des information sur cette page");
        }

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'reference' => [
                'nullable',
                'string',
                'max:100',
                \Illuminate\Validation\Rule::unique('products')->where(function ($query) {
                    return $query->where('boutique_id', Auth::user()->boutique_id);
                }),
            ],
            'purchase_price' => 'required|numeric|min:0',
            'selling_price' => 'required|numeric|min:0|gte:purchase_price',
            'alert_threshold' => 'nullable|integer|min:0',
            'category_id' => 'required|exists:categories,id',
            'description' => 'nullable|string|max:1000',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:2048',
        ],[
            'reference.unique' => 'Cette référence existe déjà pour cette boutique.',
            'selling_price.gte' => 'Le prix de vente ne peut pas être inférieur au prix d\'achat.',
            'image.image' => 'Le fichier doit être une image.',
            'image.max' => 'L\'image ne doit pas dépasser 2 Mo.',
        ]);
            
        // Gestion de l'upload de l'image
        if ($request->hasFile('image')) {
            $validated['image'] = $request->file('image')->store('products', 'public');
        }

        $validated['quantity'] = 0;
        $validated['status'] = 'rupture'; 
        $validated['boutique_id'] = Auth::user()->boutique_id;
        $validated['user_id'] = Auth::id();

        // 1. Création du produit
        $product = Product::create($validated);

        // 2. Création immédiate de la notification de rupture initiale
        \App\Models\Notification::create([
            'boutique_id' => $product->boutique_id ?? 1,
            'user_id' => Auth::id(),
            'title' => 'Rupture de stock',
            'message' => "Le produit '{$product->name}' a été créé avec un stock de 0.",
            'type' => 'warning'
        ]);

        return redirect()->route('products.index')->with('success', 'Produit créé avec succès.');
    }

    /**
     * Affiche les détails d'un produit spécifique.
     */
    public function show(Product $product)
    {
        if ($product->boutique_id !== Auth::user()->boutique_id) {
            abort(404, "Produit introuvable.");
        }

        return view('products.show', compact('product'));
    }

    /**
     * Affiche le formulaire de modification d'un produit.
     */
    public function edit(Product $product)
    {
        if ($product->boutique_id !== Auth::user()->boutique_id) {
            abort(404, "Produit introuvable.");
        }

        $categories = Category::where('boutique_id', Auth::user()->boutique_id)->get();

        return view('products.edit', compact('product', 'categories'));
    }

    /**
     * Met à jour les informations du produit.
     */
    public function update(Request $request, Product $product)
    {
        if ($product->boutique_id !== Auth::user()->boutique_id) {
            abort(404 ,"acces non autoriser");
        }

        if (Auth::user()->role === 'vendeur') {
            abort(403, "Action non autorisée.");
        }

        $rules = [
            'name' => 'required|string|max:255',
            'reference' => 'nullable|string|max:100',
            'selling_price' => 'required|numeric|min:0',
            'quantity' => 'required|integer|min:0',
            'alert_threshold' => 'required|integer|min:0',
            'status' => 'required|in:disponible,rupture',
            'category_id' => 'required|exists:categories,id',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:2048',
        ];

        if (in_array(Auth::user()->role, ['administrateur', 'vice_administrateur'])) {
            $rules['purchase_price'] = 'required|numeric|min:0';
        }

        $validated = $request->validate($rules);

        if (Auth::user()->role === 'vendeur') {
            unset($validated['purchase_price']);
        }

        if ($request->hasFile('image')) {
            if ($product->image && Storage::disk('public')->exists($product->image)) {
                Storage::disk('public')->delete($product->image);
            }
            $validated['image'] = $request->file('image')->store('products', 'public');
        }

        $validated['updated_by'] = Auth::id();

        // --- ENREGISTREMENT AUTOMATIQUE DU MOUVEMENT DE STOCK ---
        $oldStock = $product->quantity;
        $newStock = (int) $validated['quantity'];
        $difference = $newStock - $oldStock;

        if ($difference !== 0) {
            StockMovement::create([
                'boutique_id' => $product->boutique_id,
                'product_id'  => $product->id,
                'user_id'     => Auth::id(),
                'type'        => $difference > 0 ? 'entry' : 'adjustment',
                'quantity'    => abs($difference),
                'old_stock'   => $oldStock,
                'new_stock'   => $newStock,
                'reason'      => 'Modification manuelle depuis la modification du produit',
            ]);
        }
        // --------------------------------------------------------

        $product->update($validated);

        return redirect()->route('products.index')->with('succes', 'Produit mis à jour.');
    }

    /**
     * Supprime un produit.
     */
    public function destroy(Product $product)
    {
        if (Auth::user()->role !== 'administrateur') {
            abort(403, "Vous n'avez pas l'autorisation de supprimer ce produit.");
        }

        if ($product->boutique_id !== Auth::user()->boutique_id) {
            abort(404);
        }

        if ($product->image && Storage::disk('public')->exists($product->image)) {
            Storage::disk('public')->delete($product->image);
        }

        $product->delete();
        
        return redirect()->route('products.index')->with('succes', 'Produit supprimé.');
    }

    /**
     * Exporte la liste des produits en PDF.
     */
    public function exportPdf()
    {
        $boutiqueId = Auth::user()->boutique_id;
        $products = Product::where('boutique_id', $boutiqueId)->with('category')->get();

        // Génération et téléchargement du PDF
        $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('products.pdf', compact('products'));

        return $pdf->download('inventaire-produits-' . date('Y-m-d') . '.pdf');
    }
}