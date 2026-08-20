@extends('layouts.app')

@section('content')
<div class="max-w-2xl mx-auto space-y-6">

    <!-- En-tête -->
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Modifier le Mouvement #{{ $movement->id }}</h1>
            <p class="text-sm text-gray-500 dark:text-gray-400">Mise à jour du motif ou de la date du bon.</p>
        </div>
        <a href="{{ route('movements.show', $movement->id) }}" class="text-sm text-gray-500 hover:text-gray-900 dark:hover:text-white transition">← Retour</a>
    </div>

    <!-- Formulaire -->
    <form action="{{ route('movements.update', $movement->id) }}" method="POST" class="bg-white dark:bg-gray-800 p-6 sm:p-8 rounded-2xl border border-gray-200 dark:border-gray-700 shadow-sm space-y-6">
        @csrf
        @method('PUT')

        @if ($errors->any())
            <div class="bg-red-500/10 border border-red-500 text-red-500 p-4 rounded-xl text-sm">
                <ul class="list-disc pl-5 space-y-1">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <!-- Date -->
        <div>
            <label class="block text-xs font-semibold uppercase text-gray-500 dark:text-gray-400 mb-2">Date</label>
            <input type="date" name="date" value="{{ old('date', $movement->date) }}" required class="w-full bg-gray-50 dark:bg-gray-900 border border-gray-300 dark:border-gray-700 rounded-xl px-4 py-2.5 text-sm text-gray-900 dark:text-gray-100 focus:outline-none focus:border-amber-500">
        </div>

        <!-- Motif -->
        <div>
            <label class="block text-xs font-semibold uppercase text-gray-500 dark:text-gray-400 mb-2">Motif / Référence</label>
            <textarea name="motif" rows="3" required class="w-full bg-gray-50 dark:bg-gray-900 border border-gray-300 dark:border-gray-700 rounded-xl px-4 py-2.5 text-sm text-gray-900 dark:text-gray-100 focus:outline-none focus:border-amber-500">{{ old('motif', $movement->motif) }}</textarea>
        </div>

        <div class="bg-amber-500/10 border border-amber-500/30 p-4 rounded-xl text-xs text-amber-600 dark:text-amber-400">
            <strong>Note :</strong> Pour des raisons de sécurité des stocks, la modification des produits et quantités n'est pas modifiable directement ici. En cas d'erreur sur les quantités, veuillez annuler ce mouvement puis en créer un nouveau.
        </div>

        <!-- Bouton de validation -->
        <div class="flex justify-end gap-3 pt-2">
            <a href="{{ route('movements.show', $movement->id) }}" class="bg-gray-200 dark:bg-gray-700 hover:bg-gray-300 text-gray-700 dark:text-gray-300 font-medium px-5 py-2.5 rounded-xl text-sm transition flex items-center">
                Annuler
            </a>
            <button type="submit" class="bg-amber-500 hover:bg-amber-600 text-gray-900 font-bold px-6 py-2.5 rounded-xl transition shadow-lg shadow-amber-500/20 text-sm">
                Enregistrer les modifications
            </button>
        </div>
    </form>

</div>
@endsection