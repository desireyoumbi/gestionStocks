@extends('layouts.app')

@section('page-title', 'Gestion des Dépenses')

@section('content')

<div class="space-y-8">

    <!-- En-tête de page -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h1 class="text-2xl font-black text-slate-900 tracking-tight">Dépenses & Charges</h1>
            <p class="text-xs font-medium text-slate-400 mt-0.5">Suivi des décaissements et frais de fonctionnement de la boutique</p>
        </div>
        <div class="flex items-center gap-3">
            <div class="bg-amber-50 border border-amber-200/60 px-4 py-2.5 rounded-2xl flex items-center gap-3">
                <span class="text-xs font-bold text-amber-800">Total du mois :</span>
                <span class="text-sm font-black text-amber-900">{{ number_format($totalMonthExpenses, 0, ',', ' ') }} FCFA</span>
            </div>
        </div>
    </div>

    @if(session('success'))
        <div class="bg-emerald-50 border border-emerald-200 text-emerald-800 px-5 py-3.5 rounded-2xl text-xs font-bold shadow-sm">
            {{ session('success') }}
        </div>
    @endif

    <div class="grid grid-cols-1 xl:grid-cols-3 gap-8 items-start">

        <!-- FORMULAIRE D'AJOUT (1/3 de l'écran) -->
        <div class="bg-white border border-slate-200/80 rounded-3xl p-6 shadow-2xl shadow-slate-300/50">
            <h2 class="text-base font-black text-slate-900 tracking-tight mb-4">Nouvelle dépense</h2>

            <form action="{{ route('expenses.store') }}" method="POST" class="space-y-4">
                @csrf

                <div>
                    <label class="block text-xs font-bold text-slate-700 mb-1">Motif / Intitulé <span class="text-red-500">*</span></label>
                    <input type="text" name="title" required placeholder="Ex: Loyer, Transport, Électricité..." 
                           class="w-full bg-slate-50 border border-slate-200 rounded-xl px-4 py-3 text-xs font-medium text-slate-800 focus:outline-none focus:border-amber-500 transition">
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-700 mb-1">Montant (FCFA) <span class="text-red-500">*</span></label>
                    <input type="number" step="any" name="amount" required placeholder="Ex: 25000" 
                           class="w-full bg-slate-50 border border-slate-200 rounded-xl px-4 py-3 text-xs font-medium text-slate-800 focus:outline-none focus:border-amber-500 transition">
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-700 mb-1">Date de la dépense <span class="text-red-500">*</span></label>
                    <input type="date" name="expense_date" value="{{ date('Y-m-d') }}" required 
                           class="w-full bg-slate-50 border border-slate-200 rounded-xl px-4 py-3 text-xs font-medium text-slate-800 focus:outline-none focus:border-amber-500 transition">
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-700 mb-1">Description (Optionnel)</label>
                    <textarea name="description" rows="3" placeholder="Précisions supplémentaires..." 
                              class="w-full bg-slate-50 border border-slate-200 rounded-xl px-4 py-3 text-xs font-medium text-slate-800 focus:outline-none focus:border-amber-500 transition"></textarea>
                </div>

                <button type="submit" class="w-full bg-slate-900 hover:bg-slate-800 text-white font-bold text-xs py-3.5 rounded-xl transition shadow-lg">
                    Enregistrer la dépense
                </button>
            </form>
        </div>

        <!-- LISTE DES DÉPENSES (2/3 de l'écran) -->
        <div class="xl:col-span-2 bg-white border border-slate-200/80 rounded-3xl p-6 shadow-2xl shadow-slate-300/50">
            <h2 class="text-base font-black text-slate-900 tracking-tight mb-4">Historique des dépenses</h2>

            @if($expenses->count() > 0)
                <div class="overflow-x-auto">
                    <table class="w-full text-left border-collapse">
                        <thead>
                            <tr class="border-b border-slate-100 text-[11px] font-bold text-slate-400 uppercase tracking-wider">
                                <th class="py-3 px-4">Date</th>
                                <th class="py-3 px-4">Intitulé</th>
                                <th class="py-3 px-4">Montant</th>
                                <th class="py-3 px-4">Auteur</th>
                                <th class="py-3 px-4 text-right">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100 text-xs font-medium text-slate-700">
                            @foreach($expenses as $expense)
                                <tr class="hover:bg-slate-50/55 transition">
                                    <td class="py-3.5 px-4 text-slate-500 font-semibold">
                                        {{ $expense->expense_date->format('d/m/Y') }}
                                    </td>
                                    <td class="py-3.5 px-4 font-bold text-slate-900">
                                        {{ $expense->title }}
                                        @if($expense->description)
                                            <p class="text-[10px] text-slate-400 font-normal mt-0.5">{{ $expense->description }}</p>
                                        @endif
                                    </td>
                                    <td class="py-3.5 px-4 font-black text-amber-600">
                                        {{ number_format($expense->amount, 0, ',', ' ') }} FCFA
                                    </td>
                                    <td class="py-3.5 px-4 text-slate-500">
                                        {{ $expense->user->name ?? 'N/A' }}
                                    </td>
                                    <td class="py-3.5 px-4 text-right">
                                        <form action="{{ route('expenses.destroy', $expense->id) }}" method="POST" onsubmit="return confirm('Voulez-vous vraiment supprimer cette dépense ?');" class="inline">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="w-8 h-8 rounded-xl bg-red-50 text-red-500 hover:bg-red-100 flex items-center justify-center transition ml-auto">
                                                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                                    <path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                                </svg>
                                            </button>
                                        </form>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <div class="mt-4">
                    {{ $expenses->links() }}
                </div>
            @else
                <div class="text-center py-12 bg-slate-50 rounded-2xl border border-slate-100">
                    <p class="text-xs text-slate-400 font-medium">Aucune dépense enregistrée pour le moment.</p>
                </div>
            @endif
        </div>

    </div>

</div>

@endsection