@extends('layouts.app')

@section('content')
<div class="max-w-4xl mx-auto space-y-6">

    <!-- En-tête -->
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Détails du Mouvement #{{ $movement->id }}</h1>
            <p class="text-sm text-gray-500 dark:text-gray-400">Informations complètes sur ce bon de stock.</p>
        </div>
        <div class="flex gap-2">
            <a href="{{ route('movements.index') }}" class="bg-gray-200 dark:bg-gray-700 hover:bg-gray-300 text-gray-700 dark:text-gray-300 px-4 py-2 rounded-xl text-sm transition">
                ← Retour
            </a>
            <a href="{{ route('movements.edit', $movement->id) }}" class="bg-amber-500 hover:bg-amber-600 text-gray-900 font-semibold px-4 py-2 rounded-xl text-sm transition shadow-lg shadow-amber-500/20">
                Modifier
            </a>
        </div>
    </div>

    <!-- Carte des informations générales -->
    <div class="bg-white dark:bg-gray-800 p-6 rounded-2xl border border-gray-200 dark:border-gray-700 shadow-sm grid grid-cols-1 sm:grid-cols-3 gap-6">
        <div>
            <span class="block text-xs font-semibold uppercase text-gray-400">Type de mouvement</span>
            <div class="mt-1">
                @if($movement->type === 'entree')
                    <span class="px-2.5 py-1 rounded-full text-xs font-semibold bg-green-500/10 text-green-500">Entrée</span>
                @elseif($movement->type === 'sortie')
                    <span class="px-2.5 py-1 rounded-full text-xs font-semibold bg-red-500/10 text-red-500">Sortie</span>
                @else
                    <span class="px-2.5 py-1 rounded-full text-xs font-semibold bg-amber-500/10 text-amber-500">Ajustement</span>
                @endif
            </div>
        </div>

        <div>
            <span class="block text-xs font-semibold uppercase text-gray-400">Date</span>
            <span class="text-sm font-medium text-gray-900 dark:text-gray-100 mt-1 block">{{ $movement->date }}</span>
        </div>

        <div>
            <span class="block text-xs font-semibold uppercase text-gray-400">Auteur</span>
            <span class="text-sm font-medium text-gray-900 dark:text-gray-100 mt-1 block">{{ $movement->user->name ?? 'N/A' }}</span>
        </div>

        <div class="sm:col-span-3">
            <span class="block text-xs font-semibold uppercase text-gray-400">Motif / Référence</span>
            <p class="text-sm text-gray-900 dark:text-gray-100 mt-1 bg-gray-50 dark:bg-gray-900 p-3 rounded-xl border border-gray-200 dark:border-gray-700">{{ $movement->motif }}</p>
        </div>
    </div>

    <!-- Liste des produits de ce mouvement -->
    <div class="bg-white dark:bg-gray-800 rounded-2xl border border-gray-200 dark:border-gray-700 shadow-sm overflow-hidden">
        <div class="p-6 border-b border-gray-200 dark:border-gray-700">
            <h3 class="text-base font-bold text-gray-900 dark:text-white">Produits impliqués</h3>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-gray-50 dark:bg-gray-900/50 border-b border-gray-200 dark:border-gray-700 text-xs font-semibold uppercase text-gray-500 dark:text-gray-400">
                        <th class="p-4">Nom du produit</th>
                        <th class="p-4">Quantité mouvementée</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200 dark:divide-gray-700 text-sm">
                    @foreach($movement->products as $product)
                        <tr class="hover:bg-gray-50 dark:hover:bg-gray-750 transition">
                            <td class="p-4 font-medium text-gray-900 dark:text-gray-100">{{ $product->name }}</td>
                            <td class="p-4">
                                <span class="font-bold {{ $movement->type === 'entree' ? 'text-green-500' : ($movement->type === 'sortie' ? 'text-red-500' : 'text-amber-500') }}">
                                    {{ $movement->type === 'entree' ? '+' : ($movement->type === 'sortie' ? '-' : '') }}{{ $product->pivot->qty }}
                                </span>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

</div>
@endsection