<?php

namespace App\Http\Controllers;

use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

class CategoryController extends Controller
{
    /**
     * Affiche la liste des catégories de la boutique connectée, avec recherche.
     */
    public function index(Request $request)
    {
        $search = $request->input('search');

        $query = Category::where('boutique_id', Auth::user()->boutique_id)
            ->with(['user']);

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'LIKE', "%{$search}%")
                  ->orWhere('description', 'LIKE', "%{$search}%");
            });
        }

        $categories = $query->latest()->paginate(10)->appends([
            'search' => $search,
        ]);

        return view('categories.index', compact('categories', 'search'));
    }

    /**
     * Affiche le formulaire de création d'une catégorie.
     */
    public function create()
    {
        // Sécurité : seuls admin/vice-admin peuvent créer une catégorie
        if (Auth::user()->role === 'vendeur') {
            abort(403, "Vous n'êtes pas autorisé à créer une catégorie.");
        }

        return view('categories.create');
    }

    /**
     * Enregistre une nouvelle catégorie dans la base de données.
     */
    public function store(Request $request)
    {
        // Sécurité : seuls admin/vice-admin peuvent créer une catégorie
        if (Auth::user()->role === 'vendeur') {
            abort(403, "Vous n'êtes pas autorisé à créer une catégorie.");
        }

        $validated = $request->validate([
            'name' => [
                'required',
                'string',
                'max:255',
                Rule::unique('categories', 'name')
                    ->where('boutique_id', Auth::user()->boutique_id)
            ],
            'description' => ['nullable', 'string'],
        ], [
            'name.unique' => 'Une catégorie portant ce nom existe déjà dans votre boutique.',
        ]);

        Category::create([
            'name' => $validated['name'],
            'description' => $validated['description'] ?? null,
            'boutique_id' => Auth::user()->boutique_id,
            'user_id' => Auth::id(),
        ]);

        return redirect()->route('categories.index')
                         ->with('success', 'Catégorie créée avec succès.');
    }

    /**
     * Affiche les détails d'une catégorie spécifique.
     */
    public function show(Category $category)
    {
        // Sécurité multi-boutique
        if ($category->boutique_id !== Auth::user()->boutique_id) {
            abort(404, "Catégorie introuvable.");
        }

        return view('categories.show', compact('category'));
    }

    /**
     * Affiche le formulaire d'édition d'une catégorie.
     */
    public function edit(Category $category)
    {
        // Sécurité multi-boutique
        if ($category->boutique_id !== Auth::user()->boutique_id) {
            abort(404, "Catégorie introuvable.");
        }

        // Sécurité rôle : seuls admin/vice-admin peuvent modifier
        if (Auth::user()->role === 'vendeur') {
            abort(403, "Vous n'êtes pas autorisé à modifier cette catégorie.");
        }

        return view('categories.edit', compact('category'));
    }

    /**
     * Met à jour une catégorie existante.
     */
    public function update(Request $request, Category $category)
    {
        // Sécurité multi-boutique
        if ($category->boutique_id !== Auth::user()->boutique_id) {
            abort(404, "Catégorie introuvable.");
        }

        // Sécurité rôle : seuls admin/vice-admin peuvent modifier
        if (Auth::user()->role === 'vendeur') {
            abort(403, "Vous n'êtes pas autorisé à modifier cette catégorie.");
        }

        $validated = $request->validate([
            'name' => [
                'required',
                'string',
                'max:255',
                Rule::unique('categories', 'name')
                    ->where('boutique_id', Auth::user()->boutique_id)
                    ->ignore($category->id)
            ],
            'description' => ['nullable', 'string'],
        ], [
            'name.unique' => 'Une catégorie portant ce nom existe déjà dans votre boutique.',
        ]);

        $category->update([
            'name' => $validated['name'],
            'description' => $validated['description'] ?? null,
            'updated_by' => Auth::id(),
            // boutique_id volontairement absent : une catégorie ne change jamais de boutique
        ]);

        return redirect()->route('categories.index')
                         ->with('success', 'Catégorie mise à jour avec succès.');
    }

    /**
     * Supprime (soft delete) une catégorie.
     */
    public function destroy(Category $category)
    {
        // Sécurité multi-boutique
        if ($category->boutique_id !== Auth::user()->boutique_id) {
            abort(404, "Catégorie introuvable.");
        }

        // Sécurité supplémentaire : seuls les administrateurs et vice-administrateurs peuvent supprimer
        if (!in_array(
            Auth::user()->role,
            ['administrateur', 'vice_administrateur']
        )) {
            abort(
                403,
                "Vous n'avez pas l'autorisation de supprimer cette catégorie."
            );
        }

        $category->delete();

        return redirect()->route('categories.index')
                         ->with('success', 'Catégorie supprimée avec succès.');
    }
}