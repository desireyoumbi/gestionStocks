@extends('layouts.app')

@section('header_subtitle', 'Gestion et historique des approvisionnements')
@section('header_title', 'Approvisionnements')

@section('content')
<div class="space-y-6">

    <!-- Message de succès -->
    @if(session('success'))
        <div class="p-4 rounded-2xl bg-emerald-500/10 border border-emerald-500/20 text-emerald-500 text-sm font-semibold flex items-center justify-between shadow-sm">
            <span>{{ session('success') }}</span>
            <button onclick="this.parentElement.remove()" class="text-emerald-700 hover:text-emerald-900 font-bold">✕</button>
        </div>
    @endif

    <!-- 1. CARTES STATISTIQUES DYNAMIQUES -->
    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
        <!-- Total Entrées -->
        <div class="bg-white dark:bg-gray-950 border border-gray-100 dark:border-gray-800 rounded-2xl p-5 flex items-center gap-4 shadow-sm">
            <div class="w-12 h-12 rounded-xl bg-emerald-500/10 text-emerald-500 flex items-center justify-center shrink-0">
                <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4" />
                </svg>
            </div>
            <div>
                <p class="text-xs font-bold uppercase tracking-wider text-gray-400 dark:text-gray-500">Total Opérations</p>
                <h3 class="text-xl font-black text-gray-900 dark:text-white">
                    {{ isset($suppliesPaginator) ? $suppliesPaginator->total() : 0 }}
                </h3>
            </div>
        </div>

        <!-- Volume Total d'Articles Ajoutés -->
        <div class="bg-white dark:bg-gray-950 border border-gray-100 dark:border-gray-800 rounded-2xl p-5 flex items-center gap-4 shadow-sm">
            <div class="w-12 h-12 rounded-xl bg-blue-500/10 text-blue-500 flex items-center justify-center shrink-0">
                <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4" />
                </svg>
            </div>
            <div>
                <p class="text-xs font-bold uppercase tracking-wider text-gray-400 dark:text-gray-500">Articles Réapprovisionnés</p>
                <h3 class="text-xl font-black text-gray-900 dark:text-white">
                    {{ $totalArticles ?? 0 }}
                </h3>
            </div>
        </div>

        <!-- Dépenses Totales des Approvisionnements -->
        <div class="bg-white dark:bg-gray-950 border border-gray-100 dark:border-gray-800 rounded-2xl p-5 flex items-center gap-4 shadow-sm">
            <div class="w-12 h-12 rounded-xl bg-amber-500/10 text-amber-500 flex items-center justify-center shrink-0">
                <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
            </div>
            <div>
                <p class="text-xs font-bold uppercase tracking-wider text-gray-400 dark:text-gray-500">Dépenses Totales</p>
                <h3 class="text-xl font-black text-gray-900 dark:text-white">
                    {{ $totalDepensesFormatted ?? '0 FCFA' }}
                </h3>
            </div>
        </div>
    </div>

    <!-- En-tête avec actions et recherche -->
    <div class="bg-white dark:bg-gray-950 border border-gray-100 dark:border-gray-800 rounded-2xl p-4 flex flex-col sm:flex-row items-center justify-between gap-4 shadow-sm">
        
        <!-- Recherche -->
        <form method="GET" action="{{ route('supplies.index') }}" class="w-full sm:w-80">
            <div class="relative w-full">
                <span class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-gray-400">
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" /></svg>
                </span>
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Rechercher un approvisionnement..." class="w-full pl-9 pr-4 py-2.5 bg-gray-50 dark:bg-gray-900 border border-gray-200 dark:border-gray-800 rounded-xl text-sm text-gray-800 dark:text-gray-200 focus:outline-none focus:border-amber-500 transition">
            </div>
        </form>

        <!-- Boutons -->
        <div class="w-full sm:w-auto flex items-center gap-3 shrink-0">
            <a href="{{ route('movements.index') }}" class="flex-1 sm:flex-none flex items-center justify-center gap-2 px-4 py-2.5 bg-gray-100 dark:bg-gray-900 hover:bg-gray-200 dark:hover:bg-gray-800 text-gray-700 dark:text-gray-300 font-bold text-sm rounded-xl border border-gray-200 dark:border-gray-800 transition">
                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                Voir le Journal des Mouvements
            </a>

            <a href="{{ route('supplies.create') }}" class="flex-1 sm:flex-none flex items-center justify-center gap-2 px-4 py-2.5 bg-emerald-500 hover:bg-emerald-600 text-gray-950 font-bold text-sm rounded-xl shadow-lg shadow-emerald-500/20 transition">
                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4" /></svg>
                Nouvel Approvisionnement
            </a>
        </div>
    </div>

    <!-- BLOCS PAR JOUR -->
    <div class="space-y-6">
        @forelse($groupedSupplies as $date => $suppliesOfDay)
            <div class="bg-white dark:bg-gray-950 border border-gray-100 dark:border-gray-800 rounded-2xl overflow-hidden shadow-sm">
                
                <!-- En-tête de la carte journalière -->
                <div class="bg-gray-50/75 dark:bg-gray-900/75 px-6 py-3.5 border-b border-gray-100 dark:border-gray-800 flex items-center justify-between">
                    <h4 class="text-xs font-black uppercase tracking-wider text-gray-900 dark:text-white flex items-center gap-2">
                        <span class="w-2 h-2 rounded-full bg-emerald-500"></span>
                        {{ \Carbon\Carbon::parse($date)->translatedFormat('l d F Y') }}
                    </h4>
                    <span class="text-xs font-bold px-2.5 py-1 bg-emerald-500/10 text-emerald-500 rounded-lg">
                        {{ $suppliesOfDay->count() }} opération(s)
                    </span>
                </div>

                <!-- Tableau des mouvements de ce jour précis -->
                <div class="overflow-x-auto">
                    <table class="w-full text-left border-collapse">
                        <thead>
                            <tr class="border-b border-gray-100 dark:border-gray-900 text-xs font-bold text-gray-400 dark:text-gray-500 uppercase tracking-wider bg-gray-50/30 dark:bg-gray-900/30">
                                <th class="py-3 px-6">Heure</th>
                                <th class="py-3 px-6">Produit approvisionné</th>
                                <th class="py-3 px-6">Quantité</th>
                                <th class="py-3 px-6">Auteur</th>
                                <th class="py-3 px-6">Motif / Référence</th>
                                <th class="py-3 px-6 text-right">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100 dark:divide-gray-900 text-sm font-medium text-gray-700 dark:text-gray-300">
                            @foreach($suppliesOfDay as $supply)
                                <tr class="hover:bg-gray-50/50 dark:hover:bg-gray-900/40 transition">
                                    <td class="py-3 px-6 text-gray-500 dark:text-gray-400">{{ $supply->created_at->format('H:i') }}</td>
                                    <td class="py-3 px-6 font-bold text-gray-900 dark:text-white">{{ $supply->products->first()->name ?? 'Produit supprimé' }}</td>
                                    <td class="py-3 px-6 font-bold text-emerald-500">+{{ $supply->products->first()->pivot->qty ?? 0 }}</td>
                                    <td class="py-3 px-6 font-semibold text-gray-900 dark:text-white">{{ $supply->user->name ?? 'Système' }}</td>
                                    <td class="py-3 px-6 text-gray-500 dark:text-gray-400">{{ $supply->motif ?? '-' }}</td>
                                    <td class="py-3 px-6 text-right">
                                        <form action="{{ route('supplies.destroy', $supply->id) }}" method="POST" onsubmit="return confirm('Voulez-vous vraiment annuler cet approvisionnement ? Le stock sera déduit.');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="p-2 rounded-lg bg-rose-500/10 text-rose-500 hover:bg-rose-500 hover:text-white transition" title="Annuler l'approvisionnement">
                                                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2.032 2.032 0 0116.138 21H7.862a2.032 2.032 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" /></svg>
                                            </button>
                                        </form>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        @empty
            <div class="bg-white dark:bg-gray-950 border border-gray-100 dark:border-gray-800 rounded-2xl p-12 text-center text-gray-400">
                Aucun approvisionnement enregistré pour le moment.
            </div>
        @endforelse
    </div>

    <!-- PAGINATION -->
    @if(isset($suppliesPaginator) && $suppliesPaginator->hasPages())
        <div class="mt-6">
            {{ $suppliesPaginator->links() }}
        </div>
    @endif

</div>
@endsection