<?php

namespace App\Http\Controllers;

use App\Models\Supplier;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
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

        $products = Product::where('boutique_id', Auth::user()->boutique_id)->get();

        return view('suppliers.create', compact('products'));
    }

    /**
     * Enregistre un nouveau fournisseur dans la base de données (Obligation d'avoir au moins un produit).
     */
    public function store(Request $request)
    {
        if (Auth::user()->role === 'vendeur') {
            return abort(403, "Vous n'êtes pas autorisé à créer un fournisseur");
        }

        $boutiqueId = Auth::user()->boutique_id;

        // Validation stricte avec unicité du téléphone par boutique
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'phone' => [
                'required',
                'string',
                'max:20',
                Rule::unique('suppliers', 'phone')->where('boutique_id', $boutiqueId),
            ],
            'email' => [
                'nullable',
                'email',
                'max:255',
                // Optionnel : décommenter si tu veux aussi l'email unique par boutique
                // Rule::unique('suppliers', 'email')->where('boutique_id', $boutiqueId),
            ],
            'adresse' => 'nullable|string|max:500',
            'products' => 'required|array|min:1',
            'products.*' => [
                'required',
                Rule::exists('products', 'id')->where('boutique_id', $boutiqueId),
            ],
        ], [
            'phone.unique' => 'Ce numéro de téléphone est déjà utilisé par un autre fournisseur dans cette boutique.',
            'products.required' => 'Vous devez obligatoirement assigner au moins un produit à ce fournisseur.',
            'products.min' => 'Vous devez obligatoirement assigner au moins un produit à ce fournisseur.',
            'products.*.exists' => 'Un ou plusieurs produits sélectionnés sont invalides ou n\'appartiennent pas à votre boutique.',
        ]);

        return DB::transaction(function () use ($request, $validated, $boutiqueId) {
            $validated['boutique_id'] = $boutiqueId;
            $validated['user_id'] = Auth::id();

            // Création du fournisseur
            $supplier = Supplier::create($validated);

            // Synchronisation des produits
            $supplier->products()->sync($request->products);

            return redirect()->route('suppliers.index')
                             ->with('succes', 'Fournisseur ajouté avec succès.');
        });
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

        $supplier->load('products');

        return view('suppliers.show', compact('supplier'));
    }

    /**
     * Affiche le formulaire de modification d'un fournisseur.
     */
    public function edit(Supplier $supplier)
    {
        if ($supplier->boutique_id !== Auth::user()->boutique_id) {
            abort(404, "Fournisseur introuvable.");
        }

        if (Auth::user()->role === 'vendeur') {
            return abort(403, "Vous n'êtes pas autorisé à voir cette page");
        }

        $products = Product::where('boutique_id', Auth::user()->boutique_id)->get();
        $supplier->load('products');

        return view('suppliers.edit', compact('supplier', 'products'));
    }

    /**
     * Met à jour les informations du fournisseur (Garde l'obligation d'avoir au moins un produit).
     */
    public function update(Request $request, Supplier $supplier)
    {
        if ($supplier->boutique_id !== Auth::user()->boutique_id) {
            abort(404, "Fournisseur introuvable.");
        }

        if (Auth::user()->role === 'vendeur') {
            return abort(403, "Vous n'êtes pas autorisé à modifier des informations sur cette page");
        }

        $boutiqueId = Auth::user()->boutique_id;

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'phone' => 'required|string|max:20',
            'email' => 'nullable|email|max:255',
            'adresse' => 'nullable|string|max:500',
            'products' => 'required|array|min:1',
            'products.*' => [
                'required',
                Rule::exists('products', 'id')->where('boutique_id', $boutiqueId),
            ],
        ], [
            'products.required' => 'Le fournisseur doit obligatoirement rester associé à au moins un produit.',
            'products.min' => 'Le fournisseur doit obligatoirement rester associé à au moins un produit.',
            'products.*.exists' => 'Un ou plusieurs produits sélectionnés sont invalides.',
        ]);

        return DB::transaction(function () use ($request, $supplier, $validated) {
            $validated['updated_by'] = Auth::id();
            $supplier->update($validated);

            // Mise à jour des produits associés
            $supplier->products()->sync($request->products);

            return redirect()->route('suppliers.index')
                             ->with('succes', 'Fournisseur modifié avec succès.');
        });
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

        DB::transaction(function () use ($supplier) {
            // Détacher les produits avant la suppression pour nettoyer la table pivot
            $supplier->products()->detach();
            $supplier->delete();
        });

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