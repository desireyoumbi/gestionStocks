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
                                <th class="p-4 text-right">Statut</th>
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
                                        <td class="p-4 text-gray-500 sale-motif">{{ $sale->motif ?? '-' }}</td>
                                        <td class="p-4 text-right">
                                            <span class="inline-flex items-center px-2.5 py-1 rounded-full text-[10px] font-bold bg-rose-50 text-rose-600 border border-rose-100">
                                                Validé
                                            </span>
                                        </td>
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

<!-- MODALE DE SUCCÈS ET REÇU WHATSAPP INTÉGRÉE ICI -->
@if(session('success_sale'))
@php
    $sale = session('success_sale');
    
    // Récupération sécurisée du nom de la boutique basé sur la colonne 'nom'
    $boutiqueName = 'E-STOCKY';
    if ($sale && $sale->user && isset($sale->user->boutique) && $sale->user->boutique) {
        $boutiqueName = $sale->user->boutique->nom;
    } elseif (auth()->check() && auth()->user()->boutique) {
        $boutiqueName = auth()->user()->boutique->nom;
    }
    
    $totalAmount = 0;
    if ($sale && $sale->products) {
        foreach($sale->products as $product) {
            $qty = $product->pivot->qty ?? ($product->pivot->quantity ?? 1);
            $totalAmount += $qty * $product->selling_price;
        }
    }

    $whatsappText  = "*REÇU D'ACHAT - " . urlencode($boutiqueName) . "*%0A";
    $whatsappText .= "--------------------------------%0A";
    $whatsappText .= "📅 Date : " . ($sale->created_at ? date('d/m/Y H:i', strtotime($sale->created_at)) : date('d/m/Y H:i')) . "%0A";
    $whatsappText .= "🧾 Reçu N° : #" . $sale->id . "%0A";
    if(!empty($sale->motif)) {
        $whatsappText .= "📌 Motif : " . urlencode($sale->motif) . "%0A";
    }
    $whatsappText .= "%0A*Articles :*%0A";
    
    if ($sale && $sale->products) {
        foreach($sale->products as $product) {
            $qty = $product->pivot->qty ?? ($product->pivot->quantity ?? 1);
            $subtotal = $qty * $product->selling_price;
            $whatsappText .= "▪ " . $qty . "x " . $product->name . " (" . number_format($product->selling_price, 0, ',', ' ') . " F) = *" . number_format($subtotal, 0, ',', ' ') . " FCFA*%0A";
        }
    }
    
    $whatsappText .= "--------------------------------%0A";
    $whatsappText .= "💰 *TOTAL : " . number_format($totalAmount, 0, ',', ' ') . " FCFA*%0A";
    $whatsappText .= "--------------------------------%0A";
    $whatsappText .= "🙏 Merci pour votre confiance ! À très bientôt.";

    $phone = preg_replace('/[^0-9]/', '', $sale->client_phone ?? '');
    if (strlen($phone) === 9) {
        $phone = '237' . $phone;
    }
    
    if (!empty($phone)) {
        $whatsappUrl = "https://web.whatsapp.com/send?phone=" . $phone . "&text=" . $whatsappText;
    } else {
        $whatsappUrl = "https://web.whatsapp.com/";
    }
@endphp

<div id="modal-success-sale" class="fixed inset-0 z-50 flex items-center justify-center bg-gray-950/70 p-4 backdrop-blur-sm">
    <div class="w-full max-w-md space-y-6 rounded-3xl bg-white dark:bg-gray-950 border border-gray-100 dark:border-gray-800 p-6 shadow-2xl">
        
        <div class="text-center space-y-2">
            <div class="mx-auto flex h-12 w-12 items-center justify-center rounded-2xl bg-emerald-500/10 text-emerald-500 text-xl font-bold shadow-sm">
                ✓
            </div>
            <h3 class="text-lg font-black text-gray-900 dark:text-gray-100">Vente validée avec succès !</h3>
            <p class="text-xs font-medium text-gray-400">Le stock a été mis à jour et la vente enregistrée.</p>
        </div>

        <div class="rounded-2xl bg-gray-50 dark:bg-gray-900 border border-gray-200 dark:border-gray-800 p-4 space-y-3 text-xs font-medium text-gray-600 dark:text-gray-300">
            <div class="flex justify-between border-b border-gray-200 dark:border-gray-800 pb-2">
                <span class="font-bold text-gray-900 dark:text-gray-100">Reçu #{{ $sale->id }}</span>
                <span class="text-gray-400">{{ date('d/m/Y H:i') }}</span>
            </div>
            
            <div class="space-y-1.5 max-h-32 overflow-y-auto">
                @if($sale->products)
                    @foreach($sale->products as $product)
                        @php $q = $product->pivot->qty ?? ($product->pivot->quantity ?? 1); @endphp
                        <div class="flex justify-between">
                            <span>{{ $q }}x {{ $product->name }}</span>
                            <span class="font-bold text-gray-900 dark:text-gray-100">{{ number_format($q * $product->selling_price, 0, ',', ' ') }} F</span>
                        </div>
                    @endforeach
                @endif
            </div>

            <div class="flex justify-between border-t border-gray-200 dark:border-gray-800 pt-2 text-sm font-black text-gray-900 dark:text-gray-100">
                <span>TOTAL</span>
                <span class="text-emerald-500">{{ number_format($totalAmount, 0, ',', ' ') }} FCFA</span>
            </div>
        </div>

        <div class="flex flex-col gap-2.5">
            <a href="{{ $whatsappUrl }}" target="_blank" rel="noopener noreferrer" 
               onclick="document.getElementById('modal-success-sale').style.display='none';" 
               class="flex items-center justify-center gap-2 rounded-xl bg-emerald-500 hover:bg-emerald-600 px-4 py-3 text-xs font-bold text-gray-950 shadow-md transition">
                <svg class="w-4 h-4 fill-current" viewBox="0 0 24 24">
                    <path d="M.057 24l1.687-6.163c-1.041-1.804-1.588-3.849-1.587-5.946.003-6.556 5.338-11.891 11.893-11.891 3.181.001 6.167 1.24 8.413 3.488 2.245 2.248 3.481 5.236 3.48 8.414-.003 6.557-5.338 11.892-11.893 11.892-1.99-.001-3.951-.5-5.688-1.448l-6.305 1.654zm6.597-3.807c1.676.995 3.276 1.591 5.392 1.592 5.448 0 9.886-4.434 9.889-9.885.002-5.462-4.415-9.89-9.881-9.892-5.452 0-9.887 4.434-9.889 9.884-.001 2.225.651 3.891 1.746 5.634l-.999 3.648 3.742-.981zm11.387-5.464c-.074-.124-.272-.198-.57-.347-.297-.149-1.758-.868-2.031-.967-.272-.099-.47-.149-.669.149-.198.297-.768.967-.941 1.165-.173.198-.347.223-.644.074-.297-.149-1.255-.462-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.297-.347.446-.521.151-.172.2-.296.3-.495.099-.198.05-.372-.025-.521-.075-.148-.669-1.611-.916-2.206-.242-.579-.487-.501-.669-.51l-.57-.01c-.198 0-.52.074-.792.372s-1.04 1.016-1.04 2.479 1.065 2.876 1.213 3.074c.149.198 2.095 3.2 5.076 4.487.709.306 1.263.489 1.694.626.712.226 1.36.194 1.872.118.571-.085 1.758-.719 2.006-1.413.248-.695.248-1.29.173-1.414z"/>
                </svg>
                Envoyer le reçu sur WhatsApp
            </a>

            <button onclick="document.getElementById('modal-success-sale').remove()" class="w-full rounded-xl bg-gray-100 dark:bg-gray-900 hover:bg-gray-200 dark:hover:bg-gray-800 px-4 py-3 text-xs font-bold text-gray-700 dark:text-gray-300 transition">
                Fermer / Nouvelle vente
            </button>
        </div>

    </div>
</div>
@endif

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