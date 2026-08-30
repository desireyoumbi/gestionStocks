@extends('layouts.app')

@section('content')
<div class="space-y-8 max-w-4xl mx-auto">

    {{-- EN-TÊTE DE PAGE AVEC STATS --}}
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
        <div>
            <h1 class="text-3xl font-black text-slate-900 dark:text-white tracking-tight">Mon Profil</h1>
            <p class="text-sm text-slate-500 dark:text-slate-400 mt-1">Membre depuis {{ $ancienneté }} • Rôle : <span class="uppercase font-bold text-amber-500">{{ $user->role }}</span></p>
        </div>

        <div class="bg-white dark:bg-gray-950 px-5 py-3 rounded-2xl shadow-md border border-slate-100 dark:border-gray-800 flex items-center gap-3">
            <div class="w-10 h-10 rounded-xl bg-amber-500/10 text-amber-500 flex items-center justify-center font-bold">
                📊
            </div>
            <div>
                <p class="text-[11px] font-bold text-slate-400 uppercase tracking-wider">Mouvements gérés</p>
                <p class="text-lg font-black text-slate-900 dark:text-white">{{ $stats['total_movements'] ?? 0 }}</p>
            </div>
        </div>
    </div>

    {{-- MESSAGE FLASH --}}
    @if(session('success'))
        <div class="bg-emerald-50 border border-emerald-200 text-emerald-800 px-5 py-4 rounded-2xl text-sm font-medium flex items-center shadow-md" role="alert">
            <span class="mr-2">✅</span> {{ session('success') }}
        </div>
    @endif

    {{-- FORMULAIRE UNIQUE DE MISE À JOUR --}}
    <form method="POST" action="{{ route('profile.update') }}" class="space-y-6">
        @csrf
        @method('PATCH')

        {{-- SECTION 1 : INFORMATIONS PERSONNELLES --}}
        <div class="bg-white dark:bg-gray-950 p-7 rounded-3xl shadow-xl border border-slate-100 dark:border-gray-800 space-y-5">
            <h2 class="text-lg font-bold text-slate-900 dark:text-white mb-2">Informations personnelles</h2>

            {{-- Code de connexion (lecture seule) --}}
            <div>
                <label class="block text-xs font-bold text-slate-400 uppercase tracking-wider mb-2">Code de connexion</label>
                <div class="flex items-center gap-3">
                    <input type="text" value="{{ $user->code_connexion }}" readonly disabled
                        class="w-full bg-slate-100 dark:bg-gray-900 border border-slate-200 dark:border-gray-800 rounded-xl px-4 py-3 text-sm font-mono font-bold tracking-widest text-amber-600 dark:text-amber-500 opacity-90 cursor-not-allowed">
                </div>
                <p class="text-[11px] text-slate-400 mt-1.5">Ce code sert à vous connecter. Il ne peut pas être modifié ici.</p>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                {{-- Nom --}}
                <div>
                    <label class="block text-xs font-bold text-slate-400 uppercase tracking-wider mb-2">Nom complet</label>
                    <input type="text" name="name" value="{{ old('name', $user->name) }}" required 
                        class="w-full bg-slate-50 dark:bg-gray-900 border border-slate-200 dark:border-gray-800 rounded-xl px-4 py-3 text-sm text-slate-800 dark:text-white focus:outline-none focus:ring-2 focus:ring-amber-500/25 focus:border-amber-500 transition">
                    @error('name') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
                </div>

                {{-- Téléphone --}}
                <div>
                    <label class="block text-xs font-bold text-slate-400 uppercase tracking-wider mb-2">Numéro de téléphone</label>
                    <input type="text" name="phone" value="{{ old('phone', $user->phone) }}" maxlength="15" placeholder="Ex: +237 6..."
                        class="w-full bg-slate-50 dark:bg-gray-900 border border-slate-200 dark:border-gray-800 rounded-xl px-4 py-3 text-sm text-slate-800 dark:text-white focus:outline-none focus:ring-2 focus:ring-amber-500/25 focus:border-amber-500 transition">
                    @error('phone') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
                </div>
            </div>

            {{-- Email --}}
            <div>
                <label class="block text-xs font-bold text-slate-400 uppercase tracking-wider mb-2">
                    Adresse Email 
                    @if($user->role !== 'administrateur') 
                        <span class="text-slate-400 font-normal lowercase">(réservé à l'administrateur)</span> 
                    @endif
                </label>
                <input type="email" name="email" value="{{ old('email', $user->email) }}" 
                    {{ $user->role !== 'administrateur' ? 'disabled' : 'required' }}
                    class="w-full bg-slate-50 dark:bg-gray-900 border border-slate-200 dark:border-gray-800 rounded-xl px-4 py-3 text-sm text-slate-800 dark:text-white {{ $user->role !== 'administrateur' ? 'opacity-60 cursor-not-allowed' : 'focus:outline-none focus:ring-2 focus:ring-amber-500/25 focus:border-amber-500' }} transition">
                @error('email') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
            </div>
        </div>

        {{-- SECTION 2 : SÉCURITÉ / MOT DE PASSE (OPTIONNEL) --}}
        <div class="bg-white dark:bg-gray-950 p-7 rounded-3xl shadow-xl border border-slate-100 dark:border-gray-800 space-y-5">
            <div>
                <h2 class="text-lg font-bold text-slate-900 dark:text-white">Modifier le mot de passe</h2>
                <p class="text-xs text-slate-400 mt-0.5">Laissez ces champs vides si vous ne souhaitez pas changer de mot de passe.</p>
            </div>

            <div>
                <label class="block text-xs font-bold text-slate-400 uppercase tracking-wider mb-2">Mot de passe actuel</label>
                <input type="password" name="current_password" placeholder="••••••••"
                    class="w-full bg-slate-50 dark:bg-gray-900 border border-slate-200 dark:border-gray-800 rounded-xl px-4 py-3 text-sm text-slate-800 dark:text-white focus:outline-none focus:ring-2 focus:ring-amber-500/25 focus:border-amber-500 transition">
                @error('current_password') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-bold text-slate-400 uppercase tracking-wider mb-2">Nouveau mot de passe</label>
                    <input type="password" name="password" placeholder="••••••••"
                        class="w-full bg-slate-50 dark:bg-gray-900 border border-slate-200 dark:border-gray-800 rounded-xl px-4 py-3 text-sm text-slate-800 dark:text-white focus:outline-none focus:ring-2 focus:ring-amber-500/25 focus:border-amber-500 transition">
                    @error('password') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-400 uppercase tracking-wider mb-2">Confirmer le mot de passe</label>
                    <input type="password" name="password_confirmation" placeholder="••••••••"
                        class="w-full bg-slate-50 dark:bg-gray-900 border border-slate-200 dark:border-gray-800 rounded-xl px-4 py-3 text-sm text-slate-800 dark:text-white focus:outline-none focus:ring-2 focus:ring-amber-500/25 focus:border-amber-500 transition">
                </div>
            </div>
        </div>

        {{-- BOUTON DE VALIDATION GLOBAL --}}
        <div class="flex justify-end">
            <button type="submit" class="bg-amber-500 hover:bg-amber-600 text-slate-900 text-sm font-bold px-8 py-3.5 rounded-xl shadow-md hover:shadow-lg transition">
                Enregistrer toutes les modifications
            </button>
        </div>

    </form>

</div>
@endsection