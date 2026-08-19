<?php

namespace App\Http\Controllers;

use App\Models\Supplier;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class SupplierController extends Controller
{
    /**
     * Affiche la liste des fournisseurs de la boutique connectée avec option de recherche.
     */
    public function index(Request $request)
    {

        // Si le vendeur parvient à passer, on s'assure qu'il ne touche pas au prix d'achat
        if (Auth::user()->role === 'vendeur') {
            return abort(403,"vous n'etes pas autoriser a voir cette page");
        }

        // 1. On récupère le terme de recherche s'il existe dans l'URL 
        $search = $request->input('search');

        // 2. On construit la requête de base (isolée par boutique)
        $query = Supplier::where('boutique_id', Auth::user()->boutique_id);

        // 3. Si l'utilisateur a tapé quelque chose, on filtre les résultats
        if ($search) {
            $query->where(function($q) use ($search) {
                $q->where('name', 'LIKE', "%{$search}%")
                  ->orWhere('contact_person', 'LIKE', "%{$search}%")
                  ->orWhere('phone', 'LIKE', "%{$search}%")
                  ->orWhere('email', 'LIKE', "%{$search}%");
            });
        }

        // 4. On récupère les résultats avec pagination (et on garde la recherche active dans les pages suivantes grâce à ->appends())
        $suppliers = $query->latest()->paginate(10)->appends(['search' => $search]);

        return view('suppliers.index', compact('suppliers', 'search'));
    }
    /**
     * Affiche le formulaire de création d'un fournisseur.
     */
    public function create()
    {
        return view('suppliers.create');
    }

    /**
     * Enregistre un nouveau fournisseur dans la base de données.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'phone' => 'required|string|max:9',
            'email' => 'nullable|email|max:255',
            'adresse'=> 'nullable|string|max:255',
        ]);

        // Si le vendeur parvient à passer, on s'assure qu'il ne touche pas au prix d'achat
        if (Auth::user()->role === 'vendeur') {
            return abort(403,"vous n'etes pas autoriser a cree un fournisseur");
        }

        // On associe automatiquement le fournisseur à la boutique de l'utilisateur connecté
        $validated['boutique_id'] = Auth::user()->boutique_id;
        $validated['user_id'] = Auth::id();
        Supplier::create($validated);

        return redirect()->route('suppliers.index')
                         ->with('succes', 'Fournisseur ajouté avec succès.');
    }

    /**
     * Affiche les détails d'un fournisseur spécifique.
     */
    public function show(Supplier $supplier)
    {
        // Sécurité multi-boutique
        if ($supplier->boutique_id !== Auth::user()->boutique_id) {
            abort(404, "Fournisseur introuvable.");
        }

         // Si le vendeur parvient à passer, on s'assure qu'il ne touche pas au prix d'achat
        if (Auth::user()->role === 'vendeur') {
            return abort(403,"vous n'etes pas autoriser a voir cette page");
        }

        return view('suppliers.show', compact('supplier'));
    }

    /**
     * Affiche le formulaire de modification d'un fournisseur.
     */
    public function edit(Supplier $supplier)
    {
        // Sécurité multi-boutique
        if ($supplier->boutique_id !== Auth::user()->boutique_id) {
            abort(404, "Fournisseur introuvable.");
        }

        return view('suppliers.edit', compact('supplier'));
    }

    /**
     * Met à jour les informations du fournisseur.
     */
    public function update(Request $request, Supplier $supplier)
    {
        // Sécurité multi-boutique
        if ($supplier->boutique_id !== Auth::user()->boutique_id) {
            abort(404, "Fournisseur introuvable.");
        }

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'contact_person' => 'nullable|string|max:255',
            'phone' => 'required|string|max:20',
            'email' => 'nullable|email|max:255',
            'adresse' => 'nullable|string|max:500',
        ]);

         // Si le vendeur parvient à passer, on s'assure qu'il ne touche pas au prix d'achat
        if (Auth::user()->role === 'vendeur') {
            return abort(403,"vous n'etes pas autoriser a modifier des information sur cette page");
        }

        $validated['updated_by'] = Auth::id();
        $supplier->update($validated);

        return redirect()->route('suppliers.index')
                         ->with('succes', 'Fournisseur modifié avec succès.');
    }

    /**
     * Supprime un fournisseur.
     */
    public function destroy(Supplier $supplier)
    {
        // Sécurité multi-boutique
        if ($supplier->boutique_id !== Auth::user()->boutique_id) {
            abort(404, "Fournisseur introuvable.");
        }

        // Sécurité supplémentaire : seuls les administrateurs peuvent supprimer
        if (Auth::user()->role !== 'administrateur') {
            abort(403, "Vous n'avez pas l'autorisation de supprimer ce produit.");
        }

        $supplier->delete();

        return redirect()->route('suppliers.index')
                         ->with('succes', 'Fournisseur supprimé avec succès.');
    }
}