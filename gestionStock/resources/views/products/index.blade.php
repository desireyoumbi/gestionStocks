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
            <a href="{{ route('products.export-pdf') }}" class="bg-white hover:bg-gray-50 text-gray-800 font-bold px-4 py-2.5 rounded-xl transition border border-gray-300 shadow-sm hover:shadow flex items-center gap-2 text-xs">
                <svg class="w-4 h-4 text-red-500" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                </svg>
                Exporter PDF
            </a>

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

    {{-- Cartes Statistiques (KPIs) --}}
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
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

        <div class="bg-white p-5 rounded-3xl shadow-sm border border-gray-100 flex items-center gap-4">
            <div class="w-12 h-12 rounded-2xl bg-emerald-50 text-emerald-600 flex items-center justify-center shrink-0">
                <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 1.343 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08.402-2.599 1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
            </div>
            <div>
                <div class="text-[11px] font-bold text-gray-400 uppercase tracking-wider">Valeur du Stock</div>
                <div class="text-base font-black text-gray-900 mt-0.5">{{ number_format($stockValue ?? 0, 0, ',', ' ') }} FCFA</div>
            </div>
        </div>

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

    @if(session('succes'))
        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded-2xl relative shadow-sm text-sm">
            {{ session('succes') }}
        </div>
    @endif

    {{-- Filtres --}}
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

                                @if($product->image)

                                    @php
                                        $imageUrl = 'https://xgvcyiaflsawvfmjuoxx.supabase.co/storage/v1/object/public/products/' . $product->image;
                                    @endphp

                                    <img src="{{ $imageUrl }}"
                                         alt="{{ $product->name }}" 
                                         class="w-10 h-10 rounded-xl object-cover border border-gray-100 shadow-sm cursor-pointer hover:opacity-80 transition transform hover:scale-105"
                                         onclick="openImageModal('{{ $imageUrl }}', '{{ addslashes($product->name) }}')">

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

                        <td class="px-6 py-4 font-semibold text-gray-900">
                            {{ number_format($product->selling_price, 0, ',', ' ') }} FCFA
                        </td>
                        
                        @if(Auth::user()->isManager())
                            <td class="px-6 py-4 text-gray-500">
                                {{ number_format($product->purchase_price, 0, ',', ' ') }} FCFA
                            </td>
                        @endif
                        
                        <td class="px-6 py-4">
                            <span class="px-2.5 py-1 rounded-full text-[10px] font-bold {{ $product->quantity <= $product->alert_threshold ? 'bg-red-100 text-red-700' : 'bg-emerald-100 text-emerald-700' }}">
                                {{ $product->quantity }}
                            </span>
                        </td>
                        
                        <td class="px-6 py-4 text-right">
                            <div class="flex items-center justify-end gap-2">

                                {{-- Bouton QR Code sécurisé avec attribut data pour éviter les bugs de rendu --}}
                                <button type="button" 
                                        data-name="{{ $product->name }}"
                                        data-ref="{{ $product->reference ?? 'PROD-'.$product->id }}"
                                        data-svg="{!! base64_encode(QrCode::size(200)->format('svg')->generate($product->reference ?? 'PROD-' . $product->id)) !!}"
                                        onclick="openQrModalFromButton(this)" 
                                        class="p-2 bg-indigo-50 text-indigo-600 hover:bg-indigo-100 rounded-xl transition inline-flex items-center justify-center" 
                                        title="Afficher le QR Code">
                                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v1m6 11h2m-6 0h-2v4m0-11v3m0 0h.01M12 12h4.01M16 20h4M4 12h4m12 0h.01M5 8h2a1 1 0 001-1V5a1 1 0 00-1-1H5a1 1 0 001 1v2a1 1 0 01-1 1zm12 0h2a1 1 0 001-1V5a1 1 0 00-1-1h-2v2a1 1 0 001 1zM5 20h2a1 1 0 001-1v-2a1 1 0 00-1-1H5v2a1 1 0 001 1z" />
                                    </svg>
                                </button>

                                @if(Auth::user()->isManager())
                                    <a href="{{ route('products.edit', $product->id) }}" class="p-2 bg-amber-50 text-amber-600 hover:bg-amber-100 rounded-xl transition" title="Modifier">
                                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                        </svg>
                                    </a>
                                @endif
                                
                                @if(Auth::user()->isAdmin())
                                    <form action="{{ route('products.destroy', $product->id) }}" method="POST" class="inline-block" onsubmit="return confirm('Supprimer ce produit ?');">
                                        @csrf
                                        @method('DELETE')

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

{{-- Modale Image --}}
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

{{-- Modale QR Code --}}
<div id="qrModal" class="fixed inset-0 z-50 flex items-center justify-center bg-black/80 backdrop-blur-sm hidden" onclick="closeQrModal()">

    <div id="qrPrintArea" class="relative max-w-sm w-full mx-4 p-6 bg-white rounded-3xl shadow-2xl flex flex-col items-center text-center" onclick="event.stopPropagation()">

        <button onclick="closeQrModal()" class="absolute top-4 right-4 z-10 bg-gray-100 hover:bg-gray-200 text-gray-700 w-8 h-8 rounded-full flex items-center justify-center font-bold transition print:hidden">
            &times;
        </button>
        
        <h3 class="text-xs font-bold text-gray-400 uppercase tracking-wider mb-1">QR Code Produit</h3>

        <h2 id="qrProductName" class="text-base font-black text-gray-900 mb-4"></h2>

        <div id="qrCodeContainer" class="p-4 bg-gray-50 rounded-2xl border border-gray-100 mb-4 flex items-center justify-center [&>svg]:w-36 [&>svg]:h-36">
            <!-- SVG injecté ici -->
        </div>

        <span id="qrProductRef" class="text-xs font-mono bg-gray-100 px-3 py-1.5 rounded-xl text-gray-600 font-bold mb-4"></span>

        <button onclick="printQrCode()" class="w-full bg-gray-900 hover:bg-gray-800 text-white font-bold py-2.5 rounded-xl transition text-xs shadow-md print:hidden">
            Imprimer l'étiquette
        </button>

    </div>
</div>

{{-- Style CSS dédié à l'impression --}}
<style>
@media print {
    body * {
        visibility: hidden;
    }

    #qrPrintArea,
    #qrPrintArea * {
        visibility: visible;
    }

    #qrPrintArea {
        position: fixed;
        left: 50%;
        top: 50%;
        transform: translate(-50%, -50%);
        width: 320px;
        box-shadow: none !important;
        border: none !important;
        margin: 0;
        padding: 20px;
        background-color: white !important;
    }
}
</style>

<script>
    function openImageModal(imageUrl, title) {
        document.getElementById('modalImage').src = imageUrl;
        document.getElementById('modalTitle').textContent = title;
        document.getElementById('imageModal').classList.remove('hidden');
    }

    function closeImageModal() {
        document.getElementById('imageModal').classList.add('hidden');
    }

    function openQrModalFromButton(button) {
        const name = button.getAttribute('data-name');
        const ref = button.getAttribute('data-ref');
        const base64Svg = button.getAttribute('data-svg');
        const svgContent = atob(base64Svg);

        document.getElementById('qrProductName').textContent = name;
        document.getElementById('qrProductRef').textContent = ref;
        document.getElementById('qrCodeContainer').innerHTML = svgContent;
        
        document.getElementById('qrModal').classList.remove('hidden');
    }

    function closeQrModal() {
        document.getElementById('qrModal').classList.add('hidden');
    }

    function printQrCode() {
        window.print();
    }
</script>

@endsection