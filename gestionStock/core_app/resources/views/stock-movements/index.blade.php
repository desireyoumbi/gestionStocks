@extends('layouts.app')
@section('header_title', 'Historique')
@section('header_subtitle', 'Journal des mouvements de stock')

@section('content')
<div class="space-y-6">

    <!-- En-tête -->
    <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Journal des Mouvements</h1>
            <p class="text-sm text-gray-500 mt-0.5">Traçabilité complète de toutes les entrées et sorties de stock.</p>
        </div>
        
        <!-- Bouton d'export PDF -->
        <div>
            <a href="{{ route('stock-movements.export-pdf', request()->all()) }}" class="inline-flex items-center gap-2 bg-amber-600 hover:bg-amber-700 text-white text-sm font-semibold py-2.5 px-4 rounded-xl transition shadow-sm">
                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                </svg>
                Exporter en PDF
            </a>
        </div>
    </div>

    <!-- Mini-cartes de Statistiques / KPIs -->
    @php
        // Calculs basés sur TOUS les mouvements filtrés de la requête (sans pagination pour avoir les totaux globaux de la recherche)
        $allFilteredMovements = $groupedByWeek->flatten();
        $totalEntries = $allFilteredMovements->where('type', 'entry')->sum('quantity');
        $totalSales = $allFilteredMovements->where('type', 'sale')->sum('quantity');
        $totalAdjustments = $allFilteredMovements->where('type', 'adjustment')->sum('quantity');
    @endphp

    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
        <!-- Carte Entrées -->
        <div class="bg-white p-5 rounded-3xl shadow-sm border border-gray-100 flex items-center gap-4">
            <div class="w-12 h-12 rounded-2xl bg-emerald-50 text-emerald-600 flex items-center justify-center font-bold text-lg">
                +
            </div>
            <div>
                <p class="text-xs font-semibold uppercase tracking-wider text-gray-400">Total Entrées</p>
                <h4 class="text-xl font-black text-gray-900 mt-0.5">+{{ $totalEntries }}</h4>
            </div>
        </div>

        <!-- Carte Ventes -->
        <div class="bg-white p-5 rounded-3xl shadow-sm border border-gray-100 flex items-center gap-4">
            <div class="w-12 h-12 rounded-2xl bg-blue-50 text-blue-600 flex items-center justify-center font-bold text-lg">
                -
            </div>
            <div>
                <p class="text-xs font-semibold uppercase tracking-wider text-gray-400">Total Ventes</p>
                <h4 class="text-xl font-black text-gray-900 mt-0.5">-{{ $totalSales }}</h4>
            </div>
        </div>

        <!-- Carte Ajustements / Autres -->
        <div class="bg-white p-5 rounded-3xl shadow-sm border border-gray-100 flex items-center gap-4">
            <div class="w-12 h-12 rounded-2xl bg-amber-50 text-amber-600 flex items-center justify-center font-bold text-lg">
                ±
            </div>
            <div>
                <p class="text-xs font-semibold uppercase tracking-wider text-gray-400">Ajustements</p>
                <h4 class="text-xl font-black text-gray-900 mt-0.5">{{ $totalAdjustments >= 0 ? '+' . $totalAdjustments : $totalAdjustments }}</h4>
            </div>
        </div>
    </div>

    <!-- Barre de recherche et filtres élargie -->
    <div class="bg-white p-4 rounded-3xl shadow-sm border border-gray-100">
        <form method="GET" action="{{ route('stock-movements.index') }}" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-3">
            
            <!-- Recherche texte (Produit ou Motif) -->
            <div>
                <input type="text" name="search" value="{{ request('search') }}" 
                    placeholder="Rechercher un produit, motif..." 
                    class="w-full text-sm rounded-xl border-gray-200 focus:border-amber-500 focus:ring-amber-500 py-2.5">
            </div>

            <!-- Filtre par Type -->
            <div>
                <select name="type" class="w-full text-sm rounded-xl border-gray-200 focus:border-amber-500 focus:ring-amber-500 py-2.5">
                    <option value="">Tous les types</option>
                    <option value="entry" {{ request('type') == 'entry' ? 'selected' : '' }}>Entrée</option>
                    <option value="sale" {{ request('type') == 'sale' ? 'selected' : '' }}>Vente</option>
                    <option value="adjustment" {{ request('type') == 'adjustment' ? 'selected' : '' }}>Ajustement</option>
                </select>
            </div>

            <!-- Filtre par Utilisateur -->
            <div>
                <select name="user_id" class="w-full text-sm rounded-xl border-gray-200 focus:border-amber-500 focus:ring-amber-500 py-2.5">
                    <option value="">Tous les utilisateurs</option>
                    @isset($users)
                        @foreach($users as $user)
                            <option value="{{ $user->id }}" {{ request('user_id') == $user->id ? 'selected' : '' }}>{{ $user->name }}</option>
                        @endforeach
                    @endisset
                </select>
            </div>

            <!-- Filtre par Date -->
            <div>
                <input type="date" name="date" value="{{ request('date') }}" 
                    class="w-full text-sm rounded-xl border-gray-200 focus:border-amber-500 focus:ring-amber-500 py-2.5">
            </div>

            <!-- Boutons d'action -->
            <div class="flex items-center gap-2">
                <button type="submit" class="flex-1 bg-gray-900 text-white text-sm font-semibold py-2.5 px-4 rounded-xl hover:bg-gray-800 transition shadow-sm">
                    Filtrer
                </button>
                @if(request()->filled('search') || request()->filled('type') || request()->filled('user_id') || request()->filled('date'))
                    <a href="{{ route('stock-movements.index') }}" class="bg-gray-100 text-gray-600 text-sm font-semibold py-2.5 px-3 rounded-xl hover:bg-gray-200 transition" title="Réinitialiser">
                        ✕
                    </a>
                @endif
            </div>

        </form>
    </div>

    {{-- Affichage par Blocs de Semaines --}}
    @forelse($groupedByWeek as $weekTitle => $weekMovements)
        <div class="bg-white rounded-3xl shadow-md border border-gray-100 overflow-hidden mb-6">
            
            <!-- En-tête du bloc semaine -->
            <div class="bg-gray-50/80 px-6 py-4 border-b border-gray-100 flex justify-between items-center">
                <h3 class="text-sm font-extrabold uppercase tracking-wider text-gray-700 flex items-center gap-2">
                    <span class="w-2.5 h-2.5 rounded-full bg-amber-500"></span>
                    {{ $weekTitle }}
                </h3>
                <span class="text-xs font-semibold text-gray-500 bg-white px-3 py-1 rounded-lg border border-gray-100 shadow-2xs">
                    {{ $weekMovements->count() }} mouvement(s)
                </span>
            </div>

            <!-- Tableau de la semaine -->
            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse">
                    <thead>
                        <tr class="border-b border-gray-100 text-xs font-bold text-gray-500 uppercase tracking-wider bg-gray-50/30">
                            <th class="px-6 py-3.5">Date / Heure</th>
                            <th class="px-6 py-3.5">Utilisateur</th>
                            <th class="px-6 py-3.5">Produit</th>
                            <th class="px-6 py-3.5">Type</th>
                            <th class="px-6 py-3.5">Quantité</th>
                            <th class="px-6 py-3.5">Stock (Avant ➔ Après)</th>
                            <th class="px-6 py-3.5">Motif</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100 text-sm">
                        @foreach($weekMovements as $movement)
                        <tr class="hover:bg-gray-50/50 transition">
                            <td class="px-6 py-4 whitespace-nowrap text-gray-600">
                                {{ $movement->created_at->format('d/m/Y H:i') }}
                            </td>
                            <td class="px-6 py-4 font-bold text-gray-900">
                                {{ $movement->user->name ?? 'Inconnu' }}
                            </td>
                            <td class="px-6 py-4 font-semibold text-gray-900">
                                {{ $movement->product->name ?? 'Produit supprimé' }}
                            </td>
                            <td class="px-6 py-4">
                                @if($movement->type == 'entry')
                                    <span class="px-3 py-1 rounded-full text-xs font-bold bg-emerald-100 text-emerald-700">Entrée</span>
                                @elseif($movement->type == 'sale')
                                    <span class="px-3 py-1 rounded-full text-xs font-bold bg-blue-100 text-blue-700">Vente</span>
                                @else
                                    <span class="px-3 py-1 rounded-full text-xs font-bold bg-amber-100 text-amber-700">{{ ucfirst($movement->type) }}</span>
                                @endif
                            </td>
                            <td class="px-6 py-4 font-black text-base {{ $movement->type == 'entry' || ($movement->type == 'adjustment' && $movement->quantity > 0) ? 'text-emerald-600' : 'text-red-600' }}">
                                {{ $movement->type == 'entry' || ($movement->type == 'adjustment' && $movement->quantity > 0) ? '+' . $movement->quantity : '-' . abs($movement->quantity) }}
                            </td>
                            <td class="px-6 py-4 text-gray-600 font-mono text-xs">
                                {{ $movement->old_stock }} ➔ {{ $movement->new_stock }}
                            </td>
                            <td class="px-6 py-4 text-gray-600 italic">
                                {{ $movement->reason ?? '-' }}
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    @empty
        <!-- État vide si aucun mouvement ne correspond aux filtres -->
        <div class="bg-white rounded-3xl shadow-md border border-gray-100 p-12 text-center">
            <div class="flex flex-col items-center justify-center space-y-3">
                <div class="w-12 h-12 rounded-2xl bg-amber-50 text-amber-500 flex items-center justify-center shadow-inner">
                    <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                </div>
                <p class="text-gray-500 font-medium text-sm">Aucun mouvement ne correspond à vos critères de recherche.</p>
                <a href="{{ route('stock-movements.index') }}" class="text-sm font-bold text-amber-600 hover:underline">Réinitialiser les filtres</a>
            </div>
        </div>
    @endforelse
        
    {{-- Pagination globale --}}
    @if($movements->hasPages())
        <div class="p-4 bg-white rounded-2xl border border-gray-100 shadow-sm text-sm">
            {{ $movements->links() }}
        </div>
    @endif

</div>
@endsection