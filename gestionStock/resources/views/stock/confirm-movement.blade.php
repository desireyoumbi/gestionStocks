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

        {{-- Affichage des messages d'erreur globaux si nécessaire --}}
        @if(session('error'))
            <div class="bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-2xl text-xs font-bold text-center">
                {{ session('error') }}
            </div>
        @endif

        <form action="{{ route('stock.movement.store') }}" method="POST" class="space-y-4">
            @csrf
            
            {{-- Champs cachés indispensables pour le contrôleur --}}
            <input type="hidden" name="product_id" value="{{ $product->id }}">
            <input type="hidden" name="reference" value="{{ $product->reference }}">
            <input type="hidden" name="action_type" value="{{ $actionType }}">

            {{-- Sélection obligatoire du fournisseur si c'est un approvisionnement --}}
            @if($actionType === 'supply')
                <div>
                    <label class="block text-xs font-bold text-gray-700 mb-2">Sélectionner le fournisseur habilité</label>
                    <select name="supplier_id" required class="w-full bg-gray-50 border border-gray-200 rounded-2xl px-5 py-3 text-sm font-semibold focus:outline-none focus:ring-2 focus:ring-amber-500/20 focus:border-amber-500">
                        <option value="">-- Choisir un fournisseur --</option>
                        @foreach($product->suppliers as $supplier)
                            <option value="{{ $supplier->id }}" {{ old('supplier_id') == $supplier->id ? 'selected' : '' }}>
                                {{ $supplier->name }}
                            </option>
                        @endforeach
                    </select>
                    @if($product->suppliers->isEmpty())
                        <p class="text-xs text-red-500 mt-1">Attention : Aucun fournisseur n'est associé à ce produit dans la base.</p>
                    @endif
                </div>
            @endif

            {{-- Champ du prix de vente négociable (affiché uniquement pour une vente) --}}
            @if($actionType === 'sell')
                <div>
                    <label class="block text-xs font-bold text-gray-700 mb-2">Prix de vente unitaire (Négocié)</label>
                    {{-- CORRECTION ICI : Utilisation de old() pour conserver la valeur en cas d'erreur de validation --}}
                    <input type="number" name="selling_price" value="{{ old('selling_price', $product->selling_price) }}" min="1" step="any" required
                           class="w-full bg-gray-50 border border-gray-200 rounded-2xl px-5 py-3 font-mono font-bold text-lg focus:outline-none focus:ring-2 focus:ring-amber-500/20 focus:border-amber-500"
                           placeholder="Entrez le prix">
                    <p class="text-[10px] text-gray-400 mt-1">Prix indicatif de base : <span class="font-semibold">{{ number_format($product->selling_price, 0, ',', ' ') }} FCFA</span></p>
                </div>
            @endif

            <div>
                <label class="block text-xs font-bold text-gray-700 mb-2">Quantité à {{ $actionType === 'sell' ? 'vendre' : 'ajouter' }}</label>
                {{-- CORRECTION ICI : Utilisation de old() pour conserver la quantité saisie en cas d'erreur --}}
                <input type="number" name="quantity" value="{{ old('quantity', 1) }}" min="1" autofocus
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