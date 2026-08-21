<?php

namespace App\Http\Controllers;

use App\Models\Movement;
use Illuminate\Http\Request;

class MovementController extends Controller
{
    public function index(Request $request)
    {
        // CORRECTION ICI : on utilise 'products' au pluriel
        $query = Movement::with(['products', 'user']);

        // Par défaut, on affiche les mouvements du jour
        if (!$request->has('search') && !$request->has('type')) {
            $query->whereDate('created_at', today());
        }

        // Filtre par type (entree / sortie) si demandé
        if ($request->filled('type')) {
            $query->where('type', $request->type);
        }

        // Barre de recherche
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('motif', 'like', "%{$search}%")
                  ->orWhereHas('products', function($subQ) use ($search) {
                      $subQ->where('name', 'like', "%{$search}%");
                  });
            });
        }

        $movements = $query->latest()->paginate(15);

        // Statistiques pour les cartes du haut
        // Note: Pour sommer les quantités via la table pivot, on utilise la relation ou un join si nécessaire, 
        // mais pour l'instant un simple count ou une adaptation suffit si besoin.
        $totalEntrees = Movement::where('type', 'entree')->count();
        $totalSorties = Movement::where('type', 'sortie')->count();
        $totalRecents = Movement::whereDate('created_at', today())->count();

        return view('movements.index', compact('movements', 'totalEntrees', 'totalSorties', 'totalRecents'));
    }
}