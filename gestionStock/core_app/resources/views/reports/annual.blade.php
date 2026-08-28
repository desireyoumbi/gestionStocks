@extends('layouts.app')

@section('page-title', 'Bilan et Rapports Annuels')

@section('content')

@php
    // Noms des mois en français pour un affichage propre
    $frenchMonths = [
        1 => 'Janvier', 2 => 'Février', 3 => 'Mars', 4 => 'Avril', 
        5 => 'Mai', 6 => 'Juin', 7 => 'Juillet', 8 => 'Août', 
        9 => 'Septembre', 10 => 'Octobre', 11 => 'Novembre', 12 => 'Décembre'
    ];

    // Calculs globaux pour l'année
    $totalYearRevenue = isset($monthlyStats) ? $monthlyStats->sum('total_revenue') : 0;
    $totalYearProfit = isset($monthlyStats) ? $monthlyStats->sum('total_profit') : 0;
    
    // Variables de caisse et stock
    $initialFund = $initialFund ?? 0;
    $currentCashBalance = $currentCashBalance ?? 0;
    $totalYearExpenses = $totalYearExpenses ?? 0;
    $stockValue = $stockValue ?? 0;
@endphp

<div class="space-y-8">

    <!-- En-tête de la page -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 bg-white border border-slate-200/80 rounded-3xl p-8 shadow-2xl shadow-slate-300/50">
        <div>
            <span class="text-xs font-bold text-amber-600 bg-amber-50 px-2.5 py-1 rounded-lg">Archives & Historique</span>
            <h1 class="text-2xl font-black text-slate-900 tracking-tight mt-2">Bilan Annuel et Mensuel</h1>
            <p class="text-xs font-medium text-slate-400 mt-1">Suivi de la performance globale de votre activité et gestion de la caisse.</p>
        </div>
        
        <!-- Sélecteur d'année & Action Fonds de caisse -->
        <div class="flex items-center gap-3">
            <div class="flex items-center gap-2">
                <span class="text-xs font-bold text-slate-500">Année :</span>
                <span class="bg-slate-900 text-white text-xs font-black px-4 py-2.5 rounded-xl shadow-md">{{ date('Y') }}</span>
            </div>
            <!-- Bouton pour ouvrir le modal d'ajustement du fonds -->
            <button onclick="document.getElementById('modal-adjust-fund').classList.remove('hidden')" class="bg-emerald-500 hover:bg-emerald-600 text-slate-950 text-xs font-bold px-4 py-2.5 rounded-xl shadow-md transition flex items-center gap-1.5">
                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/></svg>
                Gérer le Fonds
            </button>
        </div>
    </div>

    <!-- 1. CARTES DE SYNTHÈSE DE CAISSE & STOCK (5 colonnes) -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-4">
        <!-- Fonds Initial (Opening) -->
        <div class="bg-white border border-slate-200/80 rounded-3xl p-5 shadow-sm flex flex-col justify-between">
            <div class="w-9 h-9 rounded-xl bg-amber-50 text-amber-600 flex items-center justify-center mb-3 text-sm font-bold">🏛️</div>
            <div>
                <p class="text-[11px] font-bold uppercase tracking-wider text-slate-400">Fonds Initial / Capital</p>
                <h3 class="text-lg font-black text-slate-900 mt-1">{{ number_format($initialFund, 0, ',', ' ') }} FCFA</h3>
            </div>
        </div>

        <!-- Solde Réel en Caisse -->
        <div class="bg-white border border-slate-200/80 rounded-3xl p-5 shadow-sm flex flex-col justify-between">
            <div class="w-9 h-9 rounded-xl bg-emerald-50 text-emerald-600 flex items-center justify-center mb-3 text-sm font-bold">💵</div>
            <div>
                <p class="text-[11px] font-bold uppercase tracking-wider text-slate-400">Solde Réel en Caisse</p>
                <h3 class="text-lg font-black {{ $currentCashBalance >= 0 ? 'text-emerald-600' : 'text-rose-600' }} mt-1">
                    {{ number_format($currentCashBalance, 0, ',', ' ') }} FCFA
                </h3>
            </div>
        </div>

        <!-- Valeur du Stock Actuel (Fonds de commerce) -->
        <div class="bg-white border border-slate-200/80 rounded-3xl p-5 shadow-sm flex flex-col justify-between">
            <div class="w-9 h-9 rounded-xl bg-purple-50 text-purple-600 flex items-center justify-center mb-3 text-sm font-bold">📦</div>
            <div>
                <p class="text-[11px] font-bold uppercase tracking-wider text-slate-400">Valeur du Stock</p>
                <h3 class="text-lg font-black text-purple-600 mt-1">{{ number_format($stockValue, 0, ',', ' ') }} FCFA</h3>
            </div>
        </div>

        <!-- Total Dépenses Annuelles -->
        <div class="bg-white border border-slate-200/80 rounded-3xl p-5 shadow-sm flex flex-col justify-between">
            <div class="w-9 h-9 rounded-xl bg-rose-50 text-rose-600 flex items-center justify-center mb-3 text-sm font-bold">OUT</div>
            <div>
                <p class="text-[11px] font-bold uppercase tracking-wider text-slate-400">Total Dépenses (Année)</p>
                <h3 class="text-lg font-black text-rose-600 mt-1">{{ number_format($totalYearExpenses, 0, ',', ' ') }} FCFA</h3>
            </div>
        </div>

        <!-- Chiffre d'Affaires Annuel -->
        <div class="bg-white border border-slate-200/80 rounded-3xl p-5 shadow-sm flex flex-col justify-between">
            <div class="w-9 h-9 rounded-xl bg-blue-50 text-blue-600 flex items-center justify-center mb-3 text-sm font-bold">IN</div>
            <div>
                <p class="text-[11px] font-bold uppercase tracking-wider text-slate-400">Chiffre d'Affaires (Année)</p>
                <h3 class="text-lg font-black text-blue-600 mt-1">{{ number_format($totalYearRevenue, 0, ',', ' ') }} FCFA</h3>
            </div>
        </div>
    </div>

    <!-- Cartes récapitulatives de performance -->
    <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
        <div class="bg-white border border-slate-200/80 rounded-3xl p-6 shadow-2xl shadow-slate-300/50 flex flex-col justify-between">
            <div class="w-10 h-10 rounded-2xl bg-blue-50 flex items-center justify-center mb-4 text-blue-600 shadow-sm">📈</div>
            <div>
                <p class="text-2xl font-black text-slate-900">{{ number_format($totalYearRevenue, 0, ',', ' ') }} FCFA</p>
                <p class="text-xs font-semibold text-slate-400 mt-0.5">Chiffre d'affaires total de l'année</p>
            </div>
        </div>

        <div class="bg-white border border-slate-200/80 rounded-3xl p-6 shadow-2xl shadow-slate-300/50 flex flex-col justify-between">
            <div class="w-10 h-10 rounded-2xl bg-emerald-50 flex items-center justify-center mb-4 text-emerald-600 shadow-sm">💰</div>
            <div>
                <p class="text-2xl font-black {{ $totalYearProfit >= 0 ? 'text-emerald-600' : 'text-red-600' }}">
                    {{ number_format($totalYearProfit, 0, ',', ' ') }} FCFA
                </p>
                <p class="text-xs font-semibold text-slate-400 mt-0.5">Bénéfice net total de l'année</p>
            </div>
        </div>
    </div>

    <!-- Tableau détaillé des mois -->
    <div class="bg-white border border-slate-200/80 rounded-3xl p-8 shadow-2xl shadow-slate-300/50 space-y-6">
        <div class="flex items-center justify-between">
            <div>
                <h2 class="text-base font-black text-slate-900 tracking-tight">Détail mois par mois</h2>
                <p class="text-xs font-medium text-slate-400 mt-0.5">Historique des performances clôturées et en cours</p>
            </div>
        </div>

        @if(isset($monthlyStats) && count($monthlyStats) > 0)
            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse">
                    <thead>
                        <tr class="border-b border-slate-200 text-[11px] font-bold text-slate-400 uppercase tracking-wider">
                            <th class="pb-3">Mois</th>
                            <th class="pb-3 text-right">Chiffre d'affaires</th>
                            <th class="pb-3 text-right">Bénéfice Net</th>
                            <th class="pb-3 text-center">Action / Statut</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 text-xs">
                        @foreach($monthlyStats as $stat)
                            <tr class="hover:bg-slate-50/50 transition">
                                <td class="py-4 font-bold text-slate-800 text-sm">
                                    {{ $frenchMonths[$stat->month] ?? $stat->month }} {{ $stat->year }}
                                </td>
                                <td class="py-4 text-right font-black text-slate-900">
                                    {{ number_format($stat->total_revenue, 0, ',', ' ') }} FCFA
                                </td>
                                <td class="py-4 text-right font-black {{ $stat->total_profit >= 0 ? 'text-emerald-600' : 'text-red-600' }}">
                                    {{ number_format($stat->total_profit, 0, ',', ' ') }} FCFA
                                </td>
                                <td class="py-4 text-center">
                                    @if($stat->year == date('Y') && $stat->month == date('n'))
                                        <span class="inline-block bg-amber-50 text-amber-600 text-[10px] font-bold px-3 py-1.5 rounded-xl">En cours</span>
                                    @else
                                        <span class="inline-block bg-slate-100 text-slate-600 text-[10px] font-bold px-3 py-1.5 rounded-xl">Clôturé</span>
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @else
            <div class="text-center py-12 bg-slate-50 rounded-2xl border border-slate-200/60">
                <p class="text-xs text-slate-400 font-medium">Aucune donnée historique disponible pour le moment.</p>
            </div>
        @endif
    </div>

</div>

<!-- Modal pour Définir / Ajuster le Fonds Initial -->
<div id="modal-adjust-fund" class="{{ $errors->has('amount') ? '' : 'hidden' }} bg-slate-900/50 fixed inset-0 z-50 flex items-center justify-center p-4">
    <div class="bg-white rounded-3xl p-6 w-full max-w-md space-y-4 shadow-2xl">
        <div class="flex justify-between items-center">
            <h3 class="text-base font-black text-slate-900">Définir / Ajuster le Fonds Initial</h3>
            <button onclick="document.getElementById('modal-adjust-fund').classList.add('hidden')" class="text-slate-400 hover:text-slate-600 font-bold">✕</button>
        </div>
        
        <form action="{{ route('cash.fund.store') }}" method="POST" class="space-y-4">
            @csrf
            <div>
                <label class="block text-xs font-bold text-slate-700 mb-1">Montant (FCFA)</label>
                <input type="number" name="amount" required step="any" value="{{ old('amount') }}" placeholder="Ex: 500000" class="w-full rounded-xl border {{ $errors->has('amount') ? 'border-rose-500' : 'border-slate-200' }} px-4 py-2.5 text-xs font-bold text-slate-900 focus:outline-none focus:ring-2 focus:ring-amber-500">
                
                <!-- Message d'erreur de validation pour le montant -->
                @error('amount')
                    <p class="mt-1 text-xs text-rose-500 font-semibold flex items-center gap-1">
                        <svg class="w-3.5 h-3.5 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                        {{ $message }}
                    </p>
                @enderror
            </div>

            <div>
                <label class="block text-xs font-bold text-slate-700 mb-1">Motif / Description</label>
                <input type="text" name="motif" value="{{ old('motif') }}" placeholder="Ex: Capital de départ / Injection de trésorerie" class="w-full rounded-xl border border-slate-200 px-4 py-2.5 text-xs font-bold text-slate-900 focus:outline-none focus:ring-2 focus:ring-amber-500">
                @error('motif')
                    <p class="mt-1 text-xs text-rose-500 font-semibold">{{ $message }}</p>
                @enderror
            </div>

            <div class="flex justify-end gap-2 pt-2">
                <button type="button" onclick="document.getElementById('modal-adjust-fund').classList.add('hidden')" class="px-4 py-2.5 rounded-xl bg-slate-100 text-slate-600 text-xs font-bold">Annuler</button>
                <button type="submit" class="px-4 py-2.5 rounded-xl bg-slate-900 text-white text-xs font-bold shadow-md">Enregistrer</button>
            </div>
        </form>
    </div>
</div>

@endsection