@extends('layouts.app')

@section('header_subtitle', 'Historique des sorties et ventes')
@section('header_title', 'Liste des Ventes')

@section('content')
<div class="space-y-6">

    <!-- 1. CARTES STATISTIQUES -->
    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
        <!-- Panier Moyen -->
        <div class="bg-white dark:bg-gray-950 border border-gray-100 dark:border-gray-800 rounded-2xl p-5 flex items-center gap-4 shadow-sm">
            <div class="w-12 h-12 rounded-xl bg-blue-500/10 text-blue-500 flex items-center justify-center shrink-0">
                <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z" />
                </svg>
            </div>
            <div>
                <p class="text-xs font-bold uppercase tracking-wider text-gray-400 dark:text-gray-500">Panier Moyen</p>
                <h3 class="text-xl font-black text-gray-900 dark:text-white">{{ $panierMoyen ?? '0 FCFA' }}</h3>
            </div>
        </div>

        <!-- Articles Vendus -->
        <div class="bg-white dark:bg-gray-950 border border-gray-100 dark:border-gray-800 rounded-2xl p-5 flex items-center gap-4 shadow-sm">
            <div class="w-12 h-12 rounded-xl bg-purple-500/10 text-purple-500 flex items-center justify-center shrink-0">
                <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4" />
                </svg>
            </div>
            <div>
                <p class="text-xs font-bold uppercase tracking-wider text-gray-400 dark:text-gray-500">Articles Vendus</p>
                <h3 class="text-xl font-black text-gray-900 dark:text-white">{{ $totalArticlesVendus ?? 0 }}</h3>
            </div>
        </div>

        <!-- Produit Phare -->
        <div class="bg-white dark:bg-gray-950 border border-gray-100 dark:border-gray-800 rounded-2xl p-5 flex items-center gap-4 shadow-sm">
            <div class="w-12 h-12 rounded-xl bg-amber-500/10 text-amber-500 flex items-center justify-center shrink-0">
                <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z" />
                </svg>
            </div>
            <div>
                <p class="text-xs font-bold uppercase tracking-wider text-gray-400 dark:text-gray-500">Produit Phare</p>
                <h3 class="text-sm font-black text-gray-900 dark:text-white truncate max-w-[160px]" title="{{ $produitPhare ?? 'Aucun' }}">
                    {{ $produitPhare ?? 'Aucun' }}
                </h3>
            </div>
        </div>
    </div>
    
    <!-- En-tête -->
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
        <h3 class="text-sm font-bold uppercase tracking-wider text-gray-400">Journal des ventes</h3>
        <a href="{{ route('sales.create') }}" class="px-4 py-2.5 bg-rose-500 hover:bg-rose-600 text-white font-bold text-sm rounded-xl shadow-lg shadow-rose-500/20 transition whitespace-nowrap">
            + Nouvelle Vente
        </a>
    </div>

    <!-- BLOCS DES VENTES PAR JOUR -->
    <div class="space-y-6">
        @forelse($groupedSales as $date => $salesOfDay)
            <div class="bg-white dark:bg-gray-950 border border-gray-100 dark:border-gray-800 rounded-2xl overflow-hidden shadow-sm">
                <!-- En-tête date -->
                <div class="bg-gray-50/75 dark:bg-gray-900/75 px-6 py-3.5 border-b border-gray-100 dark:border-gray-800">
                    <h4 class="text-xs font-black uppercase tracking-wider text-gray-900 dark:text-white flex items-center gap-2">
                        <span class="w-2 h-2 rounded-full bg-rose-500"></span>
                        {{ \Carbon\Carbon::parse($date)->translatedFormat('l d F Y') }}
                    </h4>
                </div>

                <div class="overflow-x-auto">
                    <table class="w-full text-left border-collapse text-sm">
                        <thead>
                            <tr class="bg-gray-50/30 dark:bg-gray-900/30 border-b border-gray-100 dark:border-gray-800 text-gray-400 uppercase font-bold text-xs">
                                <th class="p-4">Heure</th>
                                <th class="p-4">Référence / ID</th>
                                <th class="p-4">Utilisateur</th>
                                <th class="p-4">Produit & Quantité</th>
                                <th class="p-4">Motif</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100 dark:divide-gray-900 text-gray-700 dark:text-gray-300">
                            @foreach($salesOfDay as $sale)
                                @foreach($sale->products as $product)
                                    <tr class="hover:bg-gray-50/50 transition">
                                        <td class="p-4 text-gray-500">{{ $sale->created_at->format('H:i') }}</td>
                                        <td class="p-4 font-bold">#{{ $sale->id }}</td>
                                        <td class="p-4">{{ $sale->user->name ?? 'N/A' }}</td>
                                        <td class="p-4">
                                            <span class="inline-block bg-gray-100 dark:bg-gray-900 px-2.5 py-1 rounded-md">
                                                {{ $product->name }} <span class="font-bold text-rose-500">(x{{ $product->pivot->qty }})</span>
                                            </span>
                                        </td>
                                        <td class="p-4 text-gray-500">{{ $sale->motif }}</td>
                                    </tr>
                                @endforeach
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        @empty
            <div class="p-12 text-center text-gray-400 bg-white rounded-2xl border border-gray-100">Aucune vente enregistrée.</div>
        @endforelse
    </div>
        
    <!-- Pagination -->
    @if(isset($sales) && $sales->hasPages())
        <div class="mt-6">
            {{ $sales->links() }}
        </div>
    @endif

</div>
@endsection