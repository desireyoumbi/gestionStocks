@extends('layouts.app')

@section('content')
<div class="max-w-4xl mx-auto space-y-6">
    <!-- En-tête -->
    <div>
        <h1 class="text-2xl font-bold text-white">Mon Profil</h1>
        <p class="text-sm text-gray-400">Consultez vos statistiques et gérez vos paramètres personnels.</p>
    </div>

    @if(session('success'))
        <div class="bg-green-500/10 border border-green-500 text-green-400 px-4 py-3 rounded-xl text-sm">
            {{ session('success') }}
        </div>
    @endif

    <!-- Grille : Statistiques & Infos rapides -->
    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
        <!-- Rôle -->
        <div class="bg-gray-900 border border-gray-800 rounded-2xl p-4">
            <div class="text-xs text-gray-400 uppercase font-semibold mb-1">Rôle dans le système</div>
            <div class="text-lg font-bold text-amber-500 capitalize">{{ $user->role }}</div>
        </div>
        <!-- Ancienneté -->
        <div class="bg-gray-900 border border-gray-800 rounded-2xl p-4">
            <div class="text-xs text-gray-400 uppercase font-semibold mb-1">Ancienneté</div>
            <div class="text-lg font-bold text-white">Membre depuis {{ $ancienneté }}</div>
        </div>
        <!-- Mouvements -->
        <div class="bg-gray-900 border border-gray-800 rounded-2xl p-4">
            <div class="text-xs text-gray-400 uppercase font-semibold mb-1">
                {{ $user->role === 'vendeur' ? 'Mes mouvements' : 'Mouvements boutique' }}
            </div>
            <div class="text-lg font-bold text-white">{{ $stats['total_movements'] }} enregistrement(s)</div>
        </div>
    </div>

    <!-- Formulaire Modification -->
    <div class="bg-gray-900 border border-gray-800 rounded-2xl p-6">
        <form action="{{ route('profile.update') }}" method="POST" class="space-y-4">
            @csrf
            @method('PATCH')

            <div>
                <label class="block text-xs font-semibold uppercase text-gray-400 mb-1">Nom complet</label>
                <input type="text" name="name" value="{{ old('name', $user->name) }}" 
                       class="w-full bg-gray-950 border border-gray-800 rounded-xl px-4 py-2.5 text-white text-sm focus:outline-none focus:border-amber-500">
                @error('name') <span class="text-red-400 text-xs">{{ $message }}</span> @enderror
            </div>

            <div>
                <label class="block text-xs font-semibold uppercase text-gray-400 mb-1">
                    Adresse Email 
                    @if($user->role !== 'administrateur') 
                        <span class="text-gray-500 lowercase font-normal">(non modifiable)</span> 
                    @endif
                </label>
                <input type="email" name="email" value="{{ old('email', $user->email) }}" 
                       {{ $user->role !== 'administrateur' ? 'disabled' : '' }}
                       class="w-full bg-gray-950 border border-gray-800 rounded-xl px-4 py-2.5 text-white text-sm focus:outline-none focus:border-amber-500 {{ $user->role !== 'administrateur' ? 'opacity-60 cursor-not-allowed' : '' }}">
                @error('email') <span class="text-red-400 text-xs">{{ $message }}</span> @enderror
            </div>

            <div class="pt-4 border-t border-gray-800">
                <h3 class="text-sm font-bold text-white mb-3">Changer le mot de passe (optionnel)</h3>
                
                <div class="space-y-3">
                    <div>
                        <label class="block text-xs font-semibold uppercase text-gray-400 mb-1">Mot de passe actuel</label>
                        <input type="password" name="current_password" 
                               class="w-full bg-gray-950 border border-gray-800 rounded-xl px-4 py-2.5 text-white text-sm focus:outline-none focus:border-amber-500">
                        @error('current_password') <span class="text-red-400 text-xs">{{ $message }}</span> @enderror
                    </div>

                    <div>
                        <label class="block text-xs font-semibold uppercase text-gray-400 mb-1">Nouveau mot de passe</label>
                        <input type="password" name="password" 
                               class="w-full bg-gray-950 border border-gray-800 rounded-xl px-4 py-2.5 text-white text-sm focus:outline-none focus:border-amber-500">
                        @error('password') <span class="text-red-400 text-xs">{{ $message }}</span> @enderror
                    </div>

                    <div>
                        <label class="block text-xs font-semibold uppercase text-gray-400 mb-1">Confirmer le nouveau mot de passe</label>
                        <input type="password" name="password_confirmation" 
                               class="w-full bg-gray-950 border border-gray-800 rounded-xl px-4 py-2.5 text-white text-sm focus:outline-none focus:border-amber-500">
                    </div>
                </div>
            </div>

            <div class="flex justify-end pt-4">
                <button type="submit" class="bg-amber-500 hover:bg-amber-600 text-gray-950 font-bold px-6 py-2.5 rounded-xl text-sm transition shadow-lg shadow-amber-500/20">
                    Enregistrer les modifications
                </button>
            </div>
        </form>
    </div>
</div>
@endsection