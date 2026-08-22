<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Category;
use App\Models\StockMovement;
use App\Models\Expense;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        $boutiqueId = Auth::user()->boutique_id;

        // 1. Chiffres de base actuels
        $totalProducts = Product::where('boutique_id', $boutiqueId)->count();
        $outOfStockCount = Product::where('boutique_id', $boutiqueId)->where('status', 'rupture')->count();
        $totalCategories = Category::where('boutique_id', $boutiqueId)->count();

        // 2. Récupération des mouvements de vente pour la boutique
        $salesQuery = StockMovement::with('product')
            ->where('boutique_id', $boutiqueId)
            ->where('type', 'sale');

        // Périodes courantes (Semaine, Mois, Année)
        $now = Carbon::now();
        
        // --- CHIFFRE D'AFFAIRES (Ventes) ---
        $caWeek = (clone $salesQuery)->whereBetween('created_at', [$now->copy()->startOfWeek(), $now->copy()->endOfWeek()])
            ->get()->sum(fn($m) => $m->quantity * ($m->product->selling_price ?? 0));

        $caMonth = (clone $salesQuery)->whereYear('created_at', $now->year)->whereMonth('created_at', $now->month)
            ->get()->sum(fn($m) => $m->quantity * ($m->product->selling_price ?? 0));

        $caYear = (clone $salesQuery)->whereYear('created_at', $now->year)
            ->get()->sum(fn($m) => $m->quantity * ($m->product->selling_price ?? 0));


        // --- DÉPENSES ---
        $expenseQuery = Expense::where('boutique_id', $boutiqueId);

        $expensesWeek = (clone $expenseQuery)->whereBetween('expense_date', [$now->copy()->startOfWeek(), $now->copy()->endOfWeek()])->sum('amount');
        $expensesMonth = (clone $expenseQuery)->whereYear('expense_date', $now->year)->whereMonth('expense_date', $now->month)->sum('amount');
        $expensesYear = (clone $expenseQuery)->whereYear('expense_date', $now->year)->sum('amount');


        // --- BÉNÉFICES (CA - Coût d'achat - Dépenses) ---
        // Fonction de calcul du bénéfice brut pour une collection de ventes
        $calculateProfit = function($salesCollection) {
            $grossProfit = 0;
            foreach ($salesCollection as $sale) {
                if ($sale->product) {
                    $sellingPrice = $sale->product->selling_price ?? 0;
                    $buyingPrice = $sale->product->buying_price ?? 0;
                    $grossProfit += $sale->quantity * ($sellingPrice - $buyingPrice);
                }
            }
            return $grossProfit;
        };

        $salesWeekCollection = (clone $salesQuery)->whereBetween('created_at', [$now->copy()->startOfWeek(), $now->copy()->endOfWeek()])->get();
        $salesMonthCollection = (clone $salesQuery)->whereYear('created_at', $now->year)->whereMonth('created_at', $now->month)->get();
        $salesYearCollection = (clone $salesQuery)->whereYear('created_at', $now->year)->get();

        $grossProfitWeek = $calculateProfit($salesWeekCollection);
        $grossProfitMonth = $calculateProfit($salesMonthCollection);
        $grossProfitYear = $calculateProfit($salesYearCollection);

        // Bénéfice Net = Bénéfice Brut - Dépenses de la période
        $netProfitWeek = $grossProfitWeek - $expensesWeek;
        $netProfitMonth = $grossProfitMonth - $expensesMonth;
        $netProfitYear = $grossProfitYear - $expensesYear;


        // --- PRODUIT LE PLUS VENDU DE LA SEMAINE ---
        $topProductWeek = $salesWeekCollection->groupBy('product_id')
            ->map(fn($group) => $group->sum('quantity'))
            ->sortDesc()
            ->keys()
            ->first();
        
        $bestProduct = $topProductWeek ? Product::find($topProductWeek) : null;


        return view('dashboard', compact(
            'totalProducts', 
            'outOfStockCount', 
            'totalCategories',
            'caWeek', 'caMonth', 'caYear',
            'expensesWeek', 'expensesMonth', 'expensesYear',
            'netProfitWeek', 'netProfitMonth', 'netProfitYear',
            'bestProduct'
        ));
    }
}