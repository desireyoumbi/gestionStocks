@extends('layouts.app')
@section('header_title', ' Produits')
@section('header_subtitle', 'Catalogue et stocks')

@section('content')
<div class="space-y-6">

    <!-- En-tête -->
    <div class="flex justify-between items-center">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Gestion des Produits</h1>
            <p class="text-xs text-gray-500 mt-0.5">Consultez, filtrez et gérez l'inventaire de votre boutique.</p>
        </div>
        
        <div class="flex items-center gap-3">
            {{-- Bouton d'export PDF bien visible --}}
            <a href="{{ route('products.export-pdf') }}" class="bg-white hover:bg-gray-50 text-gray-800 font-bold px-4 py-2.5 rounded-xl transition border border-gray-300 shadow-sm hover:shadow flex items-center gap-2 text-xs">
                <svg class="w-4 h-4 text-red-500" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                </svg>
                Exporter PDF
            </a>

            {{-- Seuls les managers (Admin/Vice) peuvent ajouter un produit --}}
            @if(Auth::user()->isManager())
                <a href="{{ route('products.create') }}" class="bg-amber-500 hover:bg-amber-600 text-gray-900 font-bold px-4 py-2.5 rounded-xl transition shadow-md hover:shadow-lg flex items-center gap-2 text-xs">
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4" />
                    </svg>
                    Ajouter un produit
                </a>
            @endif
        </div>
    </div>

    {{-- Cartes Statistiques (KPIs) inspirées du modèle pro --}}
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
        <!-- Total Produits -->
        <div class="bg-white p-5 rounded-3xl shadow-sm border border-gray-100 flex items-center gap-4">
            <div class="w-12 h-12 rounded-2xl bg-purple-50 text-purple-600 flex items-center justify-center shrink-0">
                <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4" />
                </svg>
            </div>
            <div>
                <div class="text-[11px] font-bold text-gray-400 uppercase tracking-wider">Total Produits</div>
                <div class="text-xl font-black text-gray-900 mt-0.5">{{ $totalProducts ?? count($products) }}</div>
            </div>
        </div>

        <!-- Valeur du Stock -->
        <div class="bg-white p-5 rounded-3xl shadow-sm border border-gray-100 flex items-center gap-4">
            <div class="w-12 h-12 rounded-2xl bg-emerald-50 text-emerald-600 flex items-center justify-center shrink-0">
                <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
            </div>
            <div>
                <div class="text-[11px] font-bold text-gray-400 uppercase tracking-wider">Valeur du Stock</div>
                <div class="text-base font-black text-gray-900 mt-0.5">{{ number_format($stockValue ?? 0, 0, ',', ' ') }} FCFA</div>
            </div>
        </div>

        <!-- Stock Faible -->
        <div class="bg-white p-5 rounded-3xl shadow-sm border border-gray-100 flex items-center gap-4">
            <div class="w-12 h-12 rounded-2xl bg-amber-50 text-amber-600 flex items-center justify-center shrink-0">
                <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                </svg>
            </div>
            <div>
                <div class="text-[11px] font-bold text-gray-400 uppercase tracking-wider">Stock Faible</div>
                <div class="text-xl font-black text-amber-600 mt-0.5">{{ $lowStockCount ?? 0 }}</div>
            </div>
        </div>

        <!-- Rupture de stock -->
        <div class="bg-white p-5 rounded-3xl shadow-sm border border-gray-100 flex items-center gap-4">
            <div class="w-12 h-12 rounded-2xl bg-red-50 text-red-600 flex items-center justify-center shrink-0">
                <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
            </div>
            <div>
                <div class="text-[11px] font-bold text-gray-400 uppercase tracking-wider">Rupture</div>
                <div class="text-xl font-black text-red-600 mt-0.5">{{ $outOfStockCount ?? 0 }}</div>
            </div>
        </div>
    </div>

    {{-- Message flash de succès --}}
    @if(session('succes'))
        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded-2xl relative shadow-sm text-sm">
            {{ session('succes') }}
        </div>
    @endif

    {{-- Filtres avec recherche instantanée via Alpine.js --}}
    <form method="GET" action="{{ route('products.index') }}" x-data="{ search: '{{ request('search') }}' }" class="grid grid-cols-1 md:grid-cols-4 gap-4 bg-white p-6 rounded-3xl shadow-md border border-gray-100">
        <input type="text" 
               name="search" 
               x-model="search" 
               @input.debounce.300ms="
                   const url = new URL(window.location.href);
                   if (search) { url.searchParams.set('search', search); } else { url.searchParams.delete('search'); }
                   fetch(url, { headers: { 'X-Requested-With': 'XMLHttpRequest' } })
                    .then(res => res.text())
                    .then(html => {
                        const parser = new DOMParser();
                        const doc = parser.parseFromString(html, 'text/html');
                        const newTbody = doc.querySelector('tbody');
                        if (newTbody) {
                            document.querySelector('tbody').innerHTML = newTbody.innerHTML;
                        }
                    });
               "
               placeholder="Nom, référence..." 
               class="bg-gray-50 border border-gray-200 rounded-xl px-4 py-2.5 text-sm w-full focus:outline-none focus:ring-2 focus:ring-amber-500/20 focus:border-amber-500">
        
        <select name="category_id" class="bg-gray-50 border border-gray-200 rounded-xl px-4 py-2.5 text-sm w-full focus:outline-none focus:ring-2 focus:ring-amber-500/20 focus:border-amber-500">
            <option value="">Toutes catégories</option>
            @foreach($categories as $category)
                <option value="{{ $category->id }}" {{ $categoryId == $category->id ? 'selected' : '' }}>{{ $category->name }}</option>
            @endforeach
        </select>

        <select name="status" class="bg-gray-50 border border-gray-200 rounded-xl px-4 py-2.5 text-sm w-full focus:outline-none focus:ring-2 focus:ring-amber-500/20 focus:border-amber-500">
            <option value="">Tous statuts</option>
            <option value="disponible" {{ $status == 'disponible' ? 'selected' : '' }}>Disponible</option>
            <option value="rupture" {{ $status == 'rupture' ? 'selected' : '' }}>Rupture</option>
        </select>

        <button type="submit" class="bg-gray-900 hover:bg-gray-800 text-white font-bold px-4 py-2.5 rounded-xl transition shadow-md">Filtrer</button>
    </form>

    {{-- Tableau des produits --}}
    <div class="bg-white rounded-3xl shadow-md border border-gray-100 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-gray-50/70 border-b border-gray-100 text-[11px] font-bold text-gray-400 uppercase tracking-wider">
                        <th class="px-6 py-4">Produit</th>
                        <th class="px-6 py-4">Catégorie</th>
                        <th class="px-6 py-4">Prix Vente</th>
                        @if(Auth::user()->isManager())
                            <th class="px-6 py-4">Prix Achat</th>
                        @endif
                        <th class="px-6 py-4">Stock</th>
                        <th class="px-6 py-4 text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 text-xs">
                    @forelse($products as $product)
                    <tr class="hover:bg-gray-50/50 transition">
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="flex items-center gap-3">
                                {{-- Image miniature cliquable (Façon WhatsApp) --}}
                                @if($product->image)
                                    <img src="{{ asset('storage/' . $product->image) }}" alt="{{ $product->name }}" 
                                         class="w-10 h-10 rounded-xl object-cover border border-gray-100 shadow-sm cursor-pointer hover:opacity-80 transition transform hover:scale-105"
                                         onclick="openImageModal('{{ asset('storage/' . $product->image) }}', '{{ addslashes($product->name) }}')">
                                @else
                                    <div class="w-10 h-10 rounded-xl bg-gray-100 flex items-center justify-center text-gray-400 text-[10px] font-bold border border-gray-200">
                                        IMG
                                    </div>
                                @endif
                                <div>
                                    <div class="font-bold text-gray-900">{{ $product->name }}</div>
                                    <div class="text-[10px] text-gray-400">{{ $product->reference ?? 'Aucune référence' }}</div>
                                </div>
                            </div>
                        </td>
                        <td class="px-6 py-4 text-gray-500">{{ $product->category->name ?? '-' }}</td>
                        <td class="px-6 py-4 font-semibold text-gray-900">{{ number_format($product->selling_price, 0, ',', ' ') }} FCFA</td>
                        
                        @if(Auth::user()->isManager())
                            <td class="px-6 py-4 text-gray-500">{{ number_format($product->purchase_price, 0, ',', ' ') }} FCFA</td>
                        @endif
                        
                        <td class="px-6 py-4">
                            <span class="px-2.5 py-1 rounded-full text-[10px] font-bold {{ $product->quantity <= $product->alert_threshold ? 'bg-red-100 text-red-700' : 'bg-emerald-100 text-emerald-700' }}">
                                {{ $product->quantity }}
                            </span>
                        </td>
                        
                        <td class="px-6 py-4 text-right">
                            <div class="flex items-center justify-end gap-2">
                                {{-- Modification : Admin & Vice-Admin --}}
                                @if(Auth::user()->isManager())
                                    <a href="{{ route('products.edit', $product->id) }}" class="p-2 bg-amber-50 text-amber-600 hover:bg-amber-100 rounded-xl transition" title="Modifier">
                                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                        </svg>
                                    </a>
                                @endif
                                
                                {{-- Suppression : Admin uniquement --}}
                                @if(Auth::user()->isAdmin())
                                    <form action="{{ route('products.destroy', $product->id) }}" method="POST" class="inline-block" onsubmit="return confirm('Supprimer ce produit ?');">
                                        @csrf @method('DELETE')
                                        <button type="submit" class="p-2 bg-red-50 text-red-600 hover:bg-red-100 rounded-xl transition" title="Supprimer">
                                            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                            </svg>
                                        </button>
                                    </form>
                                @endif
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="px-6 py-12 text-center">
                            <div class="flex flex-col items-center justify-center space-y-3">
                                <div class="w-12 h-12 rounded-2xl bg-amber-50 text-amber-500 flex items-center justify-center shadow-inner">
                                    <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4" />
                                    </svg>
                                </div>
                                <p class="text-gray-400 font-medium text-xs">Aucun produit trouvé.</p>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

</div>

{{-- Modale pour afficher l'image en grand (Style WhatsApp) --}}
<div id="imageModal" class="fixed inset-0 z-50 flex items-center justify-center bg-black/80 backdrop-blur-sm hidden" onclick="closeImageModal()">
    <div class="relative max-w-2xl mx-4 p-3 bg-white rounded-3xl shadow-2xl overflow-hidden" onclick="event.stopPropagation()">
        <button onclick="closeImageModal()" class="absolute top-4 right-4 z-10 bg-gray-900/60 hover:bg-gray-900 text-white w-8 h-8 rounded-full flex items-center justify-center font-bold transition">
            &times;
        </button>
        <img id="modalImage" src="" alt="" class="w-full max-h-[75vh] object-contain rounded-2xl">
        <div class="p-4 text-center">
            <h3 id="modalTitle" class="text-sm font-bold text-gray-900"></h3>
        </div>
    </div>
</div>

{{-- Script JS pour gérer l'affichage de la modale --}}
<script>
    function openImageModal(imageUrl, title) {
        const modal = document.getElementById('imageModal');
        const modalImage = document.getElementById('modalImage');
        const modalTitle = document.getElementById('modalTitle');

        modalImage.src = imageUrl;
        modalTitle.textContent = title;
        modal.classList.remove('hidden');
    }

    function closeImageModal() {
        const modal = document.getElementById('imageModal');
        modal.classList.add('hidden');
    }
</script>
@endsection