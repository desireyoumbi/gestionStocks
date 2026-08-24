<?php

namespace App\Http\Controllers;

use App\Models\Expense;
use App\Models\CashMovement; // Import du modèle de caisse
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB; // Import de la façade DB

class ExpenseController extends Controller
{
    // Afficher la liste des dépenses de la boutique
    public function index()
    {
        $boutiqueId = Auth::user()->boutique_id;

        $expenses = Expense::where('boutique_id', $boutiqueId)
            ->orderBy('expense_date', 'desc')
            ->paginate(10);

        // Total des dépenses du mois en cours pour information rapide
        $totalMonthExpenses = Expense::where('boutique_id', $boutiqueId)
            ->whereYear('expense_date', now()->year)
            ->whereMonth('expense_date', now()->month)
            ->sum('amount');

        return view('expenses.index', compact('expenses', 'totalMonthExpenses'));
    }

    // Enregistrer une nouvelle dépense et impacter la caisse
    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'amount' => 'required|numeric|min:0',
            'description' => 'nullable|string',
        ]);

        $user = Auth::user();
        $boutique = $user->boutique;
        $amount = $request->amount;
        $today = now()->toDateString(); // Date du jour automatique

        // 1. Vérifier le solde de la caisse de la boutique
        $currentCashBalance = $boutique ? $boutique->getCashBalance() : 0;

        // 2. BLOQUER si le solde est insuffisant (et déclencher ton modal d'erreur de caisse)
        if ($amount > $currentCashBalance) {
            return redirect()->back()
                ->withInput()
                ->with('cash_error', 'Opération bloquée : Fonds insuffisants en caisse (' . number_format($currentCashBalance, 0, ',', ' ') . ' FCFA disponibles pour une dépense de ' . number_format($amount, 0, ',', ' ') . ' FCFA). Veuillez ajuster la caisse.');
        }

        DB::transaction(function () use ($request, $user, $amount, $today) {
            $boutiqueId = $user->boutique_id;

            // 3. Enregistrer la dépense avec la date du jour automatique
            $expense = Expense::create([
                'boutique_id' => $boutiqueId,
                'user_id' => $user->id,
                'title' => $request->title,
                'amount' => $amount,
                'expense_date' => $today, // Forcé à aujourd'hui
                'description' => $request->description,
            ]);

            // 4. Enregistrer la sortie d'argent correspondante dans les mouvements de caisse avec un motif unique
            CashMovement::create([
                'boutique_id' => $boutiqueId,
                'user_id' => $user->id,
                'type' => 'out',
                'category' => 'expense', // Catégorie dépense
                'amount' => $amount,
                'motif' => 'Dépense #' . $expense->id . ' : ' . $request->title,
                'is_unjustified' => false, // Désormais justifié puisque bloqué si insuffisant
            ]);
        });

        return redirect()->route('expenses.index')->with('success', 'Dépense enregistrée et caisse mise à jour avec succès.');
    }

    // Supprimer une dépense
    public function destroy(Expense $expense)
    {
        // Sécurité : s'assurer que la dépense appartient bien à la boutique de l'utilisateur
        if ($expense->boutique_id !== Auth::user()->boutique_id) {
            abort(403);
        }

        DB::transaction(function () use ($expense) {
            // Supprimer le mouvement de caisse exact lié à cette dépense précise grâce à son ID
            CashMovement::where('boutique_id', $expense->boutique_id)
                ->where('category', 'expense')
                ->where('motif', 'like', 'Dépense #' . $expense->id . ' %')
                ->delete();

            $expense->delete();
        });

        return redirect()->route('expenses.index')->with('success', 'Dépense supprimée et caisse recréditée avec succès.');
    }
}