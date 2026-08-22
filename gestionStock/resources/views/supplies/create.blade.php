@extends('layouts.app')

@section('header_subtitle', 'Enregistrer une nouvelle entrée de stock')
@section('header_title', 'Nouvel Approvisionnement')

@section('content')
<div class="max-w-3xl mx-auto">
    <div class="bg-white dark:bg-gray-950 border border-gray-100 dark:border-gray-800 rounded-2xl p-6 shadow-sm">
        
        <form action="{{ route('supplies.store') }}" method="POST" class="space-y-6">
            @csrf

            <!-- Erreurs -->
            @if($errors->any())
                <div class="p-4 rounded-xl bg-rose-500/10 border border-rose-500/20 text-rose-500 text-sm font-semibold">
                    <ul class="list-disc pl-4 space-y-1">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <!-- Sélection du Fournisseur -->
            <div>
                <label class="block text-xs font-bold uppercase tracking-wider text-gray-500 dark:text-gray-400 mb-2">Fournisseur</label>
                <select name="supplier_id" required class="w-full px-4 py-3 bg-gray-50 dark:bg-gray-900 border border-gray-200 dark:border-gray-800 rounded-xl text-sm font-medium text-gray-800 dark:text-gray-200 focus:outline-none focus:border-amber-500 transition">
                    <option value="">Sélectionnez un fournisseur...</option>
                    @foreach($suppliers as $supplier)
                        <option value="{{ $supplier->id }}">{{ $supplier->name }}</option>
                    @endforeach
                </select>
            </div>

            <!-- Liste dynamique des produits -->
            <div id="products-container" class="space-y-4">
                <label class="block text-xs font-bold uppercase tracking-wider text-gray-500 dark:text-gray-400">Produits livrés</label>
                
                <div class="product-row grid grid-cols-12 gap-3 items-center">
                    <div class="col-span-6">
                        <select name="products[0][id]" required class="product-select w-full px-4 py-3 bg-gray-50 dark:bg-gray-900 border border-gray-200 dark:border-gray-800 rounded-xl text-sm font-medium text-gray-800 dark:text-gray-200">
                            <option value="">Sélectionner un produit...</option>
                            @foreach($products as $product)
                                <option value="{{ $product->id }}" data-price="{{ $product->purchase_price ?? 0 }}">{{ $product->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-span-3">
                        <input type="number" name="products[0][qty]" placeholder="Qté" min="1" required class="product-qty w-full px-4 py-3 bg-gray-50 dark:bg-gray-900 border border-gray-200 dark:border-gray-800 rounded-xl text-sm font-medium text-gray-800 dark:text-gray-200">
                    </div>
                    <div class="col-span-3">
                        <input type="number" name="products[0][unit_price]" placeholder="Prix Achat" required class="product-price w-full px-4 py-3 bg-gray-50 dark:bg-gray-900 border border-gray-200 dark:border-gray-800 rounded-xl text-sm font-medium text-gray-800 dark:text-gray-200">
                    </div>
                </div>
            </div>

            <button type="button" id="add-product" class="text-xs font-bold text-amber-600 hover:text-amber-700 transition">
                + Ajouter un autre produit
            </button>

            <!-- Affichage du Prix Total Global -->
            <div class="p-4 rounded-xl bg-gray-50 dark:bg-gray-900 border border-gray-100 dark:border-gray-800 flex items-center justify-between">
                <span class="text-xs font-bold uppercase tracking-wider text-gray-500">Montant Total de l'Approvisionnement :</span>
                <span id="grand-total" class="text-base font-black text-emerald-500">0 FCFA</span>
            </div>

            <!-- Motif -->
            <div>
                <label class="block text-xs font-bold uppercase tracking-wider text-gray-500 dark:text-gray-400 mb-2">Motif (Optionnel)</label>
                <input type="text" name="motif" placeholder="Ex: Réception commande fournisseur" class="w-full px-4 py-3 bg-gray-50 dark:bg-gray-900 border border-gray-200 dark:border-gray-800 rounded-xl text-sm font-medium text-gray-800 dark:text-gray-200 focus:outline-none focus:border-amber-500 transition">
            </div>

            <!-- Boutons -->
            <div class="flex items-center justify-end gap-3 pt-4 border-t border-gray-100 dark:border-gray-900">
                <a href="{{ route('supplies.index') }}" class="px-5 py-2.5 bg-gray-100 dark:bg-gray-900 hover:bg-gray-200 dark:hover:bg-gray-800 text-gray-700 dark:text-gray-300 font-bold text-sm rounded-xl transition">Annuler</a>
                <button type="submit" class="px-5 py-2.5 bg-emerald-500 hover:bg-emerald-600 text-gray-950 font-bold text-sm rounded-xl shadow-lg shadow-emerald-500/20 transition">Enregistrer</button>
            </div>
        </form>
    </div>
</div>

<script>
    let productIndex = 1;

    function calculateGrandTotal() {
        let grandTotal = 0;
        document.querySelectorAll('.product-row').forEach(row => {
            let qty = parseFloat(row.querySelector('.product-qty').value) || 0;
            let price = parseFloat(row.querySelector('.product-price').value) || 0;
            grandTotal += qty * price;
        });
        document.getElementById('grand-total').textContent = new Intl.NumberFormat('fr-FR').format(grandTotal) + ' FCFA';
    }

    document.getElementById('products-container').addEventListener('change', function(e) {
        if (e.target.classList.contains('product-select')) {
            let selectedOption = e.target.options[e.target.selectedIndex];
            let price = selectedOption.getAttribute('data-price') || 0;
            let row = e.target.closest('.product-row');
            row.querySelector('.product-price').value = price;
            calculateGrandTotal();
        }
    });

    document.getElementById('products-container').addEventListener('input', function(e) {
        if (e.target.classList.contains('product-qty') || e.target.classList.contains('product-price')) {
            calculateGrandTotal();
        }
    });

    document.getElementById('add-product').addEventListener('click', function() {
        let container = document.getElementById('products-container');
        let newRow = document.createElement('div');
        newRow.className = 'product-row grid grid-cols-12 gap-3 items-center';
        newRow.innerHTML = `
            <div class="col-span-6">
                <select name="products[${productIndex}][id]" required class="product-select w-full px-4 py-3 bg-gray-50 dark:bg-gray-900 border border-gray-200 dark:border-gray-800 rounded-xl text-sm font-medium text-gray-800 dark:text-gray-200">
                    <option value="">Sélectionner un produit...</option>
                    @foreach($products as $product)
                        <option value="{{ $product->id }}" data-price="{{ $product->purchase_price ?? 0 }}">{{ $product->name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-span-3">
                <input type="number" name="products[${productIndex}][qty]" placeholder="Qté" min="1" required class="product-qty w-full px-4 py-3 bg-gray-50 dark:bg-gray-900 border border-gray-200 dark:border-gray-800 rounded-xl text-sm font-medium text-gray-800 dark:text-gray-200">
            </div>
            <div class="col-span-3">
                <input type="number" name="products[${productIndex}][unit_price]" placeholder="Prix Achat" required class="product-price w-full px-4 py-3 bg-gray-50 dark:bg-gray-900 border border-gray-200 dark:border-gray-800 rounded-xl text-sm font-medium text-gray-800 dark:text-gray-200">
            </div>
        `;
        container.appendChild(newRow);
        productIndex++;
    });
</script>
@endsection