@extends('layouts.app')

@section('content')
<div class="max-w-7xl mx-auto space-y-6">

    <!-- En-tête et Bouton d'ajout -->
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
        <div>
            <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Mouvements de Stock</h1>
            <p class="text-sm text-gray-500 dark:text-gray-400">Journal chronologique des entrées, sorties et ajustements.</p>
        </div>
        <a href="{{ route('movements.create') }}" class="bg-amber-500 hover:bg-amber-600 text-gray-900 font-semibold px-4 py-2.5 rounded-xl transition shadow-lg shadow-amber-500/20 text-sm flex items-center gap-2">
            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/></svg>
            Nouveau Mouvement
        </a>
    </div>

    <!-- Barre de recherche et filtres -->
    <div class="bg-white dark:bg-gray-800 p-4 rounded-2xl border border-gray-200 dark:border-gray-700 shadow-sm">
        <form action="{{ route('movements.index') }}" method="GET" class="flex flex-col md:flex-row gap-3">
            <div class="flex-1">
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Rechercher par motif..." 
                    class="w-full bg-gray-50 dark:bg-gray-900 border border-gray-300 dark:border-gray-700 rounded-xl px-4 py-2.5 text-sm text-gray-900 dark:text-gray-100 focus:outline-none focus:border-amber-500">
            </div>
            <div class="w-full md:w-48">
                <select name="type" class="w-full bg-gray-50 dark:bg-gray-900 border border-gray-300 dark:border-gray-700 rounded-xl px-4 py-2.5 text-sm text-gray-900 dark:text-gray-100 focus:outline-none focus:border-amber-500">
                    <option value="">Tous les types</option>
                    <option value="entree" {{ request('type') == 'entree' ? 'selected' : '' }}>Entrée</option>
                    <option value="sortie" {{ request('type') == 'sortie' ? 'selected' : '' }}>Sortie</option>
                    <option value="ajustement" {{ request('type') == 'ajustement' ? 'selected' : '' }}>Ajustement</option>
                </select>
            </div>
            <div class="flex gap-2">
                <button type="submit" class="bg-gray-900 dark:bg-gray-700 hover:bg-gray-800 text-white font-medium px-5 py-2.5 rounded-xl text-sm transition">
                    Filtrer
                </button>
                @if(request('search') || request('type'))
                    <a href="{{ route('movements.index') }}" class="bg-gray-200 dark:bg-gray-700 hover:bg-gray-300 text-gray-700 dark:text-gray-300 px-4 py-2.5 rounded-xl text-sm transition flex items-center justify-center">
                        Réinitialiser
                    </a>
                @endif
            </div>
        </form>
    </div>

    <!-- Affichage par Blocs / Journaux par date -->
    <div class="space-y-6">
        @forelse($groupedMovements as $date => $movementsOfDay)
            <!-- Bloc d'une journée -->
            <div class="bg-white dark:bg-gray-800 rounded-2xl border border-gray-200 dark:border-gray-700 shadow-sm overflow-hidden">
                <!-- En-tête de la date -->
                <div class="bg-gray-50 dark:bg-gray-900/80 px-6 py-3.5 border-b border-gray-200 dark:border-gray-700 flex items-center justify-between">
                    <div class="flex items-center gap-2">
                        <svg class="w-5 h-5 text-amber-500" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                        </svg>
                        <span class="font-bold text-gray-900 dark:text-white">
                            {{ \Carbon\Carbon::parse($date)->translatedFormat('l d F Y') }}
                        </span>
                    </div>
                    <span class="text-xs font-semibold px-2.5 py-1 rounded-full bg-amber-500/10 text-amber-500">
                        {{ $movementsOfDay->count() }} mouvement(s) ce jour
                    </span>
                </div>

                <!-- Tableau des mouvements pour cette date précise -->
                <div class="overflow-x-auto">
                    <table class="w-full text-left border-collapse">
                        <thead>
                            <tr class="border-b border-gray-100 dark:border-gray-700/50 text-xs font-semibold uppercase text-gray-400 dark:text-gray-500">
                                <th class="p-4">Type & Heure</th>
                                <th class="p-4">Produit & Catégorie</th>
                                <th class="p-4">Motif</th>
                                <th class="p-4">Auteur</th>
                                <th class="p-4 text-right">Quantité</th>
                                <th class="p-4 text-right">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100 dark:divide-gray-700/50 text-sm">
                            @foreach($movementsOfDay as $movement)
                                @foreach($movement->products as $product)
                                    <tr class="hover:bg-gray-50/50 dark:hover:bg-gray-750/50 transition">
                                        <!-- Type et Heure -->
                                        <td class="p-4 whitespace-nowrap">
                                            <div class="flex items-center gap-2">
                                                @if($movement->type === 'entree')
                                                    <span class="px-2.5 py-1 rounded-full text-xs font-semibold bg-green-500/10 text-green-500">Entrée</span>
                                                @elseif($movement->type === 'sortie')
                                                    <span class="px-2.5 py-1 rounded-full text-xs font-semibold bg-red-500/10 text-red-500">Sortie</span>
                                                @else
                                                    <span class="px-2.5 py-1 rounded-full text-xs font-semibold bg-amber-500/10 text-amber-500">Ajustement</span>
                                                @endif
                                                <span class="text-xs font-medium text-gray-500 dark:text-gray-400">
                                                    {{ \Carbon\Carbon::parse($movement->created_at)->format('H:i') }}
                                                </span>
                                            </div>
                                        </td>

                                        <!-- Produit & Catégorie -->
                                        <td class="p-4">
                                            <div class="font-medium text-gray-900 dark:text-gray-100">{{ $product->name }}</div>
                                            <div class="text-xs text-gray-500 dark:text-gray-400">
                                                Catégorie : {{ $product->category->name ?? 'Aucune' }} | Réf : {{ $product->reference ?? 'N/A' }}
                                            </div>
                                        </td>

                                        <!-- Motif -->
                                        <td class="p-4 text-gray-600 dark:text-gray-300">{{ $movement->motif }}</td>

                                        <!-- Auteur -->
                                        <td class="p-4 text-gray-600 dark:text-gray-300 whitespace-nowrap">{{ $movement->user->name ?? 'N/A' }}</td>

                                        <!-- Quantité -->
                                        <td class="p-4 text-right font-bold whitespace-nowrap {{ $movement->type === 'entree' ? 'text-green-500' : ($movement->type === 'sortie' ? 'text-red-500' : 'text-amber-500') }}">
                                            {{ $movement->type === 'entree' ? '+' : '-' }}{{ $product->pivot->qty }}
                                        </td>

                                        <!-- Actions -->
                                        <td class="p-4 text-right space-x-2 whitespace-nowrap">
                                            <a href="{{ route('movements.show', $movement->id) }}" class="text-gray-500 hover:text-amber-500 transition font-medium">Détails</a>
                                            
                                            @if($loop->first)
                                                <form action="{{ route('movements.destroy', $movement->id) }}" method="POST" class="inline-block" onsubmit="return confirm('Attention : Annuler ce mouvement va réajuster les stocks. Continuer ?')">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="text-red-400 hover:text-red-600 transition font-medium">Annuler</button>
                                                </form>
                                            @endif
                                        </td>
                                    </tr>
                                @endforeach
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        @empty
            <div class="bg-white dark:bg-gray-800 rounded-2xl border border-gray-200 dark:border-gray-700 p-12 text-center text-gray-500 dark:text-gray-400">
                Aucun mouvement trouvé pour le moment.
            </div>
        @endforelse
    </div>

</div>
@endsection