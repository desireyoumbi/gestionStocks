@extends('layouts.app')

@section('content')
<div class="space-y-6">

    {{-- EN-TÊTE DE PAGE (Titre, sous-titre et bouton d'ajout à droite) --}}
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
        <div>
            <h1 class="text-2xl font-black text-slate-900 tracking-tight">Fournisseurs</h1>
            <p class="text-xs text-slate-500 mt-0.5">Gérez et suivez l'ensemble de vos fournisseurs et partenaires.</p>
        </div>
        
        <a href="{{ route('suppliers.create') }}" class="bg-amber-500 hover:bg-amber-600 text-slate-900 text-xs font-bold px-4 py-2.5 rounded-xl shadow-md hover:shadow-lg transition flex items-center gap-1.5 shrink-0">
            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4" />
            </svg>
            Nouveau fournisseur
        </a>
    </div>

    {{-- Message flash de succès --}}
    @if(session('succes'))
        <div class="bg-emerald-50 border border-emerald-200 text-emerald-800 px-4 py-3 rounded-2xl text-xs font-medium flex items-center shadow-md" role="alert">
            <span class="mr-2">✅</span> {{ session('succes') }}
        </div>
    @endif

    {{-- CARTE DE FILTRE / RECHERCHE (Avec shadow-xl plus prononcé) --}}
    <div class="bg-white p-6 rounded-3xl shadow-xl border border-slate-100">
        <form method="GET" action="{{ route('suppliers.index') }}" class="grid grid-cols-1 md:grid-cols-3 gap-4 items-end">
            
            <!-- Champ Recherche -->
            <div class="md:col-span-2">
                <label class="block text-[11px] font-bold text-slate-400 uppercase tracking-wider mb-2">Recherche</label>
                <div class="relative">
                    <svg class="w-4 h-4 absolute left-3.5 top-1/2 -translate-y-1/2 text-slate-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                    </svg>
                    <input type="text" name="search" value="{{ request('search') }}" placeholder="Nom, téléphone, email..." 
                        class="w-full bg-slate-50 border border-slate-200 rounded-xl pl-10 pr-4 py-2.5 text-xs text-slate-800 placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-amber-500/20 focus:border-amber-500 transition">
                </div>
            </div>

            <!-- Boutons Filtrer / Réinitialiser -->
            <div class="flex items-center gap-2 justify-end">
                @if(request('search'))
                    <a href="{{ route('suppliers.index') }}" class="bg-slate-100 hover:bg-slate-200 text-slate-600 text-xs font-bold px-4 py-2.5 rounded-xl transition flex items-center justify-center">
                        Réinitialiser
                    </a>
                @endif
                <button type="submit" class="bg-slate-900 hover:bg-slate-800 text-white text-xs font-bold px-6 py-2.5 rounded-xl shadow-md transition flex items-center justify-center">
                    Filtrer
                </button>
            </div>

        </form>
    </div>

    {{-- TABLEAU MODERNE (Avec shadow-xl prononcé et coins arrondis modernisés) --}}
    <div class="bg-white rounded-3xl shadow-xl border border-slate-100 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-slate-50/70 border-b border-slate-100 text-[11px] font-bold text-slate-400 uppercase tracking-wider">
                        <th class="px-6 py-4">Nom</th>
                        <th class="px-6 py-4">Téléphone</th>
                        <th class="px-6 py-4">Email</th>
                        <th class="px-6 py-4 text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 text-xs">
                    @forelse($suppliers as $supplier)
                        <tr class="hover:bg-slate-50/70 transition">
                            <td class="px-6 py-4 font-bold text-slate-900">{{ $supplier->name }}</td>
                            <td class="px-6 py-4 text-slate-500 font-medium">{{ $supplier->phone }}</td>
                            <td class="px-6 py-4 text-slate-500">{{ $supplier->email ?? '—' }}</td>
                            <td class="px-6 py-4 text-right">
                                <div class="flex items-center justify-end gap-3">
                                    <a href="{{ route('suppliers.edit', $supplier->id) }}" class="text-amber-600 hover:text-amber-700 font-bold transition bg-amber-50 px-2.5 py-1.5 rounded-lg">Modifier</a>
                                    <form action="{{ route('suppliers.destroy', $supplier->id) }}" method="POST" class="inline-block" onsubmit="return confirm('Voulez-vous vraiment supprimer ce fournisseur ?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="text-red-500 hover:text-red-700 font-bold transition bg-red-50 px-2.5 py-1.5 rounded-lg">Supprimer</button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="px-6 py-16 text-center">
                                <div class="flex flex-col items-center justify-center space-y-3">
                                    <div class="w-12 h-12 rounded-2xl bg-amber-50 text-amber-500 flex items-center justify-center shadow-inner">
                                        <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                                        </svg>
                                    </div>
                                    <p class="text-slate-500 font-medium text-xs">Aucun fournisseur trouvé.</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    {{-- Pagination propre --}}
    @if($suppliers->hasPages())
        <div class="bg-white p-4 rounded-2xl shadow-md border border-slate-100">
            {{ $suppliers->appends(['search' => $search])->links() }}
        </div>
    @endif

</div>
@endsection