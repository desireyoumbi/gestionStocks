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
    
    <!-- BARRE DE RECHERCHE & BOUTONS D'ACTION -->
    <div class="bg-white dark:bg-gray-950 p-6 rounded-3xl shadow-xl border border-gray-100 dark:border-gray-800 flex flex-col sm:flex-row justify-between items-center gap-4">
        <div class="relative w-full sm:w-96">
            <svg class="w-5 h-5 absolute left-3.5 top-1/2 -translate-y-1/2 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
            </svg>
            <input type="text" id="liveSearch" placeholder="Rechercher une vente..." 
                class="w-full bg-gray-50 dark:bg-gray-900 border border-gray-200 dark:border-gray-800 rounded-xl pl-11 pr-4 py-3 text-sm text-gray-800 dark:text-gray-200 placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-rose-500/20 focus:border-rose-500 transition">
        </div>

        <div class="flex items-center gap-3 w-full sm:w-auto justify-end">
            <a href="{{ route('sales.export.pdf') }}" class="bg-slate-900 hover:bg-slate-800 dark:bg-slate-800 dark:hover:bg-slate-700 text-white text-sm font-bold px-5 py-3 rounded-2xl shadow-md hover:shadow-lg transition flex items-center gap-2">
                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z" />
                </svg>
                PDF Global
            </a>

            <a href="{{ route('sales.create') }}" class="bg-rose-500 hover:bg-rose-600 text-white text-sm font-bold px-5 py-3 rounded-2xl shadow-md hover:shadow-lg transition flex items-center gap-2 whitespace-nowrap">
                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4" />
                </svg>
                Nouvelle Vente
            </a>
        </div>
    </div>

    <!-- BLOCS DES VENTES PAR JOUR -->
    <div class="space-y-6" id="salesContainer">
        @forelse($groupedSales as $date => $salesOfDay)
            @php
                $nbOperations = $salesOfDay->count();
                $nbArticlesJour = 0;
                $totalMontantJour = 0;

                foreach($salesOfDay as $sale) {
                    foreach($sale->products as $product) {
                        $qty = $product->pivot->qty ?? ($product->pivot->quantity ?? 0);
                        $price = $product->selling_price ?? 0;
                        $nbArticlesJour += $qty;
                        $totalMontantJour += ($qty * $price);
                    }
                }
            @endphp

            <div class="bg-white dark:bg-gray-950 border border-gray-100 dark:border-gray-800 rounded-2xl overflow-hidden shadow-sm sale-day-group" data-date-text="{{ \Carbon\Carbon::parse($date)->translatedFormat('l d F Y') }}">
                
                <div class="bg-gray-50/75 dark:bg-gray-900/75 px-6 py-3.5 border-b border-gray-100 dark:border-gray-800 flex flex-wrap justify-between items-center gap-4">
                    <h4 class="text-xs font-black uppercase tracking-wider text-gray-900 dark:text-white flex items-center gap-2">
                        <span class="w-2 h-2 rounded-full bg-rose-500"></span>
                        {{ \Carbon\Carbon::parse($date)->translatedFormat('l d F Y') }}
                    </h4>

                    <div class="flex items-center gap-4 text-xs font-bold">
                        <span class="text-gray-500 dark:text-gray-400">{{ $nbOperations }} Opération(s)</span>
                        <span class="text-purple-600 dark:text-purple-400">{{ $nbArticlesJour }} Article(s)</span>
                        <span class="text-rose-600 dark:text-rose-400 font-black">{{ number_format($totalMontantJour, 0, ',', ' ') }} FCFA</span>

                        <a href="{{ route('sales.export.pdf.date', $date) }}" class="bg-rose-50 hover:bg-rose-500 text-rose-600 hover:text-white px-3 py-1.5 rounded-xl transition flex items-center gap-1.5 border border-rose-200 dark:bg-gray-900 dark:border-gray-800">
                            <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z" /></svg>
                            PDF
                        </a>
                    </div>
                </div>

                <div class="overflow-x-auto">
                    <table class="w-full text-left border-collapse text-sm">
                        <thead>
                            <tr class="bg-gray-50/30 dark:bg-gray-900/30 border-b border-gray-100 dark:border-gray-800 text-gray-400 uppercase font-bold text-xs">
                                <th class="p-4">Heure</th>
                                <th class="p-4">Réf / ID</th>
                                <th class="p-4">Utilisateur</th>
                                <th class="p-4">Produit & Qté</th>
                                <th class="p-4">Prix Unit.</th>
                                <th class="p-4">Total Ligne</th>
                                <th class="p-4">Motif</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100 dark:divide-gray-900 text-gray-700 dark:text-gray-300">
                            @foreach($salesOfDay as $sale)
                                @foreach($sale->products as $product)
                                    @php
                                        $qty = $product->pivot->qty ?? ($product->pivot->quantity ?? 0);
                                        $price = $product->selling_price ?? 0;
                                        $lineTotal = $qty * $price;
                                    @endphp
                                    <tr class="hover:bg-gray-50/50 transition sale-row">
                                        <td class="p-4 text-gray-500 sale-time">{{ $sale->created_at->format('H:i') }}</td>
                                        <td class="p-4 font-bold sale-id">#{{ $sale->id }}</td>
                                        <td class="p-4 sale-user">{{ $sale->user->name ?? 'N/A' }}</td>
                                        <td class="p-4 sale-product">
                                            <span class="inline-block bg-gray-100 dark:bg-gray-900 px-2.5 py-1 rounded-md">
                                                {{ $product->name }} <span class="font-bold text-rose-500">(x{{ $qty }})</span>
                                            </span>
                                        </td>
                                        <td class="p-4 sale-price">{{ number_format($price, 0, ',', ' ') }}</td>
                                        <td class="p-4 font-bold text-gray-900 dark:text-white sale-total">{{ number_format($lineTotal, 0, ',', ' ') }} FCFA</td>
                                        <td class="p-4 text-gray-500 sale-motif">{{ $sale->motif }}</td>
                                    </tr>
                                @endforeach
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        @empty
            <div class="p-12 text-center text-gray-400 bg-white dark:bg-gray-950 rounded-2xl border border-gray-100 dark:border-gray-800">Aucune vente enregistrée.</div>
        @endforelse
    </div>
        
    <!-- Pagination -->
    @if(isset($sales) && $sales->hasPages())
        <div class="mt-6">
            {{ $sales->links() }}
        </div>
    @endif

</div>

<!-- SCRIPT DE RECHERCHE INSTANTANÉE -->
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const searchInput = document.getElementById('liveSearch');
        
        if (!searchInput) return;

        searchInput.addEventListener('input', function(e) {
            let term = e.target.value.toLowerCase().trim();
            let dayGroups = document.querySelectorAll('.sale-day-group');

            dayGroups.forEach(group => {
                let dateText = group.getAttribute('data-date-text').toLowerCase();
                let rows = group.querySelectorAll('.sale-row');
                let groupHasVisibleRow = false;

                rows.forEach(row => {
                    let time = row.querySelector('.sale-time')?.textContent.toLowerCase() || '';
                    let id = row.querySelector('.sale-id')?.textContent.toLowerCase() || '';
                    let user = row.querySelector('.sale-user')?.textContent.toLowerCase() || '';
                    let product = row.querySelector('.sale-product')?.textContent.toLowerCase() || '';
                    let price = row.querySelector('.sale-price')?.textContent.toLowerCase() || '';
                    let total = row.querySelector('.sale-total')?.textContent.toLowerCase() || '';
                    let motif = row.querySelector('.sale-motif')?.textContent.toLowerCase() || '';

                    if (
                        time.includes(term) || 
                        id.includes(term) || 
                        user.includes(term) || 
                        product.includes(term) || 
                        price.includes(term) || 
                        total.includes(term) || 
                        motif.includes(term) || 
                        dateText.includes(term)
                    ) {
                        row.style.display = '';
                        groupHasVisibleRow = true;
                    } else {
                        row.style.display = 'none';
                    }
                });

                if (groupHasVisibleRow || dateText.includes(term)) {
                    group.style.display = '';
                } else {
                    group.style.display = 'none';
                }
            });
        });
    });
</script>
@endsection