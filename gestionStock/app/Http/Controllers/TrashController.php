<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Movement;
use App\Models\Product;
use App\Models\Supplier;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class TrashController extends Controller
{
    /**
     * Table des modèles gérés par la corbeille, avec leurs métadonnées d'affichage.
     * Clé = identifiant utilisé dans les URLs (?type=...)
     */
    protected function models(): array
    {
        return [
            'products' => ['model' => Product::class, 'label' => 'Produit', 'display' => 'name'],
            'categories' => ['model' => Category::class, 'label' => 'Catégorie', 'display' => 'name'],
            'suppliers' => ['model' => Supplier::class, 'label' => 'Fournisseur', 'display' => 'name'],
            'movements' => ['model' => Movement::class, 'label' => 'Mouvement', 'display' => 'motif'],
            'users' => ['model' => User::class, 'label' => 'Utilisateur', 'display' => 'name'],
        ];
    }

    /**
     * Vérifie que l'utilisateur connecté est bien administrateur, sinon bloque l'accès.
     */
    protected function ensureIsAdmin(): void
    {
        if (Auth::user()->role !== 'administrateur') {
            abort(403, "Seul un administrateur peut accéder à la corbeille.");
        }
    }

    /**
     * Affiche tous les éléments supprimés de la boutique, mélangés et triés par date de suppression.
     */
    public function index()
    {
        $this->ensureIsAdmin();

        $boutiqueId = Auth::user()->boutique_id;
        $items = collect();

        foreach ($this->models() as $type => $config) {
            $records = $config['model']::onlyTrashed()
                ->where('boutique_id', $boutiqueId)
                ->get();

            foreach ($records as $record) {
                $items->push([
                    'type' => $type,
                    'label' => $config['label'],
                    'id' => $record->id,
                    'title' => $record->{$config['display']} ?? "#{$record->id}",
                    'deleted_at' => $record->deleted_at,
                ]);
            }
        }

        // Tri global par date de suppression, du plus récent au plus ancien
        $items = $items->sortByDesc('deleted_at')->values();

        return view('trash.index', compact('items'));
    }

    /**
     * Restaure un élément supprimé.
     */
    public function restore(Request $request, string $type, int $id)
    {
        $this->ensureIsAdmin();

        $models = $this->models();

        if (!array_key_exists($type, $models)) {
            abort(404, "Type d'élément inconnu.");
        }

        $modelClass = $models[$type]['model'];

        $record = $modelClass::onlyTrashed()
            ->where('boutique_id', Auth::user()->boutique_id)
            ->findOrFail($id);

        $record->restore();

        return back()->with('success', $models[$type]['label'] . ' restauré(e) avec succès.');
    }

    /**
     * Supprime définitivement un élément (irréversible).
     */
    public function forceDelete(Request $request, string $type, int $id)
    {
        $this->ensureIsAdmin();

        $models = $this->models();

        if (!array_key_exists($type, $models)) {
            abort(404, "Type d'élément inconnu.");
        }

        $modelClass = $models[$type]['model'];

        $record = $modelClass::onlyTrashed()
            ->where('boutique_id', Auth::user()->boutique_id)
            ->findOrFail($id);

        try {
            $record->forceDelete();
        } catch (\Illuminate\Database\QueryException $e) {
            // Se produit si une contrainte restrictOnDelete() bloque (ex : des produits
            // référencent encore cette catégorie/cet utilisateur)
            return back()->with(
                'error',
                'Impossible de supprimer définitivement : cet élément est encore référencé ailleurs.'
            );
        }

        return back()->with('success', $models[$type]['label'] . ' supprimé(e) définitivement.');
    }
}