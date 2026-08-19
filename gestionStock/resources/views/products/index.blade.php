@extends('layouts.app')

@section('content')
<div class="space-y-6">

    <!-- En-tête -->
    <div class="flex justify-between items-center">
        <h1 class="text-2xl font-bold text-gray-900">Gestion des Produits</h1>
        
        {{-- Seuls les managers (Admin/Vice) peuvent ajouter un produit --}}
        @if(Auth::user()->isManager())
            <a href="{{ route('products.create') }}" class="bg-amber-500 hover:bg-amber-600 text-gray-900 font-bold px-4 py-2.5 rounded-xl transition shadow-md hover:shadow-lg">
                + Ajouter un produit
            </a>
        @endif
    </div>

    {{-- Message flash de succès --}}
    @if(session('succes'))
        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded-2xl relative shadow-sm">
            {{ session('succes') }}
        </div>
    @endif

    {{-- Filtres (Ombre plus prononcée : shadow-md) --}}
    <form method="GET" action="{{ route('products.index') }}" class="grid grid-cols-1 md:grid-cols-4 gap-4 bg-white p-6 rounded-3xl shadow-md border border-gray-100">
        <input type="text" name="search" value="{{ request('search') }}" placeholder="Nom, référence..." class="bg-gray-50 border border-gray-200 rounded-xl px-4 py-2.5 text-sm w-full focus:outline-none focus:ring-2 focus:ring-amber-500/20 focus:border-amber-500">
        
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

    {{-- Tableau des produits (Ombre plus prononcée : shadow-md) --}}
    <div class="bg-white rounded-3xl shadow-md border border-gray-100 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-gray-50/70 border-b border-gray-100 text-[11px] font-bold text-gray-400 uppercase tracking-wider">
                        <th class="px-6 py-4">Produit</th>
                        <th class="px-6 py-4">Catégorie</th>
                        <th class="px-6 py-4">Prix Vente</th>
                        {{-- On cache le prix d'achat aux vendeurs --}}
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
                            <div class="font-bold text-gray-900">{{ $product->name }}</div>
                            <div class="text-[10px] text-gray-400">{{ $product->reference }}</div>
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
                        
                        <td class="px-6 py-4 text-right space-x-3">
                            {{-- Modification : Admin & Vice-Admin --}}
                            @if(Auth::user()->isManager())
                                <a href="{{ route('products.edit', $product->id) }}" class="text-amber-600 hover:text-amber-800 font-bold transition">Modifier</a>
                            @endif
                            
                            {{-- Suppression : Admin uniquement --}}
                            @if(Auth::user()->isAdmin())
                                <form action="{{ route('products.destroy', $product->id) }}" method="POST" class="inline-block" onsubmit="return confirm('Supprimer ce produit ?');">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="text-red-500 hover:text-red-700 font-bold transition">Supprimer</button>
                                </form>
                            @endif
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
@endsection