@extends('layouts.app')

@section('content')
<div class="max-w-3xl mx-auto px-4 py-8">
    <div class="bg-white shadow-xl rounded-3xl border border-gray-100 p-8">
        
        <!-- En-tête -->
        <div class="mb-8 border-b border-gray-100 pb-4">
            <h2 class="text-2xl font-bold text-gray-900">Ajouter un nouveau produit</h2>
            <p class="text-xs text-gray-400 mt-1">Renseignez les informations de base du produit. Le stock initial commencera à 0.</p>
        </div>

        <form method="POST" action="{{ route('products.store') }}" enctype="multipart/form-data" class="space-y-6">
            @csrf

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                {{-- Nom du produit --}}
                <div>
                    <label for="name" class="block text-xs font-bold text-gray-500 uppercase tracking-wider mb-2">Nom du produit *</label>
                    <input type="text" name="name" id="name" value="{{ old('name') }}" required placeholder="Ex: Ordinateur Portable HP" class="bg-gray-50 border border-gray-200 rounded-xl px-4 py-3 text-sm w-full focus:outline-none focus:ring-2 focus:ring-amber-500/20 focus:border-amber-500 transition">
                    @error('name') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
                </div>

                {{-- Référence --}}
                <div>
                    <label for="reference" class="block text-xs font-bold text-gray-500 uppercase tracking-wider mb-2">Référence / SKU</label>
                    <input type="text" name="reference" id="reference" value="{{ old('reference') }}" placeholder="Ex: REF-HP-001" class="bg-gray-50 border border-gray-200 rounded-xl px-4 py-3 text-sm w-full focus:outline-none focus:ring-2 focus:ring-amber-500/20 focus:border-amber-500 transition">
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
                            <option value="{{ $category->id }}" {{ old('category_id') == $category->id ? 'selected' : '' }}>
                                {{ $category->name }}
                            </option>
                        @endforeach
                    </select>
                    @error('category_id') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
                </div>

                {{-- Seuil d'alerte stock bas --}}
                <div>
                    <label for="alert_threshold" class="block text-xs font-bold text-gray-500 uppercase tracking-wider mb-2">Seuil d'alerte stock bas *</label>
                    <input type="number" name="alert_threshold" id="alert_threshold" value="{{ old('alert_threshold', 5) }}" required class="bg-gray-50 border border-gray-200 rounded-xl px-4 py-3 text-sm w-full focus:outline-none focus:ring-2 focus:ring-amber-500/20 focus:border-amber-500 transition">
                    @error('alert_threshold') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                {{-- Prix d'achat --}}
                <div>
                    <label for="purchase_price" class="block text-xs font-bold text-gray-500 uppercase tracking-wider mb-2">Prix d'achat (FCFA) *</label>
                    <input type="number" step="0.01" name="purchase_price" id="purchase_price" value="{{ old('purchase_price') }}" required placeholder="0" class="bg-gray-50 border border-gray-200 rounded-xl px-4 py-3 text-sm w-full focus:outline-none focus:ring-2 focus:ring-amber-500/20 focus:border-amber-500 transition">
                    @error('purchase_price') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
                </div>

                {{-- Prix de vente --}}
                <div>
                    <label for="selling_price" class="block text-xs font-bold text-gray-500 uppercase tracking-wider mb-2">Prix de vente (FCFA) *</label>
                    <input type="number" step="0.01" name="selling_price" id="selling_price" value="{{ old('selling_price') }}" required placeholder="0" class="bg-gray-50 border border-gray-200 rounded-xl px-4 py-3 text-sm w-full focus:outline-none focus:ring-2 focus:ring-amber-500/20 focus:border-amber-500 transition">
                    @error('selling_price') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
                </div>
            </div>

            {{-- Image du produit (Nouveau champ) --}}
            <div>
                <label for="image" class="block text-xs font-bold text-gray-500 uppercase tracking-wider mb-2">Image du produit</label>
                <input type="file" name="image" id="image" accept="image/*" class="w-full bg-gray-50 border border-gray-200 rounded-xl px-4 py-2.5 text-sm text-gray-700 file:mr-4 file:py-2 file:px-4 file:rounded-xl file:border-0 file:text-xs file:font-bold file:bg-amber-500/10 file:text-amber-600 hover:file:bg-amber-500/20 focus:outline-none focus:ring-2 focus:ring-amber-500/20 focus:border-amber-500 transition">
                @error('image') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
            </div>

            <div class="flex items-center justify-end space-x-3 pt-6 border-t border-gray-100">
                <a href="{{ route('products.index') }}" class="bg-gray-100 hover:bg-gray-200 text-gray-700 font-bold px-5 py-3 rounded-xl transition text-sm">
                    Annuler
                </a>
                <button type="submit" class="bg-amber-500 hover:bg-amber-600 text-gray-900 font-bold px-6 py-3 rounded-xl transition shadow-md hover:shadow-lg text-sm">
                    Enregistrer le produit
                </button>
            </div>
        </form>
    </div>
</div>
@endsection