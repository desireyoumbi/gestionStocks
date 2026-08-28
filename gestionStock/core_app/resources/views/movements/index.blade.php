@extends('layouts.app')

@section('header_subtitle', 'Journal des mouvements de stock')
@section('header_title', 'Mouvements de stock')

@section('content')
<div class="space-y-6">

    <!-- 1. CARTES STATISTIQUES -->
    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
        <div class="bg-white dark:bg-gray-950 border border-gray-100 dark:border-gray-800 rounded-2xl p-5 flex items-center gap-4 shadow-sm">
            <div class="w-12 h-12 rounded-xl bg-emerald-500/10 text-emerald-500 flex items-center justify-center shrink-0">
                <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6" /></svg>
            </div>
            <div>
                <p class="text-[11px] font-bold uppercase tracking-wider text-gray-400 dark:text-gray-500">Total Entrées</p>
                <h3 class="text-2xl font-black text-gray-900 dark:text-white">{{ $totalEntrees ?? 62 }}</h3>
            </div>
        </div>

        <div class="bg-white dark:bg-gray-950 border border-gray-100 dark:border-gray-800 rounded-2xl p-5 flex items-center gap-4 shadow-sm">
            <div class="w-12 h-12 rounded-xl bg-rose-500/10 text-rose-500 flex items-center justify-center shrink-0">
                <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M13 17h8m0 0V9m0 8l-8-8-4 4-6-6" /></svg>
            </div>
            <div>
                <p class="text-[11px] font-bold uppercase tracking-wider text-gray-400 dark:text-gray-500">Total Sorties</p>
                <h3 class="text-2xl font-black text-gray-900 dark:text-white">{{ $totalSorties ?? 1 }}</h3>
            </div>
        </div>

        <div class="bg-white dark:bg-gray-950 border border-gray-100 dark:border-gray-800 rounded-2xl p-5 flex items-center gap-4 shadow-sm">
            <div class="w-12 h-12 rounded-xl bg-amber-500/10 text-amber-500 flex items-center justify-center shrink-0">
                <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
            </div>
            <div>
                <p class="text-[11px] font-bold uppercase tracking-wider text-gray-400 dark:text-gray-500">Mouvements Récents</p>
                <h3 class="text-2xl font-black text-gray-900 dark:text-white">{{ $totalRecents ?? 3 }}</h3>
            </div>
        </div>
    </div>

    <!-- 2. BARRE DE RECHERCHE ET FILTRES -->
    <div class="bg-white dark:bg-gray-950 border border-gray-100 dark:border-gray-800 rounded-2xl p-4 shadow-sm">
        <form method="GET" action="{{ route('movements.index') }}" class="w-full flex flex-col sm:flex-row items-center gap-3">
            <div class="relative w-full sm:w-80">
                <span class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-gray-400">
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" /></svg>
                </span>
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Rechercher un produit, motif..." class="w-full pl-9 pr-4 py-2.5 bg-gray-50 dark:bg-gray-900 border border-gray-200 dark:border-gray-800 rounded-xl text-xs text-gray-800 dark:text-gray-200 focus:outline-none focus:border-amber-500 transition">
            </div>

            <!-- AJOUT DE L'OPTION AJUSTEMENT DANS LE FILTRE -->
            <select name="type" onchange="this.form.submit()" class="w-full sm:w-auto px-4 py-2.5 bg-gray-50 dark:bg-gray-900 border border-gray-200 dark:border-gray-800 rounded-xl text-xs font-medium text-gray-700 dark:text-gray-300 focus:outline-none focus:border-amber-500 transition">
                <option value="">Tous les types</option>
                <option value="entree" {{ request('type') == 'entree' ? 'selected' : '' }}>Entrées</option>
                <option value="sortie" {{ request('type') == 'sortie' ? 'selected' : '' }}>Sorties</option>
                <option value="ajustement" {{ request('type') == 'ajustement' ? 'selected' : '' }}>Ajustements</option>
            </select>
        </form>
    </div>

    <!-- 3. TABLEAU DES MOUVEMENTS -->
    <div class="bg-white dark:bg-gray-950 border border-gray-100 dark:border-gray-800 rounded-2xl overflow-hidden shadow-sm">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="border-b border-gray-100 dark:border-gray-900 text-[11px] font-bold text-gray-400 dark:text-gray-500 uppercase tracking-wider bg-gray-50/50 dark:bg-gray-900/50">
                        <th class="py-3.5 px-6">Date & Heure</th>
                        <th class="py-3.5 px-6">Nom du produit</th>
                        <th class="py-3.5 px-6">Type</th>
                        <th class="py-3.5 px-6">Quantité</th>
                        <th class="py-3.5 px-6">Auteur</th>
                        <th class="py-3.5 px-6">Commentaire / Motif</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 dark:divide-gray-900 text-xs font-medium text-gray-700 dark:text-gray-300">
                    @forelse($movements ?? [] as $movement)
                        @php 
                            $pivotQty = $movement->products->first()->pivot->qty ?? 0; 
                        @endphp
                        <tr class="hover:bg-gray-50/50 dark:hover:bg-gray-900/40 transition">
                            <td class="py-4 px-6 text-gray-500 dark:text-gray-400">{{ $movement->created_at->format('d/m/Y H:i') }}</td>
                            
                            <td class="py-4 px-6 font-bold text-gray-900 dark:text-white">
                                {{ $movement->products->first()->name ?? 'Produit supprimé' }}
                            </td>

                            <!-- GESTION PROPRE DES BADGES (ENTRÉE, SORTIE, AJUSTEMENT) -->
                            <td class="py-4 px-6">
                                @if($movement->type === 'ajustement')
                                    <span class="inline-flex items-center px-2.5 py-1 rounded-lg text-[11px] font-bold bg-amber-500/10 text-amber-500">Ajustement</span>
                                @elseif($movement->type === 'entree')
                                    <span class="inline-flex items-center px-2.5 py-1 rounded-lg text-[11px] font-bold bg-emerald-500/10 text-emerald-500">Entrée</span>
                                @else
                                    <span class="inline-flex items-center px-2.5 py-1 rounded-lg text-[11px] font-bold bg-rose-500/10 text-rose-500">Sortie</span>
                                @endif
                            </td>

                            <!-- AFFICHAGE DE LA QUANTITÉ AVEC COULEUR ET SIGNE ADAPTÉS -->
                            <td class="py-4 px-6 font-bold {{ $pivotQty > 0 ? 'text-emerald-500' : 'text-rose-500' }}">
                                {{ $pivotQty > 0 ? '+' : '' }}{{ $pivotQty }}
                            </td>

                            <td class="py-4 px-6 font-semibold text-gray-900 dark:text-white">{{ $movement->user->name ?? 'Système' }}</td>
                            <td class="py-4 px-6 text-gray-500 dark:text-gray-400">{{ $movement->motif ?? '-' }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="py-8 text-center text-gray-400">Aucun mouvement enregistré.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

</div>
@endsection