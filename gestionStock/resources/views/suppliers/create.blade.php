@extends('layouts.app')

@section('content')
<div class="max-w-xl mx-auto px-4 py-12">

    <!-- En-tête avec retour -->
    <div class="flex items-center gap-3 mb-6">
        <a href="{{ route('suppliers.index') }}" class="w-10 h-10 bg-white border border-slate-200 rounded-xl flex items-center justify-center text-slate-400 hover:text-slate-700 hover:bg-slate-50 transition shadow-sm">
            <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
            </svg>
        </a>
        <div>
            <h1 class="text-2xl font-black text-slate-900 tracking-tight">Ajouter un fournisseur</h1>
            <p class="text-xs text-slate-400 mt-0.5">Renseignez les informations de votre nouveau partenaire</p>
        </div>
    </div>

    <!-- Carte du formulaire avec shadow-2xl et arrondis modernes -->
    <div class="bg-white border border-slate-100 rounded-3xl shadow-2xl p-8">
        <form method="POST" action="{{ route('suppliers.store') }}" class="space-y-5">
            @csrf

            {{-- Nom de l'entreprise --}}
            <div>
                <label for="name" class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-2">Nom du fournisseur *</label>
                <input type="text" name="name" id="name" value="{{ old('name') }}" required 
                    class="w-full bg-slate-50 border border-slate-200 rounded-xl px-4 py-3 text-sm text-slate-800 focus:outline-none focus:ring-2 focus:ring-amber-500/20 focus:border-amber-500 transition">
                @error('name') <span class="text-red-500 text-xs mt-1 block font-medium">{{ $message }}</span> @enderror
            </div>

            {{-- Téléphone --}}
            <div>
                <label for="phone" class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-2">Téléphone *</label>
                <input type="text" name="phone" id="phone" value="{{ old('phone') }}" required 
                    class="w-full bg-slate-50 border border-slate-200 rounded-xl px-4 py-3 text-sm text-slate-800 focus:outline-none focus:ring-2 focus:ring-amber-500/20 focus:border-amber-500 transition">
                @error('phone') <span class="text-red-500 text-xs mt-1 block font-medium">{{ $message }}</span> @enderror
            </div>

            {{-- Email --}}
            <div>
                <label for="email" class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-2">Adresse Email</label>
                <input type="email" name="email" id="email" value="{{ old('email') }}" 
                    class="w-full bg-slate-50 border border-slate-200 rounded-xl px-4 py-3 text-sm text-slate-800 focus:outline-none focus:ring-2 focus:ring-amber-500/20 focus:border-amber-500 transition">
                @error('email') <span class="text-red-500 text-xs mt-1 block font-medium">{{ $message }}</span> @enderror
            </div>

            {{-- Adresse géographique --}}
            <div>
                <label for="address" class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-2">Adresse</label>
                <textarea name="address" id="address" rows="3" 
                    class="w-full bg-slate-50 border border-slate-200 rounded-xl px-4 py-3 text-sm text-slate-800 focus:outline-none focus:ring-2 focus:ring-amber-500/20 focus:border-amber-500 transition resize-none">{{ old('address') }}</textarea>
                @error('address') <span class="text-red-500 text-xs mt-1 block font-medium">{{ $message }}</span> @enderror
            </div>

            {{-- Actions --}}
            <div class="flex items-center justify-end gap-3 pt-6 border-t border-slate-100">
                <a href="{{ route('suppliers.index') }}" class="bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold px-5 py-3 rounded-xl transition text-sm">
                    Annuler
                </a>
                <button type="submit" class="bg-amber-500 hover:bg-amber-600 text-slate-900 font-bold px-6 py-3 rounded-xl transition shadow-md hover:shadow-lg text-sm">
                    Enregistrer
                </button>
            </div>
        </form>
    </div>
</div>
@endsection