@extends('layouts.app')

@section('page-title', 'Commandes fournisseurs')

@section('content')

@php
    $statusLabels = [
        'en_attente' => ['Livraison en attente', 'bg-amber-100 text-amber-700'],
        'recue' => ['Reçue', 'bg-emerald-100 text-emerald-700'],
        'annulee' => ['Annulée', 'bg-red-100 text-red-700'],
    ];
@endphp

<div class="space-y-6">

    <!-- En-tête -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h1 class="text-2xl font-bold text-slate-900">Commandes fournisseurs</h1>
            <p class="text-sm text-slate-500 mt-0.5">{{ $purchaseOrders->total() }} commande(s) enregistrée(s)</p>
        </div>

        @if(Auth::user()->role !== 'vendeur')
        <a href="{{ route('purchase_orders.create') }}"
           class="inline-flex items-center gap-2 bg-amber-500 text-slate-900 text-sm font-semibold px-4 py-2.5 rounded-xl hover:bg-amber-400 transition shadow-md hover:shadow-lg shrink-0">
            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4" />
            </svg>
            Nouvelle commande
        </a>
        @endif
    </div>

    <!-- Messages de notification -->
    @if(session('success'))
        <div class="bg-emerald-50 border border-emerald-200 text-emerald-700 text-sm px-4 py-3 rounded-2xl shadow-md">{{ session('success') }}</div>
    @endif
    @if(session('error'))
        <div class="bg-red-50 border border-red-200 text-red-700 text-sm px-4 py-3 rounded-2xl shadow-md">{{ session('error') }}</div>
    @endif

    <!-- Filtres par statut -->
    <div class="flex flex-wrap gap-2 bg-white p-3 rounded-2xl shadow-md border border-slate-100">
        <a href="{{ route('purchase_orders.index') }}"
           class="px-4 py-2 rounded-xl text-sm font-medium transition {{ !$status ? 'bg-slate-900 text-white shadow-sm' : 'bg-slate-50 text-slate-600 border border-slate-200 hover:bg-slate-100' }}">
            Toutes
        </a>
        @foreach($statusLabels as $key => $info)
        <a href="{{ route('purchase_orders.index', ['status' => $key]) }}"
           class="px-4 py-2 rounded-xl text-sm font-medium transition {{ $status === $key ? 'bg-slate-900 text-white shadow-sm' : 'bg-slate-50 text-slate-600 border border-slate-200 hover:bg-slate-100' }}">
            {{ $info[0] }}
        </a>
        @endforeach
    </div>

    <!-- Tableau principal -->
    <div class="bg-white border border-slate-100 rounded-3xl shadow-lg overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="bg-slate-50 border-b border-slate-100">
                    <tr class="text-left text-xs font-semibold text-slate-500 uppercase tracking-wide">
                        <th class="px-6 py-3.5">Fournisseur</th>
                        <th class="px-6 py-3.5">Statut</th>
                        <th class="px-6 py-3.5">Créée par</th>
                        <th class="px-6 py-3.5">Date</th>
                        <th class="px-6 py-3.5 text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($purchaseOrders as $order)
                    <tr class="hover:bg-slate-50/70 transition">
                        <td class="px-6 py-4 font-medium text-slate-800">{{ $order->supplier->name ?? '—' }}</td>
                        <td class="px-6 py-4">
                            <span class="text-xs font-semibold px-3 py-1 rounded-full {{ $statusLabels[$order->status][1] }}">
                                {{ $statusLabels[$order->status][0] }}
                            </span>
                        </td>
                        <td class="px-6 py-4 text-slate-500">{{ $order->user->name ?? '—' }}</td>
                        <td class="px-6 py-4 text-slate-500">{{ $order->created_at->format('d/m/Y') }}</td>
                        <td class="px-6 py-4 text-right">
                            <div class="inline-flex items-center justify-end gap-2">
                                <!-- Bouton Valider (Apparaît uniquement si en attente et rôle autorisé) -->
                                @if($order->status === 'en_attente' && Auth::user()->role !== 'vendeur')
                                    <form action="{{ route('purchase_orders.receive', $order) }}" method="POST" onsubmit="return confirm('Voulez-vous vraiment valider cette commande ? Cela va générer le mouvement d\'entrée et mettre à jour le stock.')">
                                        @csrf
                                        @method('PATCH')
                                        <button type="submit" class="inline-flex items-center gap-1.5 bg-emerald-50 hover:bg-emerald-100 text-emerald-700 font-semibold px-3 py-1.5 rounded-xl transition text-xs">
                                            <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" />
                                            </svg>
                                            Valider
                                        </button>
                                    </form>
                                @endif

                                <!-- Bouton Voir détail -->
                                <a href="{{ route('purchase_orders.show', $order) }}" class="inline-flex items-center gap-1.5 bg-amber-50 hover:bg-amber-100 text-amber-700 font-semibold px-3 py-1.5 rounded-xl transition text-xs">
                                    <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                    </svg>
                                    Voir détail
                                </a>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="px-6 py-16 text-center">
                            <div class="flex flex-col items-center justify-center space-y-3">
                                <div class="w-12 h-12 rounded-2xl bg-amber-50 text-amber-500 flex items-center justify-center shadow-inner">
                                    <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4" />
                                    </svg>
                                </div>
                                <p class="text-slate-500 font-medium text-sm">Aucune commande pour l'instant.</p>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- Pagination -->
    @if($purchaseOrders->hasPages())
        <div class="bg-white p-4 rounded-2xl shadow-md border border-slate-100">
            {{ $purchaseOrders->links() }}
        </div>
    @endif

</div>

@endsection