@extends('layouts.app')

@section('page-title', 'Corbeille')

@section('content')

@php
    $types = $items->pluck('label', 'type')->unique();
@endphp

<div class="space-y-6" x-data="{ activeTab: 'all' }">

    <div>
        <h1 class="text-xl font-bold text-slate-900">Corbeille</h1>
        <p class="text-sm text-slate-500 mt-0.5">{{ $items->count() }} élément(s) supprimé(s)</p>
    </div>

    @if(session('success'))
        <div class="bg-emerald-50 border border-emerald-200 text-emerald-700 text-sm px-4 py-3 rounded-xl">{{ session('success') }}</div>
    @endif
    @if(session('error'))
        <div class="bg-red-50 border border-red-200 text-red-700 text-sm px-4 py-3 rounded-xl">{{ session('error') }}</div>
    @endif

    <!-- Onglets -->
    <div class="flex flex-wrap gap-2 border-b border-slate-200 pb-px">
        <button type="button" @click="activeTab = 'all'"
            class="px-4 py-2.5 text-sm font-medium border-b-2 transition"
            :class="activeTab === 'all' ? 'border-amber-500 text-amber-600' : 'border-transparent text-slate-500 hover:text-slate-700'">
            Tout ({{ $items->count() }})
        </button>
        @foreach($types as $type => $label)
        <button type="button" @click="activeTab = '{{ $type }}'"
            class="px-4 py-2.5 text-sm font-medium border-b-2 transition"
            :class="activeTab === '{{ $type }}' ? 'border-amber-500 text-amber-600' : 'border-transparent text-slate-500 hover:text-slate-700'">
            {{ $label }} ({{ $items->where('type', $type)->count() }})
        </button>
        @endforeach
    </div>

    <!-- Liste -->
    <div class="bg-white border border-slate-100 rounded-2xl shadow-sm overflow-hidden">
        <table class="w-full text-sm">
            <thead class="bg-slate-50 border-b border-slate-100">
                <tr class="text-left text-xs font-semibold text-slate-500 uppercase tracking-wide">
                    <th class="px-6 py-3">Type</th>
                    <th class="px-6 py-3">Élément</th>
                    <th class="px-6 py-3">Supprimé le</th>
                    <th class="px-6 py-3 text-right">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100">
                @forelse($items as $item)
                <tr x-show="activeTab === 'all' || activeTab === '{{ $item['type'] }}'" class="hover:bg-slate-50 transition">
                    <td class="px-6 py-4">
                        <span class="text-xs font-semibold px-2.5 py-1 rounded-full bg-slate-100 text-slate-600">
                            {{ $item['label'] }}
                        </span>
                    </td>
                    <td class="px-6 py-4 font-medium text-slate-800">{{ $item['title'] }}</td>
                    <td class="px-6 py-4 text-slate-500">{{ $item['deleted_at']->format('d/m/Y à H:i') }}</td>
                    <td class="px-6 py-4">
                        <div class="flex items-center justify-end gap-3">
                            <form method="POST" action="{{ route('trash.restore', [$item['type'], $item['id']]) }}"
                                  onsubmit="return confirm('Restaurer cet élément ?');">
                                @csrf
                                <button type="submit" class="text-emerald-600 hover:text-emerald-700 text-sm font-medium">
                                    Restaurer
                                </button>
                            </form>

                            <form method="POST" action="{{ route('trash.forceDelete', [$item['type'], $item['id']]) }}"
                                  onsubmit="return confirm('Supprimer DÉFINITIVEMENT cet élément ? Cette action est irréversible.');">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="text-red-500 hover:text-red-600 text-sm font-medium">
                                    Supprimer définitivement
                                </button>
                            </form>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="4" class="px-6 py-12 text-center text-slate-400 text-sm">La corbeille est vide.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

</div>

@endsection