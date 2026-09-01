<?php

namespace App\Http\Controllers;

use App\Models\Expense;
use App\Models\CashMovement;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class ExpenseController extends Controller
{
    public function index()
    {
        if (Auth::user()->role === 'vendeur') {
            abort(403, "Vous n'êtes pas autorisé à consulter les dépenses.");
        }

        $boutiqueId = Auth::user()->boutique_id;

        $expenses = Expense::where('boutique_id', $boutiqueId)
            ->orderBy('expense_date', 'desc')
            ->paginate(10);

        $totalMonthExpenses = Expense::where('boutique_id', $boutiqueId)
            ->whereYear('expense_date', now()->year)
            ->whereMonth('expense_date', now()->month)
            ->sum('amount');

        return view('expenses.index', compact('expenses', 'totalMonthExpenses'));
    }

    public function store(Request $request)
    {
        if (Auth::user()->role === 'vendeur') {
            abort(403, "Vous n'êtes pas autorisé à enregistrer une dépense.");
        }

        $request->validate([
            'title' => 'required|string|max:255',
            'amount' => 'required|numeric|min:0|max:999999999999999999.99',
            'expense_date' => 'nullable|date',
            'description' => 'nullable|string',
        ], [
            'amount.required' => 'Le montant est obligatoire.',
            'amount.numeric' => 'Le montant doit être un nombre valide.',
            'amount.min' => 'Le montant de la dépense ne peut pas être négatif.',
            'amount.max' => 'Le montant saisi est trop élevé.',
            'title.required' => 'Le libellé de la dépense est obligatoire.',
            'expense_date.date' => 'La date de l’opération n’est pas valide.',
        ]);

        $user = Auth::user();
        $boutiqueId = $user->boutique_id;
        $amount = $request->amount;
        
        // Si l'utilisateur a choisi une date, on la prend, sinon on met la date du jour
        $expenseDate = $request->filled('expense_date') ? $request->expense_date : now()->toDateString();

        DB::transaction(function () use ($request, $user, $boutiqueId, $amount, $expenseDate) {
            $expense = Expense::create([
                'boutique_id' => $boutiqueId,
                'user_id' => $user->id,
                'title' => $request->title,
                'amount' => $amount,
                'expense_date' => $expenseDate,
                'description' => $request->description,
            ]);

            CashMovement::create([
                'boutique_id' => $boutiqueId,
                'user_id' => $user->id,
                'type' => 'out',
                'category' => 'expense',
                'amount' => $amount,
                'motif' => 'Dépense #' . $expense->id . ' : ' . $request->title,
                'is_unjustified' => false,
            ]);
        });

        return redirect()->route('expenses.index')->with('success', 'Dépense enregistrée et caisse mise à jour avec succès.');
    }

    public function destroy(Expense $expense)
    {
        if (Auth::user()->role === 'vendeur') {
            abort(403, "Vous n'êtes pas autorisé à supprimer une dépense.");
        }

        if ($expense->boutique_id !== Auth::user()->boutique_id) {
            abort(403);
        }

        DB::transaction(function () use ($expense) {
            CashMovement::where('boutique_id', $expense->boutique_id)
                ->where('category', 'expense')
                ->where('motif', 'like', 'Dépense #' . $expense->id . ' %')
                ->delete();

            $expense->delete();
        });

        return redirect()->route('expenses.index')->with('success', 'Dépense supprimée et caisse recréditée avec succès.');
    }
}