<?php

namespace App\Http\Controllers;

use App\Models\StockMovement;
use App\Models\Product;
use App\Models\User;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class StockMovementController extends Controller
{
    public function index(Request $request)
    {
        // Récupère la boutique de l'utilisateur connecté
        $boutiqueId = Auth::user()->boutique_id;

        $query = StockMovement::with(['product', 'user', 'boutique'])
            ->where('boutique_id', $boutiqueId);

        // Filtre par recherche textuelle (Nom du produit ou motif)
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('reason', 'like', "%{$search}%")
                  ->orWhereHas('product', function($subQ) use ($search) {
                      $subQ->where('name', 'like', "%{$search}%");
                  });
            });
        }

        // Filtre optionnel par type (entry, sale, adjustment, etc.)
        if ($request->filled('type')) {
            $query->where('type', $request->type);
        }

        // Filtre optionnel par utilisateur
        if ($request->filled('user_id')) {
            $query->where('user_id', $request->user_id);
        }

        // Filtre par date spécifique
        if ($request->filled('date')) {
            $query->whereDate('created_at', $request->date);
        }

        // Récupération paginée avec conservation des paramètres de recherche dans les liens
        $movements = $query->latest()->paginate(15)->withQueryString();

        // Groupement des éléments de la page actuelle par bloc de semaine
        $groupedByWeek = collect($movements->items())->groupBy(function ($movement) {
            $date = $movement->created_at;
            $startOfWeek = $date->copy()->startOfWeek()->format('d/m/Y');
            $endOfWeek = $date->copy()->endOfWeek()->format('d/m/Y');
            return "Semaine du {$startOfWeek} au {$endOfWeek}";
        });

        // Liste des produits et utilisateurs pour les filtres
        $products = Product::where('boutique_id', $boutiqueId)->get();
        $users = User::where('boutique_id', $boutiqueId)->get();

        return view('stock-movements.index', compact('movements', 'groupedByWeek', 'products', 'users'));
    }

    /**
     * Exporte l'historique des mouvements en PDF en respectant les filtres
     */
    public function exportPdf(Request $request)
    {
        $boutiqueId = Auth::user()->boutique_id;

        $query = StockMovement::with(['product', 'user', 'boutique'])
            ->where('boutique_id', $boutiqueId);

        // Appliquer les mêmes filtres que l'index
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('reason', 'like', "%{$search}%")
                  ->orWhereHas('product', function($subQ) use ($search) {
                      $subQ->where('name', 'like', "%{$search}%");
                  });
            });
        }

        if ($request->filled('type')) {
            $query->where('type', $request->type);
        }

        if ($request->filled('user_id')) {
            $query->where('user_id', $request->user_id);
        }

        if ($request->filled('date')) {
            $query->whereDate('created_at', $request->date);
        }

        // Récupérer tous les mouvements correspondants (sans pagination pour le PDF)
        $movements = $query->latest()->get();

        // Groupement global par semaine pour le PDF
        $groupedByWeek = $movements->groupBy(function ($movement) {
            $date = $movement->created_at;
            $startOfWeek = $date->copy()->startOfWeek()->format('d/m/Y');
            $endOfWeek = $date->copy()->endOfWeek()->format('d/m/Y');
            return "Semaine du {$startOfWeek} au {$endOfWeek}";
        });

        // Génération et téléchargement du PDF
        $pdf = Pdf::loadView('stock-movements.pdf', compact('groupedByWeek'));
        return $pdf->download('journal-mouvements-' . date('d-m-Y') . '.pdf');
    }
}