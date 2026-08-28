@extends('layouts.app')

@section('content')
<div class="max-w-md mx-auto px-4 py-16">

    <!-- En-tête / Alerte visuelle de sécurité -->
    <div class="text-center mb-6">
        <div class="w-14 h-14 bg-amber-50 text-amber-500 rounded-2xl flex items-center justify-center mx-auto mb-4 shadow-inner">
            <svg class="w-7 h-7" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
            </svg>
        </div>
        <h1 class="text-2xl font-black text-slate-900 tracking-tight">Sécurité : Changement requis</h1>
        <p class="text-xs text-slate-400 mt-1 max-w-xs mx-auto">Veuillez définir un nouveau mot de passe sécurisé pour continuer à accéder à votre compte.</p>
    </div>

    <!-- Carte du formulaire avec shadow-2xl et arrondis modernes -->
    <div class="bg-white border border-slate-100 rounded-3xl shadow-2xl p-8">
        <form method="POST" action="{{ route('password.update.forced') }}" class="space-y-5">
            @csrf

            {{-- Nouveau mot de passe --}}
            <div>
                <label for="password" class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-2">Nouveau mot de passe *</label>
                <input type="password" name="password" id="password" required 
                    class="w-full bg-slate-50 border border-slate-200 rounded-xl px-4 py-3 text-sm text-slate-800 placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-amber-500/20 focus:border-amber-500 transition" 
                    placeholder="••••••••">
                @error('password') <span class="text-red-500 text-xs mt-1 block font-medium">{{ $message }}</span> @enderror
            </div>

            {{-- Confirmation du mot de passe --}}
            <div>
                <label for="password_confirmation" class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-2">Confirmer le mot de passe *</label>
                <input type="password" name="password_confirmation" id="password_confirmation" required 
                    class="w-full bg-slate-50 border border-slate-200 rounded-xl px-4 py-3 text-sm text-slate-800 placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-amber-500/20 focus:border-amber-500 transition" 
                    placeholder="••••••••">
            </div>

            {{-- Bouton de validation --}}
            <div class="pt-4">
                <button type="submit" class="w-full bg-amber-500 hover:bg-amber-600 text-slate-900 font-bold px-6 py-3 rounded-xl transition shadow-md hover:shadow-lg text-sm flex items-center justify-center gap-2">
                    <span>Valider le nouveau mot de passe</span>
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M14 5l7 7m0 0l-7 7m7-7H3" />
                    </svg>
                </button>
            </div>
        </form>
    </div>
</div>
@endsection