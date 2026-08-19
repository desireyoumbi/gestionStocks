<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function index()
    {
        $boutiqueId = Auth::user()->boutique_id;

        // On récupère quelques chiffres clés pour le tableau de bord
        $totalProducts = Product::where('boutique_id', $boutiqueId)->count();
        $outOfStockCount = Product::where('boutique_id', $boutiqueId)->where('status', 'rupture')->count();
        $totalCategories = Category::where('boutique_id', $boutiqueId)->count();

        // On envoie tout ça à la vue dashboard
        return view('dashboard', compact('totalProducts', 'outOfStockCount', 'totalCategories'));
    }
}