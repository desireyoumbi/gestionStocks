@extends('layouts.app')

@section('page-title', 'Détail catégorie')

@section('content')

<div class="max-w-2xl">

    <div class="flex items-center gap-3 mb-6">
        <a href="{{ route('categories.index') }}" class="text-slate-400 hover:text-slate-700">
            <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
            </svg>
        </a>
        <h1 class="text-xl font-bold text-slate-900">{{ $category->name }}</h1>
    </div>

    <div class="bg-white border border-slate-100 rounded-2xl shadow-sm p-6 space-y-5">

        <div>
            <p class="text-xs font-semibold text-slate-400 uppercase tracking-wide mb-1">Description</p>
            <p class="text-sm text-slate-700">{{ $category->description ?? 'Aucune description renseignée.' }}</p>
        </div>

        <div class="grid grid-cols-2 gap-4 pt-4 border-t border-slate-100">
            <div>
                <p class="text-xs font-semibold text-slate-400 uppercase tracking-wide mb-1">Créée par</p>
                <p class="text-sm text-slate-700">{{ $category->user->name ?? '—' }}</p>
            </div>
            <div>
                <p class="text-xs font-semibold text-slate-400 uppercase tracking-wide mb-1">Date de création</p>
                <p class="text-sm text-slate-700">{{ $category->created_at->format('d/m/Y à H:i') }}</p>
            </div>

            @if($category->updatedBy)
            <div>
                <p class="text-xs font-semibold text-slate-400 uppercase tracking-wide mb-1">Dernière modification par</p>
                <p class="text-sm text-slate-700">{{ $category->updatedBy->name }}</p>
            </div>
            <div>
                <p class="text-xs font-semibold text-slate-400 uppercase tracking-wide mb-1">Modifiée le</p>
                <p class="text-sm text-slate-700">{{ $category->updated_at->format('d/m/Y à H:i') }}</p>
            </div>
            @endif
        </div>

        <div class="flex items-center gap-3 pt-4 border-t border-slate-100">
            @if(Auth::user()->role !== 'vendeur')
            <a href="{{ route('categories.edit', $category) }}"
                class="bg-amber-500 text-slate-900 text-sm font-semibold px-5 py-2.5 rounded-xl hover:bg-amber-400 transition">
                Modifier
            </a>
            @endif

            @if(Auth::user()->role === 'administrateur')
            <form method="POST" action="{{ route('categories.destroy', $category) }}"
                  onsubmit="return confirm('Supprimer cette catégorie ?');">
                @csrf
                @method('DELETE')
                <button type="submit" class="bg-red-50 text-red-600 text-sm font-semibold px-5 py-2.5 rounded-xl hover:bg-red-100 transition">
                    Supprimer
                </button>
            </form>
            @endif
        </div>

    </div>

</div>

@endsection