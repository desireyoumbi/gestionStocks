@extends('layouts.app')

@section('content')
<div class="max-w-4xl mx-auto space-y-6">

    <!-- En-tête -->
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Effectuer un Mouvement de Stock</h1>
            <p class="text-sm text-gray-500 dark:text-gray-400">Enregistrez une entrée, une sortie ou un ajustement de produits.</p>
        </div>
        <a href="{{ route('movements.index') }}" class="text-sm text-gray-500 hover:text-gray-900 dark:hover:text-white transition">← Retour</a>
    </div>

    <!-- Formulaire -->
    <form action="{{ route('movements.store') }}" method="POST" class="bg-white dark:bg-gray-800 p-6 sm:p-8 rounded-2xl border border-gray-200 dark:border-gray-700 shadow-sm space-y-6">
        @csrf

        @if ($errors->any())
            <div class="bg-red-500/10 border border-red-500 text-red-500 p-4 rounded-xl text-sm">
                <ul class="list-disc pl-5 space-y-1">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
            <!-- Type -->
            <div>
                <label class="block text-xs font-semibold uppercase text-gray-500 dark:text-gray-400 mb-2">Type de mouvement</label>
                <select name="type" required class="w-full bg-gray-50 dark:bg-gray-900 border border-gray-300 dark:border-gray-700 rounded-xl px-4 py-2.5 text-sm text-gray-900 dark:text-gray-100 focus:outline-none focus:border-amber-500">
                    <option value="entree">Entrée</option>
                    <option value="sortie">Sortie</option>
                    <option value="ajustement">Ajustement</option>
                </select>
            </div>

            <!-- Date (Fixée à aujourd'hui et non modifiable) -->
            <div>
                <label class="block text-xs font-semibold uppercase text-gray-500 dark:text-gray-400 mb-2">Date du jour</label>
                <!-- Champ caché pour envoyer la valeur réelle de la date au contrôleur -->
                <input type="hidden" name="date" value="{{ date('Y-m-d') }}">
                
                <!-- Champ visuel non modifiable -->
                <input type="text" value="{{ date('d/m/Y') }}" disabled class="w-full bg-gray-100 dark:bg-gray-900/80 border border-gray-300 dark:border-gray-700 rounded-xl px-4 py-2.5 text-sm text-gray-500 dark:text-gray-400 cursor-not-allowed">
            </div>

            <!-- Motif -->
            <div>
                <label class="block text-xs font-semibold uppercase text-gray-500 dark:text-gray-400 mb-2">Motif / Référence</label>
                <input type="text" name="motif" placeholder="Ex: Achat fournisseur, Vente..." required class="w-full bg-gray-50 dark:bg-gray-900 border border-gray-300 dark:border-gray-700 rounded-xl px-4 py-2.5 text-sm text-gray-900 dark:text-gray-100 focus:outline-none focus:border-amber-500">
            </div>
        </div>

        <hr class="border-gray-200 dark:border-gray-700">

        <!-- Liste dynamique des produits -->
        <div>
            <div class="flex justify-between items-center mb-4">
                <h3 class="text-base font-bold text-gray-900 dark:text-white">Produits concernés</h3>
                <button type="button" id="add-product-btn" class="text-xs bg-amber-500/10 text-amber-500 hover:bg-amber-500 hover:text-gray-900 font-semibold px-3 py-1.5 rounded-lg transition">
                    + Ajouter un produit
                </button>
            </div>

            <div id="products-container" class="space-y-3">
                <!-- Ligne de produit par défaut -->
                <div class="product-row flex items-center gap-3 bg-gray-50 dark:bg-gray-900/50 p-3 rounded-xl border border-gray-200 dark:border-gray-700">
                    <div class="flex-1">
                        <select name="items[0][product_id]" required class="w-full bg-white dark:bg-gray-900 border border-gray-300 dark:border-gray-700 rounded-lg px-3 py-2 text-sm text-gray-900 dark:text-gray-100">
                            <option value="">Sélectionner un produit</option>
                            @foreach($products as $product)
                                <option value="{{ $product->id }}">{{ $product->name }} (Stock actuel : {{ $product->quantity }})</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="w-32">
                        <input type="number" name="items[0][qty]" placeholder="Quantité" min="1" required class="w-full bg-white dark:bg-gray-900 border border-gray-300 dark:border-gray-700 rounded-lg px-3 py-2 text-sm text-gray-900 dark:text-gray-100">
                    </div>
                    <button type="button" class="remove-row text-red-400 hover:text-red-600 p-2 font-bold">×</button>
                </div>
            </div>
        </div>

        <!-- Bouton de validation -->
        <div class="flex justify-end pt-4">
            <button type="submit" class="bg-amber-500 hover:bg-amber-600 text-gray-900 font-bold px-6 py-3 rounded-xl transition shadow-lg shadow-amber-500/20 text-sm">
                Enregistrer le mouvement
            </button>
        </div>
    </form>

</div>

<!-- Script JS pour gérer l'ajout/suppression dynamique des lignes de produits -->
<script>
    let rowIndex = 1;
    document.getElementById('add-product-btn').addEventListener('click', function() {
        const container = document.getElementById('products-container');
        const newRow = document.createElement('div');
        newRow.className = 'product-row flex items-center gap-3 bg-gray-50 dark:bg-gray-900/50 p-3 rounded-xl border border-gray-200 dark:border-gray-700';
        newRow.innerHTML = `
            <div class="flex-1">
                <select name="items[${rowIndex}][product_id]" required class="w-full bg-white dark:bg-gray-900 border border-gray-300 dark:border-gray-700 rounded-lg px-3 py-2 text-sm text-gray-900 dark:text-gray-100">
                    <option value="">Sélectionner un produit</option>
                    @foreach($products as $product)
                        <option value="{{ $product->id }}">{{ $product->name }} (Stock actuel : {{ $product->quantity }})</option>
                    @endforeach
                </select>
            </div>
            <div class="w-32">
                <input type="number" name="items[${rowIndex}][qty]" placeholder="Quantité" min="1" required class="w-full bg-white dark:bg-gray-900 border border-gray-300 dark:border-gray-700 rounded-lg px-3 py-2 text-sm text-gray-900 dark:text-gray-100">
            </div>
            <button type="button" class="remove-row text-red-400 hover:text-red-600 p-2 font-bold">×</button>
        `;
        container.appendChild(newRow);
        rowIndex++;
    });

    document.addEventListener('click', function(e) {
        if (e.target && e.target.classList.contains('remove-row')) {
            const rows = document.querySelectorAll('.product-row');
            if (rows.length > 1) {
                e.target.closest('.product-row').remove();
            } else {
                alert('Le mouvement doit contenir au moins un produit.');
            }
        }
    });
</script>
@endsection