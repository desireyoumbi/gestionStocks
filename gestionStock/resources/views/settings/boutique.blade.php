@extends('layouts.app')
@section('header_title', 'Configurer ma boutique')
@section('content')
@if(Auth::check() && Auth::user()->role === 'administrateur')
<div class="max-w-3xl mx-auto space-y-6">
    <h1 class="text-2xl font-bold text-white">Paramètres de la Boutique</h1>

    @if(session('success'))
        <div class="bg-green-500/10 border border-green-500 text-green-400 p-4 rounded-xl text-sm">{{ session('success') }}</div>
    @endif

    @if($errors->any())
        <div class="bg-red-500/10 border border-red-500 text-red-400 p-4 rounded-xl text-sm">
            <ul class="list-disc list-inside">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <!-- Bloc Infos Générales, Logo & Couleur -->
    <div class="bg-gray-900 border border-gray-800 rounded-2xl p-6">
        <h2 class="text-white font-bold mb-4">Informations & Personnalisation</h2>
        <form action="{{ route('boutique.update') }}" method="POST" enctype="multipart/form-data">
            @csrf @method('PATCH')
            <div class="space-y-4">
                <!-- Nom -->
                <div>
                    <label class="block text-xs text-gray-400 uppercase font-semibold mb-1">Nom de la boutique</label>
                    <input type="text" name="nom" value="{{ old('nom', $boutique->nom) }}" class="w-full bg-gray-950 border border-gray-800 rounded-lg p-2.5 text-white text-sm focus:outline-none focus:border-amber-500">
                </div>

                <!-- Logo -->
                <div>
                    <label class="block text-xs text-gray-400 uppercase font-semibold mb-1">Logo de la boutique</label>
                    <div class="flex items-center gap-4">
                        @if(!empty($boutique->logo))
                            <img src="{{ asset('storage/' . $boutique->logo) }}" alt="Logo" class="w-12 h-12 rounded-lg object-cover border border-gray-800">
                        @else
                            <div class="w-12 h-12 rounded-lg bg-gray-950 border border-gray-800 flex items-center justify-center text-xs text-gray-500 font-bold">
                                Aucun
                            </div>
                        @endif
                        <input type="file" name="logo" class="w-full text-xs text-gray-400 file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-xs file:font-semibold file:bg-gray-800 file:text-white hover:file:bg-gray-750 transition cursor-pointer">
                    </div>
                </div>

                <!-- Couleur principale -->
                <div>
                    <label class="block text-xs text-gray-400 uppercase font-semibold mb-1">Couleur principale de l'interface</label>
                    <div class="flex items-center gap-3">
                        <input type="color" name="primary_color" value="{{ old('primary_color', $boutique->primary_color ?? '#f59e0b') }}" class="w-10 h-10 rounded-lg bg-gray-950 border border-gray-800 cursor-pointer p-1">
                        <span class="text-xs text-gray-400">Personnalise la couleur des éléments clés (boutons, accents).</span>
                    </div>
                </div>

                <!-- Créée le -->
                <div>
                    <label class="block text-xs text-gray-400 uppercase font-semibold mb-1">Créée le</label>
                    <input type="text" disabled value="{{ $boutique->created_at->format('d/m/Y à H:i') }}" class="w-full bg-gray-950 border border-gray-800 rounded-lg p-2.5 text-gray-500 text-sm cursor-not-allowed">
                </div>
            </div>
            <button type="submit" class="mt-4 bg-amber-500 hover:bg-amber-600 text-gray-950 font-bold px-4 py-2.5 rounded-lg text-sm transition">Enregistrer</button>
        </form>
    </div>

    <!-- Bloc Transfert de propriété -->
    <div class="bg-gray-900 border border-amber-500/30 rounded-2xl p-6">
        <h2 class="text-amber-500 font-bold mb-2">Transfert de propriété</h2>
        <p class="text-gray-400 text-sm mb-4">Vous pouvez transférer vos droits d'administrateur à un autre membre. Cette action est irréversible.</p>
        
        <form action="{{ route('boutique.transfer') }}" method="POST" class="space-y-3">
            @csrf
            <select name="new_admin_id" class="w-full bg-gray-950 border border-gray-800 rounded-lg p-2.5 text-white text-sm focus:outline-none focus:border-amber-500">
                @foreach($staff as $member)
                    @if($member->id !== Auth::id())
                        <option value="{{ $member->id }}">{{ $member->name }} ({{ $member->role }})</option>
                    @endif
                @endforeach
            </select>
            <input type="password" name="password" placeholder="Confirmez votre mot de passe" class="w-full bg-gray-950 border border-gray-800 rounded-lg p-2.5 text-white text-sm focus:outline-none focus:border-amber-500">
            <button type="submit" class="bg-red-900/80 hover:bg-red-900 text-red-200 px-4 py-2.5 rounded-lg text-sm font-bold transition">Confirmer le transfert</button>
        </form>
    </div>

    <!-- Zone Dangereuse -->
    <div class="bg-gray-950 border border-red-900/50 rounded-2xl p-6">
        <h2 class="text-red-500 font-bold mb-2">Zone Dangereuse</h2>
        <form action="{{ route('boutique.destroy') }}" method="POST" class="space-y-4">
            @csrf @method('DELETE')
            
            <p class="text-red-400/70 text-sm">Tapez <span class="font-mono text-red-300">SUPPRIMER_MA_BOUTIQUE</span> pour confirmer et entrez votre mot de passe administrateur.</p>
            
            <div>
                <label class="block text-xs text-gray-400 uppercase font-semibold mb-1">Texte de confirmation</label>
                <input type="text" name="confirm_text" required placeholder="SUPPRIMER_MA_BOUTIQUE" class="w-full bg-black border border-red-900 rounded-lg p-2.5 text-red-500 text-sm focus:outline-none focus:border-red-500">
            </div>

            <div>
                <label class="block text-xs text-gray-400 uppercase font-semibold mb-1">Mot de passe administrateur</label>
                <input type="password" name="password" required placeholder="Votre mot de passe actuel" class="w-full bg-black border border-red-900 rounded-lg p-2.5 text-white text-sm focus:outline-none focus:border-red-500">
            </div>

            <button type="submit" class="bg-red-600 hover:bg-red-700 text-white font-bold px-4 py-2.5 rounded-lg text-sm transition shadow-lg shadow-red-600/20">
                Supprimer définitivement la boutique
            </button>
        </form>
    </div>
</div>
@else
<div class="bg-red-500/10 border border-red-500 text-red-400 p-6 rounded-2xl text-center">
    <h2 class="text-lg font-bold mb-1">Accès refusé</h2>
    <p class="text-sm">Cette page est strictement réservée à l'administrateur de la boutique.</p>
</div>
@endif
@endsection