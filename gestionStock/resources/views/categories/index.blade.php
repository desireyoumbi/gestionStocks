@extends('layouts.app')

@section('page-title', 'Catégories')

@section('content')

<div class="space-y-6">

    <!-- En-tête -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h1 class="text-xl font-bold text-slate-900">Catégories</h1>
            <p class="text-sm text-slate-500 mt-0.5">{{ $categories->total() }} catégorie(s) enregistrée(s)</p>
        </div>

        @if(Auth::check() && (method_exists(Auth::user(), 'isManager') ? Auth::user()->isManager() : Auth::user()->role !== 'vendeur'))
        <a href="{{ route('categories.create') }}"
           class="inline-flex items-center gap-2 bg-amber-500 text-slate-900 text-sm font-semibold px-4 py-2.5 rounded-xl hover:bg-amber-400 transition shadow-md hover:shadow-lg shrink-0">
            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4" />
            </svg>
            Nouvelle catégorie
        </a>
        @endif
    </div>

    <!-- Message de succès -->
    @if(session('success') || session('succes'))
        <div class="bg-emerald-50 border border-emerald-200 text-emerald-700 text-sm px-4 py-3 rounded-2xl shadow-md">
            {{ session('success') ?? session('succes') }}
        </div>
    @endif

    <!-- Barre de recherche -->
    <form method="GET" action="{{ route('categories.index') }}" class="flex gap-3 bg-white p-4 rounded-2xl shadow-md border border-slate-100">
        <div class="relative flex-1">
            <svg class="w-4 h-4 absolute left-3.5 top-1/2 -translate-y-1/2 text-slate-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
            </svg>
            <input type="text" name="search" value="{{ $search }}" placeholder="Rechercher une catégorie..."
                class="w-full pl-10 pr-3 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-sm placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-amber-500/20 focus:border-amber-500 transition">
        </div>
        <button type="submit" class="bg-slate-900 text-white text-sm font-medium px-5 py-2.5 rounded-xl hover:bg-slate-800 transition shadow-md">
            Rechercher
        </button>
        @if($search)
        <a href="{{ route('categories.index') }}" class="text-sm text-slate-500 hover:text-slate-700 px-2 flex items-center">
            Réinitialiser
        </a>
        @endif
    </form>

    <!-- Tableau -->
    <div class="bg-white border border-slate-100 rounded-3xl shadow-lg overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="bg-slate-50 border-b border-slate-100">
                    <tr class="text-left text-xs font-semibold text-slate-500 uppercase tracking-wide">
                        <th class="px-6 py-3.5">Nom</th>
                        <th class="px-6 py-3.5">Description</th>
                        <th class="px-6 py-3.5">Créée par</th>
                        <th class="px-6 py-3.5">Date</th>
                        <th class="px-6 py-3.5 text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($categories as $category)
                    <tr class="hover:bg-slate-50/70 transition">
                        <td class="px-6 py-4 font-medium text-slate-800">{{ $category->name }}</td>
                        <td class="px-6 py-4 text-slate-500 max-w-xs truncate">{{ $category->description ?? '—' }}</td>
                        <td class="px-6 py-4 text-slate-500">{{ $category->user->name ?? '—' }}</td>
                        <td class="px-6 py-4 text-slate-500">{{ $category->created_at->format('d/m/Y') }}</td>
                        <td class="px-6 py-4 text-right">
                            <div class="flex items-center justify-end gap-3">
                                <!-- Bouton Modifier -->
                                <a href="{{ route('categories.edit', $category->id) }}" class="text-amber-500 hover:text-amber-600 font-bold transition flex items-center gap-1 text-xs bg-amber-50 px-2.5 py-1.5 rounded-lg" title="Modifier">
                                    <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                    </svg>
                                    Modifier
                                </a>

                                <!-- Bouton Supprimer -->
                                <form action="{{ route('categories.destroy', $category->id) }}" method="POST" class="inline-block" onsubmit="return confirm('Supprimer cette catégorie ?');">
                                    @csrf 
                                    @method('DELETE')
                                    <button type="submit" class="text-red-500 hover:text-red-700 font-bold transition flex items-center gap-1 text-xs bg-red-50 px-2.5 py-1.5 rounded-lg" title="Supprimer">
                                        <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                        </svg>
                                        Supprimer
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="px-6 py-12 text-center">
                            <div class="flex flex-col items-center justify-center space-y-3">
                                <div class="w-12 h-12 rounded-2xl bg-amber-50 text-amber-500 flex items-center justify-center shadow-inner">
                                    <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M4 6h16M4 10h16M4 14h16M4 18h16" />
                                    </svg>
                                </div>
                                <p class="text-slate-500 font-medium text-sm">
                                    @if($search)
                                        Aucune catégorie ne correspond à "{{ $search }}".
                                    @else
                                        Aucune catégorie enregistrée pour l'instant.
                                    @endif
                                </p>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- Pagination -->
    @if($categories->hasPages())
        <div class="bg-white p-4 rounded-2xl shadow-md border border-slate-100">
            {{ $categories->links() }}
        </div>
    @endif

</div>

@endsection