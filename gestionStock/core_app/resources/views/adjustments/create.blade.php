@extends('layouts.app')

@section('header_subtitle', 'Inventaire')
@section('header_title', 'Effectuer un ajustement')

@section('content')
<div class="max-w-2xl mx-auto space-y-6">
    
    <!-- Messages d'erreur globaux -->
    @if ($errors->any())
        <div class="p-4 rounded-2xl bg-rose-500/10 border border-rose-500/20 text-rose-500 text-xs font-semibold">
            <ul class="list-disc list-inside space-y-1">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('adjustments.store') }}" method="POST" class="space-y-6">
        @csrf

        <div class="bg-white dark:bg-gray-950 border border-gray-100 dark:border-gray-800 rounded-2xl p-6 shadow-sm space-y-4">
            
            <!-- Sélection du produit -->
            <div>
                <label class="block text-xs font-bold text-gray-700 dark:text-gray-300 uppercase tracking-wider mb-2">Produit</label>
                <select name="product_id" required class="w-full px-4 py-2.5 bg-gray-50 dark:bg-gray-900 border border-gray-200 dark:border-gray-800 rounded-xl text-xs text-gray-800 dark:text-gray-200 focus:outline-none focus:border-amber-500 transition">
                    <option value="">-- Choisir un produit --</option>
                    @foreach($products as $product)
                        <option value="{{ $product->id }}">
                            {{ $product->name }} (Stock actuel : {{ $product->quantity }})
                        </option>
                    @endforeach
                </select>
            </div>

            <!-- Quantité -->
            <div>
                <label class="block text-xs font-bold text-gray-700 dark:text-gray-300 uppercase tracking-wider mb-2">Quantité à ajuster</label>
                <input type="number" name="quantity" required placeholder="Ex: 5 ou -2" class="w-full px-4 py-2.5 bg-gray-50 dark:bg-gray-900 border border-gray-200 dark:border-gray-800 rounded-xl text-xs text-gray-800 dark:text-gray-200 focus:outline-none focus:border-amber-500 transition">
                <p class="text-[10px] text-gray-400 mt-1.5">Entrez un nombre positif pour ajouter du stock, ou un nombre négatif pour en retirer (ex: -1 pour une casse).</p>
            </div>

            <!-- Motif -->
            <div>
                <label class="block text-xs font-bold text-gray-700 dark:text-gray-300 uppercase tracking-wider mb-2">Motif de l'ajustement</label>
                <input type="text" name="motif" required placeholder="Ex: Erreur d'inventaire, Casse, Vol..." class="w-full px-4 py-2.5 bg-gray-50 dark:bg-gray-900 border border-gray-200 dark:border-gray-800 rounded-xl text-xs text-gray-800 dark:text-gray-200 focus:outline-none focus:border-amber-500 transition">
            </div>

        </div>

        <!-- Boutons d'action -->
        <div class="flex items-center justify-end gap-3">
            <a href="{{ route('adjustments.index') }}" class="px-5 py-2.5 bg-gray-100 dark:bg-gray-900 hover:bg-gray-200 dark:hover:bg-gray-800 text-gray-700 dark:text-gray-300 font-bold text-xs rounded-xl border border-gray-200 dark:border-gray-800 transition">
                Annuler
            </a>
            <button type="submit" class="px-6 py-2.5 bg-amber-500 hover:bg-amber-600 text-gray-900 font-bold text-xs rounded-xl shadow-lg shadow-amber-500/20 transition">
                Enregistrer l'ajustement
            </button>
        </div>

    </form>
</div>
@endsection