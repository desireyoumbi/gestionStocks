@extends('layouts.app')

@section('page-title', 'Nouvelle catégorie')

@section('content')

<div class="max-w-2xl mx-auto px-4 py-8">

    <!-- En-tête avec retour -->
    <div class="flex items-center gap-3 mb-6">
        <a href="{{ route('categories.index') }}" class="w-10 h-10 bg-white border border-slate-200 rounded-xl flex items-center justify-center text-slate-400 hover:text-slate-700 hover:bg-slate-50 transition shadow-sm">
            <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
            </svg>
        </a>
        <div>
            <h1 class="text-2xl font-bold text-slate-900">Nouvelle catégorie</h1>
            <p class="text-xs text-slate-400 mt-0.5">Ajoutez une nouvelle catégorie pour organiser vos produits.</p>
        </div>
    </div>

    <!-- Gestion des erreurs -->
    @if ($errors->any())
        <div class="bg-red-50 border border-red-200 text-red-700 text-sm px-4 py-3 rounded-2xl mb-6 shadow-md">
            <ul class="list-disc list-inside space-y-0.5">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <!-- Formulaire (Centré avec ombre forte shadow-xl) -->
    <div class="bg-white border border-slate-100 rounded-3xl shadow-xl p-8">
        <form method="POST" action="{{ route('categories.store') }}" class="space-y-6">
            @csrf

            <!-- Nom -->
            <div>
                <label for="name" class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-2">Nom de la catégorie *</label>
                <input type="text" name="name" id="name" value="{{ old('name') }}" required
                    placeholder="Ex : Électronique"
                    class="w-full bg-slate-50 border border-slate-200 rounded-xl px-4 py-3 text-sm placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-amber-500/20 focus:border-amber-500 transition">
            </div>

            <!-- Description -->
            <div>
                <label for="description" class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-2">
                    Description <span class="text-slate-400 font-normal lowercase">(facultatif)</span>
                </label>
                <textarea name="description" id="description" rows="4"
                    placeholder="Une brève description de cette catégorie..."
                    class="w-full bg-slate-50 border border-slate-200 rounded-xl px-4 py-3 text-sm placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-amber-500/20 focus:border-amber-500 transition">{{ old('description') }}</textarea>
            </div>

            <!-- Boutons d'action -->
            <div class="flex items-center justify-end gap-3 pt-6 border-t border-slate-100">
                <a href="{{ route('categories.index') }}" class="bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold px-5 py-3 rounded-xl transition text-sm">
                    Annuler
                </a>
                <button type="submit"
                    class="bg-amber-500 hover:bg-amber-600 text-slate-900 font-bold px-6 py-3 rounded-xl transition shadow-md hover:shadow-lg text-sm">
                    Créer la catégorie
                </button>
            </div>
        </form>
    </div>

</div>

@endsection