<?php

namespace App\Http\Controllers;

use App\Models\Supplier;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Barryvdh\DomPDF\Facade\Pdf;

class SupplierController extends Controller
{
    /**
     * Affiche la liste des fournisseurs de la boutique connectée avec option de recherche.
     */
    public function index(Request $request)
    {
        if (Auth::user()->role === 'vendeur') {
            return abort(403, "Vous n'êtes pas autorisé à voir cette page");
        }

        $boutiqueId = Auth::user()->boutique_id;
        $search = $request->input('search');

        // MODIFICATION ICI : On charge la relation 'products' avec with()
        $query = Supplier::with('products')->where('boutique_id', $boutiqueId);

        if ($search) {
            $query->where(function($q) use ($search) {
                $q->where('name', 'LIKE', "%{$search}%")
                  ->orWhere('phone', 'LIKE', "%{$search}%")
                  ->orWhere('email', 'LIKE', "%{$search}%")
                  ->orWhereHas('products', function($subQ) use ($search) {
                      $subQ->where('name', 'LIKE', "%{$search}%");
                  });
            });
        }

        $suppliers = $query->latest()->paginate(10)->appends(['search' => $search]);

        return view('suppliers.index', compact('suppliers', 'search'));
    }

    /**
     * Affiche le formulaire de création d'un fournisseur.
     */
    public function create()
    {
        if (Auth::user()->role === 'vendeur') {
            return abort(403, "Vous n'êtes pas autorisé à voir cette page");
        }

        // MODIFICATION ICI : On récupère les produits de la boutique pour les afficher dans le formulaire
        $products = Product::where('boutique_id', Auth::user()->boutique_id)->get();

        return view('suppliers.create', compact('products'));
    }

    /**
     * Enregistre un nouveau fournisseur dans la base de données.
     */
    public function store(Request $request)
    {
        if (Auth::user()->role === 'vendeur') {
            return abort(403, "Vous n'êtes pas autorisé à créer un fournisseur");
        }

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'phone' => 'required|string|max:20',
            'email' => 'nullable|email|max:255',
            'adresse' => 'nullable|string|max:255',
            'products' => 'nullable|array',
            'products.*' => 'exists:products,id',
        ]);

        $validated['boutique_id'] = Auth::user()->boutique_id;
        $validated['user_id'] = Auth::id();

        // Création du fournisseur
        $supplier = Supplier::create($validated);

        // MODIFICATION ICI : Synchronisation des produits sélectionnés
        if ($request->has('products')) {
            $supplier->products()->sync($request->products);
        }

        return redirect()->route('suppliers.index')
                         ->with('succes', 'Fournisseur ajouté avec succès.');
    }

    /**
     * Affiche les détails d'un fournisseur spécifique.
     */
    public function show(Supplier $supplier)
    {
        if ($supplier->boutique_id !== Auth::user()->boutique_id) {
            abort(404, "Fournisseur introuvable.");
        }

        if (Auth::user()->role === 'vendeur') {
            return abort(403, "Vous n'êtes pas autorisé à voir cette page");
        }

        // Charger les produits liés si nécessaire
        $supplier->load('products');

        return view('suppliers.show', compact('supplier'));
    }

    /**
     * Affiche le formulaire de modification d'un fournisseur.
     */
    /**
     * Affiche le formulaire de modification d'un fournisseur.
     */
    public function edit(Supplier $supplier)
    {
        // Sécurité multi-boutique
        if ($supplier->boutique_id !== Auth::user()->boutique_id) {
            abort(404, "Fournisseur introuvable.");
        }

        // Sécurité rôle vendeur
        if (Auth::user()->role === 'vendeur') {
            return abort(403, "Vous n'êtes pas autorisé à voir cette page");
        }

        // Récupération de tous les produits de la boutique et chargement des relations
        $products = Product::where('boutique_id', Auth::user()->boutique_id)->get();
        $supplier->load('products');

        return view('suppliers.edit', compact('supplier', 'products'));
    }

    /**
     * Met à jour les informations du fournisseur.
     */
    public function update(Request $request, Supplier $supplier)
    {
        if ($supplier->boutique_id !== Auth::user()->boutique_id) {
            abort(404, "Fournisseur introuvable.");
        }

        if (Auth::user()->role === 'vendeur') {
            return abort(403, "Vous n'êtes pas autorisé à modifier des informations sur cette page");
        }

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'phone' => 'required|string|max:20',
            'email' => 'nullable|email|max:255',
            'adresse' => 'nullable|string|max:500',
            'products' => 'nullable|array',
            'products.*' => 'exists:products,id',
        ]);

        $validated['updated_by'] = Auth::id();
        $supplier->update($validated);

        // MODIFICATION ICI : Mise à jour (synchronisation) des produits associés
        if ($request->has('products')) {
            $supplier->products()->sync($request->products);
        } else {
            $supplier->products()->detach(); // Si aucun produit n'est coché, on retire tout
        }

        return redirect()->route('suppliers.index')
                         ->with('succes', 'Fournisseur modifié avec succès.');
    }

    /**
     * Supprime un fournisseur.
     */
    public function destroy(Supplier $supplier)
    {
        if ($supplier->boutique_id !== Auth::user()->boutique_id) {
            abort(404, "Fournisseur introuvable.");
        }

        if (Auth::user()->role !== 'administrateur') {
            abort(403, "Vous n'avez pas l'autorisation de supprimer ce fournisseur.");
        }

        $supplier->delete();

        return redirect()->route('suppliers.index')
                         ->with('succes', 'Fournisseur supprimé avec succès.');
    }

    public function exportPdf()
    {
        $suppliers = Supplier::with('products')->where('boutique_id', Auth::user()->boutique_id)->get();

        $pdf = Pdf::loadView('suppliers.pdf', compact('suppliers'));

        return $pdf->download('liste-fournisseurs-' . date('Y-m-d') . '.pdf');
    }
}