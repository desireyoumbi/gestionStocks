<?php

namespace App\Http\Controllers;

use App\Models\Movement;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class MovementController extends Controller
{
    public function index(Request $request)
    {
        $boutiqueId = Auth::user()->boutique_id;

        // 1. Filtrage de base obligatoire par boutique
        $query = Movement::where('boutique_id', $boutiqueId)
            ->with(['products', 'user']);

        // 3. Filtre par type (entree / sortie / ajustement) si demandé
        if ($request->filled('type')) {
            $query->where('type', $request->type);
        }

        // 4. Barre de recherche (motif ou nom du produit lié)
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('motif', 'like', "%{$search}%")
                  ->orWhereHas('products', function($subQ) use ($search) {
                      $subQ->where('name', 'like', "%{$search}%");
                  });
            });
        }

        $movements = $query->latest()->paginate(15)->appends($request->all());

        // 5. Statistiques pour les cartes du haut (scopées à la boutique connectée !)
        $totalEntrees = Movement::where('boutique_id', $boutiqueId)->where('type', 'entree')->count();
        $totalSorties = Movement::where('boutique_id', $boutiqueId)->where('type', 'sortie')->count();
        $totalRecents = Movement::where('boutique_id', $boutiqueId)->whereDate('created_at', today())->count();

        return view('movements.index', compact('movements', 'totalEntrees', 'totalSorties', 'totalRecents'));
    }
}