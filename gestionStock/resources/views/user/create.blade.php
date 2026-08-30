@extends('layouts.app')

@section('content')
<div class="max-w-2xl mx-auto px-4 sm:px-6 lg:px-8 py-10">

    <!-- En-tête avec retour -->
    <div class="flex items-center gap-3 mb-6">
        <a href="{{ route('user.index') }}" class="w-10 h-10 bg-white border border-slate-200 rounded-xl flex items-center justify-center text-slate-400 hover:text-slate-700 hover:bg-slate-50 transition shadow-sm">
            <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
            </svg>
        </a>
        <div>
            <h1 class="text-2xl font-black text-slate-900 tracking-tight">Créer un nouvel utilisateur</h1>
            <p class="text-xs text-slate-400 mt-0.5">Ajoutez un collaborateur et définissez ses accès à l'application</p>
        </div>
    </div>

    <!-- Carte du formulaire avec shadow-2xl et arrondis modernes -->
    <div class="bg-white border border-slate-100 rounded-3xl shadow-2xl p-8">
        <form method="POST" action="{{ route('user.store') }}" class="space-y-5">
            @csrf

            {{-- Nom --}}
            <div>
                <label for="name" class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-2">Nom complet *</label>
                <input type="text" name="name" id="name" value="{{ old('name') }}" required 
                    class="w-full bg-slate-50 border border-slate-200 rounded-xl px-4 py-3 text-sm text-slate-800 placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-amber-500/20 focus:border-amber-500 transition" 
                    placeholder="Ex: Jean Dupont">
                @error('name') <span class="text-red-500 text-xs mt-1 block font-medium">{{ $message }}</span> @enderror
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                {{-- Téléphone --}}
                <div>
                    <label for="phone" class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-2">Téléphone * <span class="normal-case font-normal text-slate-400">(max 15 car.)</span></label>
                    <input type="text" name="phone" id="phone" value="{{ old('phone') }}" required maxlength="15" 
                        class="w-full bg-slate-50 border border-slate-200 rounded-xl px-4 py-3 text-sm text-slate-800 placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-amber-500/20 focus:border-amber-500 transition" 
                        placeholder="Ex: 699000000">
                    @error('phone') <span class="text-red-500 text-xs mt-1 block font-medium">{{ $message }}</span> @enderror
                </div>

                {{-- Email (Facultatif) --}}
                <div>
                    <label for="email" class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-2">Email <span class="normal-case font-normal text-slate-400">(Facultatif)</span></label>
                    <input type="email" name="email" id="email" value="{{ old('email') }}" 
                        class="w-full bg-slate-50 border border-slate-200 rounded-xl px-4 py-3 text-sm text-slate-800 placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-amber-500/20 focus:border-amber-500 transition" 
                        placeholder="exemple@domaine.com">
                    @error('email') <span class="text-red-500 text-xs mt-1 block font-medium">{{ $message }}</span> @enderror
                </div>
            </div>

            {{-- Rôle --}}
            <div>
                <label for="role" class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-2">Rôle *</label>
                <div class="relative">
                    <select name="role" id="role" required 
                        class="w-full bg-slate-50 border border-slate-200 rounded-xl px-4 py-3 text-sm text-slate-800 focus:outline-none focus:ring-2 focus:ring-amber-500/20 focus:border-amber-500 transition appearance-none cursor-pointer">
                        <option value="">Sélectionner un rôle</option>
                        <option value="vice_administrateur" {{ old('role') == 'vice_administrateur' ? 'selected' : '' }}>Vice-Administrateur</option>
                        <option value="vendeur" {{ old('role') == 'vendeur' ? 'selected' : '' }}>Vendeur</option>
                    </select>
                    <div class="absolute inset-y-0 right-0 flex items-center px-4 pointer-events-none text-slate-400">
                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7" />
                        </svg>
                    </div>
                </div>
                @error('role') <span class="text-red-500 text-xs mt-1 block font-medium">{{ $message }}</span> @enderror
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 pt-2 border-t border-slate-100">
                {{-- Mot de passe --}}
                <div>
                    <label for="password" class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-2">Mot de passe temporaire *</label>
                    <input type="password" name="password" id="password" required 
                        class="w-full bg-slate-50 border border-slate-200 rounded-xl px-4 py-3 text-sm text-slate-800 focus:outline-none focus:ring-2 focus:ring-amber-500/20 focus:border-amber-500 transition" 
                        placeholder="••••••••">
                    @error('password') <span class="text-red-500 text-xs mt-1 block font-medium">{{ $message }}</span> @enderror
                </div>

                {{-- Confirmation du mot de passe --}}
                <div>
                    <label for="password_confirmation" class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-2">Confirmer le mot de passe *</label>
                    <input type="password" name="password_confirmation" id="password_confirmation" required 
                        class="w-full bg-slate-50 border border-slate-200 rounded-xl px-4 py-3 text-sm text-slate-800 focus:outline-none focus:ring-2 focus:ring-amber-500/20 focus:border-amber-500 transition" 
                        placeholder="••••••••">
                </div>
            </div>

            {{-- Actions --}}
            <div class="flex items-center justify-end gap-3 pt-6 border-t border-slate-100">
                <a href="{{ route('user.index') }}" class="bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold px-5 py-3 rounded-xl transition text-sm">
                    Annuler
                </a>
                <button type="submit" class="bg-amber-500 hover:bg-amber-600 text-slate-900 font-bold px-6 py-3 rounded-xl transition shadow-md hover:shadow-lg text-sm">
                    Enregistrer l'utilisateur
                </button>
            </div>
        </form>
    </div>
</div>
@endsection