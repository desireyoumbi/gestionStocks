@extends('layouts.app')

@section('content')
<div class="max-w-2xl mx-auto px-4 py-12">
    <div class="bg-white shadow sm:rounded-lg p-6">
        <h2 class="text-xl font-bold text-gray-900 mb-6">Modifier le produit : {{ $product->name }}</h2>

        <form method="POST" action="{{ route('products.update', $product->id) }}">
            @csrf
            @method('PUT')

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                {{-- Nom du produit --}}
                <div class="mb-4">
                    <label for="name" class="block text-sm font-medium text-gray-700">Nom du produit *</label>
                    <input type="text" name="name" id="name" value="{{ old('name', $product->name) }}" required class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm p-2 focus:ring-blue-500 focus:border-blue-500">
                    @error('name') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                </div>

                {{-- Référence --}}
                <div class="mb-4">
                    <label for="reference" class="block text-sm font-medium text-gray-700">Référence / SKU</label>
                    <input type="text" name="reference" id="reference" value="{{ old('reference', $product->reference) }}" class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm p-2 focus:ring-blue-500 focus:border-blue-500">
                    @error('reference') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                {{-- Catégorie --}}
                <div class="mb-4">
                    <label for="category_id" class="block text-sm font-medium text-gray-700">Catégorie *</label>
                    <select name="category_id" id="category_id" required class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm p-2 focus:ring-blue-500 focus:border-blue-500">
                        <option value="">Sélectionner une catégorie</option>
                        @foreach($categories as $category)
                            <option value="{{ $category->id }}" {{ old('category_id', $product->category_id) == $category->id ? 'selected' : '' }}>
                                {{ $category->name }}
                            </option>
                        @endforeach
                    </select>
                    @error('category_id') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                </div>

                {{-- Statut --}}
                <div class="mb-4">
                    <label for="status" class="block text-sm font-medium text-gray-700">Statut *</label>
                    <select name="status" id="status" required class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm p-2 focus:ring-blue-500 focus:border-blue-500">
                        <option value="disponible" {{ old('status', $product->status) == 'disponible' ? 'selected' : '' }}>Disponible</option>
                        <option value="rupture" {{ old('status', $product->status) == 'rupture' ? 'selected' : '' }}>Rupture</option>
                    </select>
                    @error('status') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                {{-- Prix d'achat --}}
                <div class="mb-4">
                    <label for="purchase_price" class="block text-sm font-medium text-gray-700">Prix d'achat (FCFA) *</label>
                    <input type="number" step="0.01" name="purchase_price" id="purchase_price" value="{{ old('purchase_price', $product->purchase_price) }}" required class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm p-2 focus:ring-blue-500 focus:border-blue-500">
                    @error('purchase_price') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                </div>

                {{-- Prix de vente --}}
                <div class="mb-4">
                    <label for="selling_price" class="block text-sm font-medium text-gray-700">Prix de vente (FCFA) *</label>
                    <input type="number" step="0.01" name="selling_price" id="selling_price" value="{{ old('selling_price', $product->selling_price) }}" required class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm p-2 focus:ring-blue-500 focus:border-blue-500">
                    @error('selling_price') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-6">
                {{-- Quantité --}}
                <div>
                    <label for="quantity" class="block text-sm font-medium text-gray-700">Quantité en stock *</label>
                    <input type="number" name="quantity" id="quantity" value="{{ old('quantity', $product->quantity) }}" required class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm p-2 focus:ring-blue-500 focus:border-blue-500">
                    @error('quantity') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                </div>

                {{-- Seuil d'alerte --}}
                <div>
                    <label for="alert_threshold" class="block text-sm font-medium text-gray-700">Seuil d'alerte stock bas *</label>
                    <input type="number" name="alert_threshold" id="alert_threshold" value="{{ old('alert_threshold', $product->alert_threshold) }}" required class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm p-2 focus:ring-blue-500 focus:border-blue-500">
                    @error('alert_threshold') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                </div>
            </div>

            <div class="flex justify-end space-x-3">
                <a href="{{ route('products.index') }}" class="bg-gray-200 text-gray-700 px-4 py-2 rounded-md hover:bg-gray-300 transition">Annuler</a>
                <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded-md hover:bg-blue-700 transition">Mettre à jour</button>
            </div>
        </form>
    </div>
</div>
@endsection