@extends('layouts.app')

@section('header_subtitle', 'Gestion des stocks')
@section('header_title', 'Historique des ajustements')

@section('content')
<div class="max-w-7xl mx-auto space-y-6">
    
    <!-- En-tête avec bouton d'action -->
    <div class="flex items-center justify-between">
        <div>
            <h3 class="text-xs font-bold uppercase tracking-wider text-gray-400">Journal des corrections</h3>
        </div>
        <a href="{{ route('adjustments.create') }}" class="px-4 py-2 bg-amber-500 hover:bg-amber-600 text-gray-900 font-bold text-xs rounded-xl transition shadow">
            + Nouvel ajustement
        </a>
    </div>

    <!-- Tableau des ajustements -->
    <div class="bg-white dark:bg-gray-950 border border-gray-100 dark:border-gray-800 rounded-2xl shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="border-b border-gray-100 dark:border-gray-900 text-[10px] font-bold text-gray-400 uppercase tracking-wider">
                        <th class="p-4">Date</th>
                        <th class="p-4">Produit concerné</th>
                        <th class="p-4">Motif</th>
                        <th class="p-4 text-right">Quantité ajustée</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 dark:divide-gray-900 text-xs text-gray-800 dark:text-gray-200">
                    @forelse($adjustments as $movement)
                        @php 
                            $pivotQty = $movement->products->first()->pivot->qty ?? 0; 
                        @endphp
                        <tr class="hover:bg-gray-50/50 dark:hover:bg-gray-900/50 transition">
                            <td class="p-4 text-gray-500">{{ $movement->created_at->format('d/m/Y H:i') }}</td>
                            <td class="p-4 font-semibold">{{ $movement->products->first()->name ?? 'Produit supprimé' }}</td>
                            <td class="p-4 text-gray-600 dark:text-gray-400">{{ $movement->motif }}</td>
                            <td class="p-4 text-right font-bold {{ $pivotQty > 0 ? 'text-emerald-500' : 'text-rose-500' }}">
                                {{ $pivotQty > 0 ? '+' : '' }}{{ $pivotQty }}
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="p-8 text-center text-gray-400">
                                Aucun ajustement de stock enregistré pour le moment.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        @if($adjustments->hasPages())
            <div class="p-4 border-t border-gray-100 dark:border-gray-900">
                {{ $adjustments->links() }}
            </div>
        @endif
    </div>

</div>
@endsection