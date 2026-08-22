<?php

namespace App\Http\Controllers;

use App\Models\Expense;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

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

    // Enregistrer une nouvelle dépense
    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'amount' => 'required|numeric|min:0',
            'expense_date' => 'required|date',
            'description' => 'nullable|string',
        ]);

        Expense::create([
            'boutique_id' => Auth::user()->boutique_id,
            'user_id' => Auth::id(),
            'title' => $request->title,
            'amount' => $request->amount,
            'expense_date' => $request->expense_date,
            'description' => $request->description,
        ]);

        return redirect()->route('expenses.index')->with('success', 'Dépense enregistrée avec succès.');
    }

    // Supprimer une dépense
    public function destroy(Expense $expense)
    {
        // Sécurité : s'assurer que la dépense appartient bien à la boutique de l'utilisateur
        if ($expense->boutique_id !== Auth::user()->boutique_id) {
            abort(403);
        }

        $expense->delete();

        return redirect()->route('expenses.index')->with('success', 'Dépense supprimée avec succès.');
    }
}