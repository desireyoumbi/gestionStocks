@extends('layouts.app')

@section('header_subtitle', 'Enregistrer une nouvelle entrée de stock')
@section('header_title', 'Nouvel Approvisionnement')

@section('content')
<div class="max-w-2xl mx-auto">
    <div class="bg-white dark:bg-gray-950 border border-gray-100 dark:border-gray-800 rounded-2xl p-6 shadow-sm">
        
        <form action="{{ route('supplies.store') }}" method="POST" class="space-y-5">
            @csrf

            <!-- Erreurs globales -->
            @if($errors->any())
                <div class="p-4 rounded-xl bg-rose-500/10 border border-rose-500/20 text-rose-500 text-xs font-semibold">
                    <ul class="list-disc pl-4 space-y-1">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <!-- Sélection du Produit -->
            <div>
                <label class="block text-xs font-bold uppercase tracking-wider text-gray-500 dark:text-gray-400 mb-2">Produit</label>
                <select name="product_id" required class="w-full px-4 py-3 bg-gray-50 dark:bg-gray-900 border border-gray-200 dark:border-gray-800 rounded-xl text-xs font-medium text-gray-800 dark:text-gray-200 focus:outline-none focus:border-amber-500 transition">
                    <option value="">Sélectionnez un produit...</option>
                    @foreach($products as $product)
                        <option value="{{ $product->id }}" {{ old('product_id') == $product->id ? 'selected' : '' }}>
                            {{ $product->name }} (Stock actuel : {{ $product->stock }})
                        </option>
                    @endforeach
                </select>
            </div>

            <!-- Quantité -->
            <div>
                <label class="block text-xs font-bold uppercase tracking-wider text-gray-500 dark:text-gray-400 mb-2">Quantité approvisionnée</label>
                <input type="number" name="quantity" min="1" value="{{ old('quantity', 1) }}" required class="w-full px-4 py-3 bg-gray-50 dark:bg-gray-900 border border-gray-200 dark:border-gray-800 rounded-xl text-xs font-medium text-gray-800 dark:text-gray-200 focus:outline-none focus:border-amber-500 transition">
            </div>

            <!-- Motif / Référence -->
            <div>
                <label class="block text-xs font-bold uppercase tracking-wider text-gray-500 dark:text-gray-400 mb-2">Motif / Référence (Optionnel)</label>
                <input type="text" name="motif" value="{{ old('motif') }}" placeholder="Ex: Réception fournisseur #FR-902" class="w-full px-4 py-3 bg-gray-50 dark:bg-gray-900 border border-gray-200 dark:border-gray-800 rounded-xl text-xs font-medium text-gray-800 dark:text-gray-200 focus:outline-none focus:border-amber-500 transition">
            </div>

            <!-- Boutons d'action -->
            <div class="flex items-center justify-end gap-3 pt-4 border-t border-gray-100 dark:border-gray-900">
                <a href="{{ route('supplies.index') }}" class="px-5 py-2.5 bg-gray-100 dark:bg-gray-900 hover:bg-gray-200 dark:hover:bg-gray-800 text-gray-700 dark:text-gray-300 font-bold text-xs rounded-xl transition">
                    Annuler
                </a>
                <button type="submit" class="px-5 py-2.5 bg-emerald-500 hover:bg-emerald-600 text-gray-950 font-bold text-xs rounded-xl shadow-lg shadow-emerald-500/20 transition">
                    Enregistrer l'approvisionnement
                </button>
            </div>
        </form>

    </div>
</div>
@endsection