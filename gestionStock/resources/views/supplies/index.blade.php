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

    <!-- Message d'erreur -->
    @if(session('error'))
        <div class="p-4 rounded-2xl bg-red-500/10 border border-red-500/20 text-red-500 text-sm font-semibold flex items-center justify-between shadow-sm">
            <span>{{ session('error') }}</span>
            <button onclick="this.parentElement.remove()" class="text-red-700 hover:text-red-900 font-bold">✕</button>
        </div>
    @endif

    <!-- 1. CARTES STATISTIQUES GLOBALES (Page actuelle) -->
    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
        <div class="bg-white dark:bg-gray-950 border border-gray-100 dark:border-gray-800 rounded-2xl p-5 flex items-center gap-4 shadow-sm">
            <div class="w-12 h-12 rounded-xl bg-emerald-500/10 text-emerald-500 flex items-center justify-center shrink-0">
                <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4" /></svg>
            </div>
            <div>
                <p class="text-[10px] font-bold uppercase tracking-wider text-gray-400 dark:text-gray-500">Opérations (Page)</p>
                <h3 class="text-xl font-black text-gray-900 dark:text-white">{{ $totalOperations ?? 0 }}</h3>
            </div>
        </div>

        <div class="bg-white dark:bg-gray-950 border border-gray-100 dark:border-gray-800 rounded-2xl p-5 flex items-center gap-4 shadow-sm">
            <div class="w-12 h-12 rounded-xl bg-blue-500/10 text-blue-500 flex items-center justify-center shrink-0">
                <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4" /></svg>
            </div>
            <div>
                <p class="text-[10px] font-bold uppercase tracking-wider text-gray-400 dark:text-gray-500">Articles (Page)</p>
                <h3 class="text-xl font-black text-gray-900 dark:text-white">{{ $totalArticles ?? 0 }}</h3>
            </div>
        </div>

        <div class="bg-white dark:bg-gray-950 border border-gray-100 dark:border-gray-800 rounded-2xl p-5 flex items-center gap-4 shadow-sm">
            <div class="w-12 h-12 rounded-xl bg-amber-500/10 text-amber-500 flex items-center justify-center shrink-0">
                <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
            </div>
            <div>
                <p class="text-[10px] font-bold uppercase tracking-wider text-gray-400 dark:text-gray-500">Dépenses (Page)</p>
                <h3 class="text-xl font-black text-gray-900 dark:text-white">{{ $totalDepensesFormatted ?? '0 FCFA' }}</h3>
            </div>
        </div>
    </div>

    <!-- En-tête actions & Exports PDF -->
    <div class="bg-white dark:bg-gray-950 border border-gray-100 dark:border-gray-800 rounded-2xl p-4 flex flex-col sm:flex-row items-center justify-between gap-4 shadow-sm">
        <form method="GET" action="{{ route('supplies.index') }}" class="w-full sm:w-80">
            <div class="relative w-full">
                <span class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-gray-400"><svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" /></svg></span>
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Rechercher..." class="w-full pl-9 pr-4 py-2.5 bg-gray-50 dark:bg-gray-900 border border-gray-200 dark:border-gray-800 rounded-xl text-sm focus:outline-none focus:border-amber-500 transition">
            </div>
        </form>

        <div class="flex items-center gap-2 w-full sm:w-auto justify-end">
            <!-- Bouton PDF Global -->
            <a href="{{ route('supplies.export.all.pdf') }}" class="flex items-center gap-2 px-4 py-2.5 bg-gray-800 hover:bg-gray-900 text-white font-bold text-sm rounded-xl shadow-sm transition">
                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" /></svg>
                PDF Global
            </a>

            <a href="{{ route('supplies.create') }}" class="flex items-center gap-2 px-4 py-2.5 bg-emerald-500 hover:bg-emerald-600 text-gray-950 font-bold text-sm rounded-xl shadow-lg transition">
                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4" /></svg>
                Nouvel Approvisionnement
            </a>
        </div>
    </div>

    <!-- BLOCS PAR JOUR -->
    <div class="space-y-6">
        @forelse($groupedSupplies as $date => $suppliesOfDay)
            @php
                // Calculs incluant uniquement les lignes actives pour les statistiques journalières affichées
                $activeSuppliesOfDay = $suppliesOfDay->whereNull('annule_at');
                $dayOperations = $activeSuppliesOfDay->count();
                $dayArticles = $activeSuppliesOfDay->sum(fn($s) => $s->products->sum('pivot.qty'));
                $dayExpenses = $activeSuppliesOfDay->sum(function($s) {
                    return $s->products->sum(function($p) {
                        return $p->pivot->qty * $p->purchase_price;
                    });
                });
            @endphp

            <div class="bg-white dark:bg-gray-950 border border-gray-100 dark:border-gray-800 rounded-2xl overflow-hidden shadow-sm">
                <!-- En-tête du jour avec statistiques et bouton PDF du jour -->
                <div class="bg-gray-50/75 dark:bg-gray-900/75 px-6 py-4 border-b border-gray-100 dark:border-gray-800 flex flex-wrap items-center justify-between gap-4">
                    <h4 class="text-xs font-black uppercase tracking-wider text-gray-900 dark:text-white flex items-center gap-2">
                        <span class="w-2 h-2 rounded-full bg-emerald-500"></span>
                        {{ \Carbon\Carbon::parse($date)->translatedFormat('l d F Y') }}
                    </h4>
                    
                    <div class="flex items-center gap-4">
                        <div class="flex gap-4">
                            <span class="text-[10px] font-bold text-gray-500 uppercase">{{ $dayOperations }} Opérations</span>
                            <span class="text-[10px] font-bold text-blue-500 uppercase">{{ $dayArticles }} Articles</span>
                            <span class="text-[10px] font-bold text-amber-600 uppercase">{{ number_format($dayExpenses, 0, ',', ' ') }} FCFA</span>
                        </div>

                        <!-- Bouton PDF pour ce jour précis -->
                        <a href="{{ route('supplies.export.day.pdf', ['date' => $date]) }}" title="Télécharger le PDF de cette journée" class="p-1.5 bg-blue-500/10 hover:bg-blue-500 text-blue-600 hover:text-white rounded-lg transition text-xs font-bold flex items-center gap-1">
                            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" /></svg>
                            <span class="hidden sm:inline">PDF</span>
                        </a>
                    </div>
                </div>

                <div class="overflow-x-auto">
                    <table class="w-full text-left border-collapse">
                        <thead>
                            <tr class="border-b border-gray-100 dark:border-gray-900 text-xs font-bold text-gray-400 uppercase tracking-wider bg-gray-50/30">
                                <th class="py-3 px-6">Heure</th>
                                <th class="py-3 px-6">Fournisseur</th>
                                <th class="py-3 px-6">Produits</th>
                                <th class="py-3 px-6">Total Qté</th>
                                <th class="py-3 px-6">Motif</th>
                                <th class="py-3 px-6 text-right">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100 dark:divide-gray-900 text-sm">
                            @foreach($suppliesOfDay as $supply)
                                <tr class="hover:bg-gray-50/50 dark:hover:bg-gray-900/50 transition @if($supply->annule_at) opacity-60 bg-red-50/20 dark:bg-red-950/10 @endif">
                                    <td class="py-3 px-6 text-gray-500">{{ $supply->created_at->format('H:i') }}</td>
                                    <td class="py-3 px-6 text-xs font-bold">
                                        @if($supply->purchaseOrder && $supply->purchaseOrder->supplier)
                                            <span class="text-amber-600">{{ $supply->purchaseOrder->supplier->name }}</span>
                                        @elseif($supply->supplier)
                                            <span class="text-amber-600">{{ $supply->supplier->name }}</span>
                                        @else
                                            <span class="text-gray-400 font-normal">Direct / Aucun</span>
                                        @endif
                                    </td>
                                    <td class="py-3 px-6">
                                        <div class="flex flex-col gap-1">
                                            @foreach($supply->products as $product)
                                                <div class="font-bold text-gray-900 dark:text-white">{{ $product->name }} <span class="text-[10px] text-gray-400 font-normal">({{ $product->pivot->qty }} unités)</span></div>
                                            @endforeach
                                        </div>
                                    </td>
                                    <td class="py-3 px-6 font-bold @if($supply->annule_at) text-gray-400 line-through @else text-emerald-500 @endif">+{{ $supply->products->sum('pivot.qty') }}</td>
                                    <td class="py-3 px-6 text-gray-500">{{ $supply->motif ?? '-' }}</td>
                                    <td class="py-3 px-6 text-right">
                                        <div class="flex items-center justify-end gap-2">
                                            @if($supply->annule_at)
                                                <!-- Bouton "Annulé" inactif et stylé élégamment à la place de l'icône de suppression -->
                                                <button type="button" disabled class="px-3 py-1 bg-gray-100 dark:bg-gray-800 text-gray-400 dark:text-gray-500 text-xs font-bold rounded-lg cursor-not-allowed shadow-none border border-gray-200 dark:border-gray-700" title="Déjà annulé le {{ $supply->annule_at->format('d/m/Y à H:i') }}">
                                                    Annulé
                                                </button>
                                            @else
                                                <span class="inline-flex items-center px-2.5 py-1 rounded-full text-[10px] font-bold bg-emerald-50 text-emerald-600 border border-emerald-100">
                                                    Actif
                                                </span>

                                                <!-- Formulaire d'Annulation -->
                                                <form action="{{ route('supplies.destroy', $supply->id) }}" method="POST" onsubmit="return confirm('Êtes-vous sûr de vouloir annuler cet approvisionnement ? Cela va déduire le stock et rééquilibrer la caisse.');">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="p-1.5 bg-red-500/10 hover:bg-red-500 text-red-600 hover:text-white rounded-lg transition" title="Annuler l'approvisionnement">
                                                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" /></svg>
                                                    </button>
                                                </form>
                                            @endif
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        @empty
            <div class="bg-white dark:bg-gray-950 border border-gray-100 dark:border-gray-800 rounded-2xl p-12 text-center text-gray-400">Aucun approvisionnement trouvé.</div>
        @endforelse
    </div>

    @if(isset($suppliesPaginator) && $suppliesPaginator->hasPages())
        <div class="mt-6">{{ $suppliesPaginator->links() }}</div>
    @endif
</div>
@endsection