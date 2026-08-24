@extends('layouts.app')
@section('header_title', 'Confirmation du mouvement')
@section('header_subtitle', $actionType === 'sell' ? 'Enregistrement d\'une vente' : 'Enregistrement d\'un approvisionnement')

@section('content')
<div class="max-w-xl mx-auto space-y-6">
    <div class="bg-white p-8 rounded-3xl shadow-md border border-gray-100 space-y-6">
        
        <div class="text-center space-y-2">
            <span class="px-3 py-1 bg-amber-100 text-amber-800 rounded-full text-xs font-bold uppercase tracking-wider">
                {{ $actionType === 'sell' ? 'Vente' : 'Approvisionnement' }}
            </span>
            <h2 class="text-2xl font-bold text-gray-900">{{ $product->name }}</h2>
            <p class="text-xs text-gray-400">Réf : <span class="font-mono font-bold">{{ $product->reference }}</span> | Stock actuel : <span class="font-bold text-gray-700">{{ $product->quantity }}</span></p>
        </div>

        <form action="{{ route('stock.movement.store') }}" method="POST" class="space-y-4">
            @csrf
            
            {{-- Champs cachés indispensables pour le contrôleur --}}
            <input type="hidden" name="product_id" value="{{ $product->id }}">
            <input type="hidden" name="reference" value="{{ $product->reference }}">
            <input type="hidden" name="action_type" value="{{ $actionType }}">

            <div>
                <label class="block text-xs font-bold text-gray-700 mb-2">Quantité à {{ $actionType === 'sell' ? 'vendre' : 'ajouter' }}</label>
                <input type="number" name="quantity" value="1" min="1" autofocus
                       class="w-full bg-gray-50 border border-gray-200 rounded-2xl px-5 py-4 text-center font-mono font-bold text-xl focus:outline-none focus:ring-2 focus:ring-amber-500/20 focus:border-amber-500">
            </div>

            <div class="flex gap-3 pt-2">
                <a href="{{ route('stock.scan.index') }}" class="w-1/2 bg-gray-100 hover:bg-gray-200 text-gray-700 font-bold py-3.5 rounded-2xl transition text-center text-xs flex items-center justify-center">
                    Annuler
                </a>
                <button type="submit" class="w-1/2 bg-amber-500 hover:bg-amber-400 text-gray-900 font-bold py-3.5 rounded-2xl transition shadow-md text-xs">
                    Confirmer
                </button>
            </div>
        </form>

    </div>
</div>
@endsection