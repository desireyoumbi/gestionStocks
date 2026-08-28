@extends('layouts.app')

@section('content')
<div class="max-w-3xl mx-auto px-4 py-8">
    <div class="bg-white shadow-xl rounded-3xl border border-gray-100 p-8">
        
        <!-- En-tête -->
        <div class="mb-8 border-b border-gray-100 pb-4">
            <h2 class="text-2xl font-bold text-gray-900">Modifier le produit : {{ $product->name }}</h2>
            <p class="text-xs text-gray-400 mt-1">Mettez à jour les informations ou l'image de ce produit.</p>
        </div>

        <form method="POST" action="{{ route('products.update', $product->id) }}" enctype="multipart/form-data" class="space-y-6">
            @csrf
            @method('PUT')

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                {{-- Nom du produit --}}
                <div>
                    <label for="name" class="block text-xs font-bold text-gray-500 uppercase tracking-wider mb-2">Nom du produit *</label>
                    <input type="text" name="name" id="name" value="{{ old('name', $product->name) }}" required class="bg-gray-50 border border-gray-200 rounded-xl px-4 py-3 text-sm w-full focus:outline-none focus:ring-2 focus:ring-amber-500/20 focus:border-amber-500 transition">
                    @error('name') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
                </div>

                {{-- Référence --}}
                <div>
                    <label for="reference" class="block text-xs font-bold text-gray-500 uppercase tracking-wider mb-2">Référence / SKU</label>
                    <input type="text" name="reference" id="reference" value="{{ old('reference', $product->reference) }}" class="bg-gray-50 border border-gray-200 rounded-xl px-4 py-3 text-sm w-full focus:outline-none focus:ring-2 focus:ring-amber-500/20 focus:border-amber-500 transition">
                    @error('reference') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                {{-- Catégorie --}}
                <div>
                    <label for="category_id" class="block text-xs font-bold text-gray-500 uppercase tracking-wider mb-2">Catégorie *</label>
                    <select name="category_id" id="category_id" required class="bg-gray-50 border border-gray-200 rounded-xl px-4 py-3 text-sm w-full focus:outline-none focus:ring-2 focus:ring-amber-500/20 focus:border-amber-500 transition">
                        <option value="">Sélectionner une catégorie</option>
                        @foreach($categories as $category)
                            <option value="{{ $category->id }}" {{ old('category_id', $product->category_id) == $category->id ? 'selected' : '' }}>
                                {{ $category->name }}
                            </option>
                        @endforeach
                    </select>
                    @error('category_id') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
                </div>

                {{-- Statut --}}
                <div>
                    <label for="status" class="block text-xs font-bold text-gray-500 uppercase tracking-wider mb-2">Statut *</label>
                    <select name="status" id="status" required class="bg-gray-50 border border-gray-200 rounded-xl px-4 py-3 text-sm w-full focus:outline-none focus:ring-2 focus:ring-amber-500/20 focus:border-amber-500 transition">
                        <option value="disponible" {{ old('status', $product->status) == 'disponible' ? 'selected' : '' }}>Disponible</option>
                        <option value="rupture" {{ old('status', $product->status) == 'rupture' ? 'selected' : '' }}>Rupture</option>
                    </select>
                    @error('status') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                {{-- Prix d'achat --}}
                <div>
                    <label for="purchase_price" class="block text-xs font-bold text-gray-500 uppercase tracking-wider mb-2">Prix d'achat (FCFA) *</label>
                    <input type="number" step="0.01" name="purchase_price" id="purchase_price" value="{{ old('purchase_price', $product->purchase_price) }}" required class="bg-gray-50 border border-gray-200 rounded-xl px-4 py-3 text-sm w-full focus:outline-none focus:ring-2 focus:ring-amber-500/20 focus:border-amber-500 transition">
                    @error('purchase_price') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
                </div>

                {{-- Prix de vente --}}
                <div>
                    <label for="selling_price" class="block text-xs font-bold text-gray-500 uppercase tracking-wider mb-2">Prix de vente (FCFA) *</label>
                    <input type="number" step="0.01" name="selling_price" id="selling_price" value="{{ old('selling_price', $product->selling_price) }}" required class="bg-gray-50 border border-gray-200 rounded-xl px-4 py-3 text-sm w-full focus:outline-none focus:ring-2 focus:ring-amber-500/20 focus:border-amber-500 transition">
                    @error('selling_price') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                {{-- Quantité --}}
                <div>
                    <label for="quantity" class="block text-xs font-bold text-gray-500 uppercase tracking-wider mb-2">Quantité en stock *</label>
                    <input type="number" name="quantity" id="quantity" value="{{ old('quantity', $product->quantity) }}" required class="bg-gray-50 border border-gray-200 rounded-xl px-4 py-3 text-sm w-full focus:outline-none focus:ring-2 focus:ring-amber-500/20 focus:border-amber-500 transition">
                    @error('quantity') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
                </div>

                {{-- Seuil d'alerte --}}
                <div>
                    <label for="alert_threshold" class="block text-xs font-bold text-gray-500 uppercase tracking-wider mb-2">Seuil d'alerte stock bas *</label>
                    <input type="number" name="alert_threshold" id="alert_threshold" value="{{ old('alert_threshold', $product->alert_threshold) }}" required class="bg-gray-50 border border-gray-200 rounded-xl px-4 py-3 text-sm w-full focus:outline-none focus:ring-2 focus:ring-amber-500/20 focus:border-amber-500 transition">
                    @error('alert_threshold') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
                </div>
            </div>

            {{-- Section Image Actuelle et Modification --}}
            <div class="p-5 bg-gray-50/50 border border-gray-200 rounded-2xl space-y-4">
                <label class="block text-xs font-bold text-gray-500 uppercase tracking-wider">Image du produit</label>
                
                @if($product->image)
                    <div class="flex items-center gap-4">
                        <img src="{{ asset('storage/' . $product->image) }}" alt="{{ $product->name }}" class="w-16 h-16 rounded-xl object-cover border border-gray-200 shadow-sm">
                        <div>
                            <p class="text-xs text-gray-600 font-medium">Image actuelle enregistrée</p>
                            <p class="text-[11px] text-gray-400">Sélectionnez un nouveau fichier ci-dessous pour la remplacer.</p>
                        </div>
                    </div>
                @else
                    <p class="text-xs text-gray-400 italic">Aucune image associée pour le moment.</p>
                @endif

                <div>
                    <input type="file" name="image" id="image" accept="image/*" class="w-full bg-white border border-gray-200 rounded-xl px-4 py-2.5 text-sm text-gray-700 file:mr-4 file:py-2 file:px-4 file:rounded-xl file:border-0 file:text-xs file:font-bold file:bg-amber-500/10 file:text-amber-600 hover:file:bg-amber-500/20 focus:outline-none focus:ring-2 focus:ring-amber-500/20 focus:border-amber-500 transition">
                    @error('image') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
                </div>
            </div>

            <div class="flex items-center justify-end space-x-3 pt-6 border-t border-gray-100">
                <a href="{{ route('products.index') }}" class="bg-gray-100 hover:bg-gray-200 text-gray-700 font-bold px-5 py-3 rounded-xl transition text-sm">
                    Annuler
                </a>
                <button type="submit" class="bg-amber-500 hover:bg-amber-600 text-gray-900 font-bold px-6 py-3 rounded-xl transition shadow-md hover:shadow-lg text-sm">
                    Mettre à jour
                </button>
            </div>
        </form>
    </div>
</div>
@endsection