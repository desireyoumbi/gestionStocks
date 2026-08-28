@extends('layouts.app')

@section('page-title', 'Détail commande')

@section('content')

@php
    $statusLabels = [
        'en_attente' => ['Livraison en attente', 'bg-amber-100 text-amber-700'],
        'recue' => ['Reçue', 'bg-emerald-100 text-emerald-700'],
        'annulee' => ['Annulée', 'bg-red-100 text-red-700'],
    ];
    $total = $purchaseOrder->products->sum(fn($p) => $p->pivot->qty * $p->pivot->unit_price);
@endphp

<div class="max-w-3xl">

    <div class="flex items-center gap-3 mb-6">
        <a href="{{ route('purchase_orders.index') }}" class="text-slate-400 hover:text-slate-700">
            <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
            </svg>
        </a>
        <h1 class="text-xl font-bold text-slate-900">Commande #{{ $purchaseOrder->id }}</h1>
        <span class="text-xs font-semibold px-2.5 py-1 rounded-full {{ $statusLabels[$purchaseOrder->status][1] }}">
            {{ $statusLabels[$purchaseOrder->status][0] }}
        </span>
    </div>

    @if(session('success'))
        <div class="bg-emerald-50 border border-emerald-200 text-emerald-700 text-sm px-4 py-3 rounded-xl mb-6">{{ session('success') }}</div>
    @endif
    @if(session('error'))
        <div class="bg-red-50 border border-red-200 text-red-700 text-sm px-4 py-3 rounded-xl mb-6">{{ session('error') }}</div>
    @endif

    <div class="bg-white border border-slate-100 rounded-2xl shadow-sm p-6 space-y-6">

        <div class="grid grid-cols-2 gap-4">
            <div>
                <p class="text-xs font-semibold text-slate-400 uppercase tracking-wide mb-1">Fournisseur</p>
                <p class="text-sm text-slate-700">{{ $purchaseOrder->supplier->name ?? '—' }}</p>
            </div>
            <div>
                <p class="text-xs font-semibold text-slate-400 uppercase tracking-wide mb-1">Créée par</p>
                <p class="text-sm text-slate-700">{{ $purchaseOrder->user->name ?? '—' }}</p>
            </div>
            <div>
                <p class="text-xs font-semibold text-slate-400 uppercase tracking-wide mb-1">Date de création</p>
                <p class="text-sm text-slate-700">{{ $purchaseOrder->created_at->format('d/m/Y à H:i') }}</p>
            </div>
            @if($purchaseOrder->updatedBy)
            <div>
                <p class="text-xs font-semibold text-slate-400 uppercase tracking-wide mb-1">Dernière modification</p>
                <p class="text-sm text-slate-700">{{ $purchaseOrder->updatedBy->name }} — {{ $purchaseOrder->updated_at->format('d/m/Y') }}</p>
            </div>
            @endif
        </div>

        <!-- Lignes de produits -->
        <div class="pt-4 border-t border-slate-100">
            <p class="text-xs font-semibold text-slate-400 uppercase tracking-wide mb-3">Produits commandés</p>
            <div class="border border-slate-100 rounded-xl overflow-hidden">
                <table class="w-full text-sm">
                    <thead class="bg-slate-50">
                        <tr class="text-left text-xs font-semibold text-slate-500 uppercase">
                            <th class="px-4 py-2.5">Produit</th>
                            <th class="px-4 py-2.5 text-right">Quantité</th>
                            <th class="px-4 py-2.5 text-right">Prix unitaire</th>
                            <th class="px-4 py-2.5 text-right">Sous-total</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @foreach($purchaseOrder->products as $product)
                        <tr>
                            <td class="px-4 py-2.5 text-slate-700">{{ $product->name }}</td>
                            <td class="px-4 py-2.5 text-right text-slate-600">{{ $product->pivot->qty }}</td>
                            <td class="px-4 py-2.5 text-right text-slate-600">{{ number_format($product->pivot->unit_price, 0, ',', ' ') }} FCFA</td>
                            <td class="px-4 py-2.5 text-right font-medium text-slate-800">
                                {{ number_format($product->pivot->qty * $product->pivot->unit_price, 0, ',', ' ') }} FCFA
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                    <tfoot class="bg-slate-50">
                        <tr>
                            <td colspan="3" class="px-4 py-3 text-right text-sm font-semibold text-slate-700">Total</td>
                            <td class="px-4 py-3 text-right text-sm font-bold text-slate-900">{{ number_format($total, 0, ',', ' ') }} FCFA</td>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>

        <!-- Actions -->
        @if(Auth::user()->role !== 'vendeur' && $purchaseOrder->status === 'en_attente')
        <div class="flex items-center flex-wrap gap-3 pt-4 border-t border-slate-100">
            
            <!-- Formulaire corrigé avec @method('PATCH') -->
            <form method="POST" action="{{ route('purchase_orders.receive', $purchaseOrder) }}"
                  onsubmit="return confirm('Confirmer la réception ? Le stock sera mis à jour et un mouvement d\'entrée sera créé.');">
                @csrf
                @method('PATCH')
                <button type="submit" class="bg-emerald-500 text-white text-sm font-semibold px-5 py-2.5 rounded-xl hover:bg-emerald-600 transition">
                    Valider la réception
                </button>
            </form>

            <a href="{{ route('purchase_orders.edit', $purchaseOrder) }}"
                class="bg-white border border-slate-200 text-slate-700 text-sm font-semibold px-5 py-2.5 rounded-xl hover:bg-slate-50 transition">
                Modifier
            </a>

            <form method="POST" action="{{ route('purchase_orders.cancel', $purchaseOrder) }}"
                  onsubmit="return confirm('Annuler cette commande ?');">
                @csrf
                @method('PATCH')
                <button type="submit" class="bg-red-50 text-red-600 text-sm font-semibold px-5 py-2.5 rounded-xl hover:bg-red-100 transition">
                    Annuler la commande
                </button>
            </form>

            @if(Auth::user()->role === 'administrateur')
            <form method="POST" action="{{ route('purchase_orders.destroy', $purchaseOrder) }}"
                  onsubmit="return confirm('Supprimer définitivement cette commande ?');" class="ml-auto">
                @csrf
                @method('DELETE')
                <button type="submit" class="text-red-400 hover:text-red-600 text-sm font-medium">
                    Supprimer
                </button>
            </form>
            @endif
        </div>
        @endif

    </div>

</div>

@endsection