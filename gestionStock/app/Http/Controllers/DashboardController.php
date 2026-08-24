<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Category;
use App\Models\Movement;
use App\Models\Expense;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        $boutiqueId = Auth::user()->boutique_id;

        // Stock
        $totalProducts = Product::where('boutique_id', $boutiqueId)->count();
        $outOfStockCount = Product::where('boutique_id', $boutiqueId)->where('quantity', 0)->count();
        $lowStockCount = Product::where('boutique_id', $boutiqueId)->where('quantity', '>', 0)->whereColumn('quantity', '<=', 'alert_threshold')->count();
        $availableCount = Product::where('boutique_id', $boutiqueId)->whereColumn('quantity', '>', 'alert_threshold')->count();
        $totalCategories = Category::where('boutique_id', $boutiqueId)->count();

        $lowStockProducts = Product::where('boutique_id', $boutiqueId)
            ->where('quantity', '>', 0)
            ->whereColumn('quantity', '<=', 'alert_threshold')
            ->orderBy('quantity', 'asc')
            ->get();

        // Mouvements de vente ('sortie')
        $salesQuery = Movement::with('products')
            ->where('boutique_id', $boutiqueId)
            ->where('type', 'sortie');

        $now = Carbon::now();

        // Collections par période
        $salesWeekCollection = (clone $salesQuery)->whereBetween('created_at', [$now->copy()->startOfWeek(), $now->copy()->endOfWeek()])->get();
        $salesMonthCollection = (clone $salesQuery)->whereYear('created_at', $now->year)->whereMonth('created_at', $now->month)->get();

        // Fonctions de calcul
        $calculateCA = function ($movements) {
            $total = 0;
            foreach ($movements as $movement) {
                foreach ($movement->products as $product) {
                    $qty = abs($product->pivot->qty ?? 0);
                    $sellingPrice = $product->selling_price ?? 0;
                    $total += $qty * $sellingPrice;
                }
            }
            return $total;
        };

        $calculateProfit = function ($movements) {
            $grossProfit = 0;
            foreach ($movements as $movement) {
                foreach ($movement->products as $product) {
                    $qty = abs($product->pivot->qty ?? 0);
                    $sellingPrice = $product->selling_price ?? 0;
                    $purchase_price = $product->purchase_price ?? 0;
                    $grossProfit += $qty * ($sellingPrice - $purchase_price);
                }
            }
            return $grossProfit;
        };

        // Totaux CA
        $caWeek = $calculateCA($salesWeekCollection);
        $caMonth = $calculateCA($salesMonthCollection);

        // Dépenses
        $expenseQuery = Expense::where('boutique_id', $boutiqueId);
        $expensesWeek = (clone $expenseQuery)->whereBetween('expense_date', [$now->copy()->startOfWeek(), $now->copy()->endOfWeek()])->sum('amount');
        $expensesMonth = (clone $expenseQuery)->whereYear('expense_date', $now->year)->whereMonth('expense_date', $now->month)->sum('amount');

        // Bénéfices bruts et nets
        $grossProfitWeek = $calculateProfit($salesWeekCollection);
        $grossProfitMonth = $calculateProfit($salesMonthCollection);

        $netProfitWeek = $grossProfitWeek - $expensesWeek;
        $netProfitMonth = $grossProfitMonth - $expensesMonth;

        // --- DÉTAIL DES VENTES PAR PRODUIT (pour le mois en cours) ---
        $productSalesStats = [];
        foreach ($salesMonthCollection as $movement) {
            foreach ($movement->products as $product) {
                $qty = abs($product->pivot->qty ?? 0);
                $revenue = $qty * ($product->selling_price ?? 0);
                $profit = $qty * (($product->selling_price ?? 0) - ($product->purchase_price ?? 0));

                if (!isset($productSalesStats[$product->id])) {
                    $productSalesStats[$product->id] = [
                        'name' => $product->name,
                        'total_qty' => 0,
                        'total_revenue' => 0,
                        'total_profit' => 0,
                    ];
                }
                $productSalesStats[$product->id]['total_qty'] += $qty;
                $productSalesStats[$product->id]['total_revenue'] += $revenue;
                $productSalesStats[$product->id]['total_profit'] += $profit;
            }
        }

        // Produit le plus vendu de la semaine
        $topProductWeek = $salesWeekCollection->flatMap->products
            ->groupBy('id')
            ->map(fn($group) => $group->sum(fn($p) => abs($p->pivot->qty)))
            ->sortDesc()
            ->keys()
            ->first();

        $bestProduct = $topProductWeek ? Product::find($topProductWeek) : null;

        // --- RÉCUPÉRATION DE LA DERNIÈRE ACTIVITÉ (Sécurisé par boutique) ---
        $latestActivity = Movement::where('boutique_id', $boutiqueId)
            ->with('products')
            ->latest()
            ->first();

        return view('dashboard', compact(
            'totalProducts',
            'outOfStockCount',
            'lowStockCount',
            'availableCount',
            'totalCategories',
            'lowStockProducts',
            'caWeek',
            'caMonth',
            'expensesWeek',
            'expensesMonth',
            'netProfitWeek',
            'netProfitMonth',
            'bestProduct',
            'productSalesStats',
            'latestActivity' // <- Ajouté ici pour la vue
        ));
    }

    public function annualReport()
{
    $user = auth()->user();
    $boutiqueId = $user->boutique_id;
    $boutique = $user->boutique;

    // 1. Fonds initial de la boutique
    $initialFund = \App\Models\CashMovement::where('boutique_id', $boutiqueId)
        ->where(function($q) {
            $q->where('type', 'opening')->orWhere('category', 'initial');
        })
        ->sum('amount');

    // 2. Solde réel actuel en caisse
    $currentCashBalance = $boutique ? $boutique->getCashBalance() : 0;

    // 3. Total des dépenses de l'année en cours
    $totalYearExpenses = \App\Models\Expense::where('boutique_id', $boutiqueId)
        ->whereYear('expense_date', date('Y'))
        ->sum('amount');

    // 4. Valeur totale du stock actuel (Quantité * Prix d'achat)
    $stockValue = \App\Models\Product::where('boutique_id', $boutiqueId)
        ->select(DB::raw('SUM(quantity * purchase_price) as total_value'))
        ->value('total_value') ?? 0;

    // 5. Statistiques de vente par mois
    $monthlyStats = DB::table('movements')
        ->join('movement_products', 'movements.id', '=', 'movement_products.movement_id')
        ->join('products', 'movement_products.product_id', '=', 'products.id')
        ->where('movements.boutique_id', $boutiqueId)
        ->where('movements.type', 'sortie')
        ->whereYear('movements.created_at', date('Y'))
        ->select(
            DB::raw('MONTH(movements.created_at) as month'),
            DB::raw('YEAR(movements.created_at) as year'),
            DB::raw('SUM(ABS(movement_products.qty) * products.selling_price) as total_revenue'),
            DB::raw('SUM(ABS(movement_products.qty) * (products.selling_price - products.purchase_price)) as total_profit')
        )
        ->groupBy('year', 'month')
        ->orderBy('month', 'DESC')
        ->get();

    return view('reports.annual', compact(
        'initialFund',
        'currentCashBalance',
        'totalYearExpenses',
        'stockValue',
        'monthlyStats'
    ));
}
}