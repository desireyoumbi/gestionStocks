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

        // Mouvements de vente ('sortie') - Exclusion des ventes annulées
        $salesQuery = Movement::with('products')
            ->where('boutique_id', $boutiqueId)
            ->where('type', 'sortie')
            ->whereNull('annule_at');

        $now = Carbon::now();

        // Collections par période
        $salesWeekCollection = (clone $salesQuery)->whereBetween('created_at', [$now->copy()->startOfWeek(), $now->copy()->endOfWeek()])->get();
        $salesMonthCollection = (clone $salesQuery)->whereYear('created_at', $now->year)->whereMonth('created_at', $now->month)->get();

        // Fonctions de calcul basées sur le prix réel de la transaction (pivot unit_price)
        $calculateCA = function ($movements) {
            $total = 0;
            foreach ($movements as $movement) {
                foreach ($movement->products as $product) {
                    $qty = abs($product->pivot->qty ?? 0);
                    $unitPrice = $product->pivot->unit_price ?? $product->selling_price ?? 0;
                    $total += $qty * $unitPrice;
                }
            }
            return $total;
        };

        $calculateProfit = function ($movements) {
            $grossProfit = 0;
            foreach ($movements as $movement) {
                foreach ($movement->products as $product) {
                    $qty = abs($product->pivot->qty ?? 0);
                    $unitPrice = $product->pivot->unit_price ?? $product->selling_price ?? 0;
                    $purchase_price = $product->purchase_price ?? 0;
                    $grossProfit += $qty * ($unitPrice - $purchase_price);
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

        // --- STATISTIQUES SUPPLÉMENTAIRES POUR LES CARTES ---
        $totalArticlesVendus = 0;
        foreach ($salesMonthCollection as $movement) {
            foreach ($movement->products as $product) {
                $totalArticlesVendus += abs($product->pivot->qty ?? 0);
            }
        }

        $totalVentesMois = $salesMonthCollection->count();
        $panierMoyenVal = $totalVentesMois > 0 ? ($caMonth / $totalVentesMois) : 0;
        $panierMoyen = number_format($panierMoyenVal, 0, ',', ' ') . ' FCFA';

        // --- DÉTAIL DES VENTES ET GAINS PAR PRODUIT (pour le mois en cours) ---
        $productSalesStats = [];
        foreach ($salesMonthCollection as $movement) {
            foreach ($movement->products as $product) {
                $qty = abs($product->pivot->qty ?? 0);
                $unitPrice = $product->pivot->unit_price ?? $product->selling_price ?? 0;
                $revenue = $qty * $unitPrice;
                $profit = $qty * ($unitPrice - ($product->purchase_price ?? 0));

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

        // Produit le plus vendu de la semaine / mois
        $topProductWeek = $salesWeekCollection->flatMap->products
            ->groupBy('id')
            ->map(fn($group) => $group->sum(fn($p) => abs($p->pivot->qty)))
            ->sortDesc()
            ->keys()
            ->first();

        $bestProductObj = $topProductWeek ? Product::find($topProductWeek) : null;
        $produitPhare = $bestProductObj ? $bestProductObj->name : 'Aucun';

        // --- RÉCUPÉRATION DE LA DERNIÈRE ACTIVITÉ ---
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
            'produitPhare',
            'totalArticlesVendus',
            'panierMoyen',
            'productSalesStats',
            'latestActivity'
        ));
    }

    public function annualReport()
    {
        $user = auth()->user();
        $boutiqueId = $user->boutique_id;
        $boutique = $user->boutique;

        $initialFund = \App\Models\CashMovement::where('boutique_id', $boutiqueId)
            ->where(function ($q) {
                $q->where('type', 'opening')->orWhere('category', 'initial');
            })
            ->sum('amount');

        $currentCashBalance = $boutique ? $boutique->getCashBalance() : 0;

        $totalYearExpenses = \App\Models\Expense::where('boutique_id', $boutiqueId)
            ->whereYear('expense_date', date('Y'))
            ->sum('amount');

        $stockValue = \App\Models\Product::where('boutique_id', $boutiqueId)
            ->select(DB::raw('SUM(quantity * purchase_price) as total_value'))
            ->value('total_value') ?? 0;

        $driver = DB::connection()->getDriverName();

        if ($driver === 'sqlite') {
            $monthExpr = "CAST(strftime('%m', movements.created_at) AS INTEGER)";
            $yearExpr = "CAST(strftime('%Y', movements.created_at) AS INTEGER)";
        } elseif ($driver === 'pgsql') {
            $monthExpr = "EXTRACT(MONTH FROM movements.created_at)";
            $yearExpr = "EXTRACT(YEAR FROM movements.created_at)";
        } else {
            $monthExpr = 'MONTH(movements.created_at)';
            $yearExpr = 'YEAR(movements.created_at)';
        }

        $monthlyStats = DB::table('movements')
            ->join('movement_products', 'movements.id', '=', 'movement_products.movement_id')
            ->join('products', 'movement_products.product_id', '=', 'products.id')
            ->where('movements.boutique_id', $boutiqueId)
            ->where('movements.type', 'sortie')
            ->whereNull('movements.annule_at')
            ->whereYear('movements.created_at', date('Y'))
            ->select(
                DB::raw("{$monthExpr} as month"),
                DB::raw("{$yearExpr} as year"),
                DB::raw('SUM(ABS(movement_products.qty) * movement_products.unit_price) as total_revenue'),
                DB::raw('SUM(ABS(movement_products.qty) * (movement_products.unit_price - products.purchase_price)) as total_profit')
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