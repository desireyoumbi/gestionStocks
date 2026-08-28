@extends('layouts.app')

@section('header_subtitle', 'Enregistrer une nouvelle entrée de stock')
@section('header_title', 'Nouvel Approvisionnement')

@section('content')
<div class="max-w-3xl mx-auto relative">
    <div class="bg-white dark:bg-gray-950 border border-gray-100 dark:border-gray-800 rounded-2xl p-6 shadow-sm">
        
        <form id="supply-form" action="{{ route('supplies.store') }}" method="POST" class="space-y-6">
            @csrf

            <!-- Champ caché pour forcer la validation malgré un solde de caisse insuffisant -->
            <input type="hidden" name="force_cash" id="force_cash" value="0">

            <!-- Erreurs classiques de validation -->
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
                    <div class="col-span-2">
                        <input type="number" name="products[0][unit_price]" placeholder="Prix Achat" required class="product-price w-full px-4 py-3 bg-gray-50 dark:bg-gray-900 border border-gray-200 dark:border-gray-800 rounded-xl text-sm font-medium text-gray-800 dark:text-gray-200">
                    </div>
                    <div class="col-span-1 text-center">
                        <button type="button" class="remove-row p-2 text-rose-400 hover:text-rose-600 hover:bg-rose-500/10 rounded-xl transition" title="Retirer la ligne">
                            <svg class="w-5 h-5 mx-auto" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                            </svg>
                        </button>
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

<!-- MODALE PERSONNALISÉE D'AVERTISSEMENT CAISSE -->
<div id="cash-warning-modal" class="fixed inset-0 z-50 flex items-center justify-center bg-black/60 backdrop-blur-sm hidden">
    <div class="bg-white dark:bg-gray-900 border border-gray-200 dark:border-gray-800 rounded-2xl p-6 max-w-md w-full mx-4 shadow-2xl space-y-4">
        <div class="flex items-center space-x-3 text-amber-500">
            <svg class="w-8 h-8 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
            </svg>
            <h3 class="text-lg font-bold text-gray-900 dark:text-white">Solde de caisse insuffisant</h3>
        </div>
        
        <p class="text-sm text-gray-600 dark:text-gray-300">
            L'argent en caisse est insuffisant pour couvrir cet approvisionnement. Continuer mettra la caisse en négatif.
        </p>

        <div class="bg-gray-50 dark:bg-gray-950 p-3 rounded-xl space-y-1 text-xs font-medium text-gray-700 dark:text-gray-300">
            <div class="flex justify-between">
                <span>Montant de l'achat :</span>
                <span id="modal-required-amount" class="font-bold text-rose-500">0 FCFA</span>
            </div>
            <div class="flex justify-between">
                <span>Solde actuel :</span>
                <span id="modal-current-balance" class="font-bold text-emerald-500">0 FCFA</span>
            </div>
        </div>

        <div class="flex items-center justify-end gap-3 pt-2">
            <button type="button" id="modal-cancel" class="px-4 py-2 bg-gray-100 dark:bg-gray-800 hover:bg-gray-200 dark:hover:bg-gray-700 text-gray-700 dark:text-gray-300 font-bold text-xs rounded-xl transition">
                Annuler
            </button>
            <button type="button" id="modal-confirm" class="px-4 py-2 bg-amber-500 hover:bg-amber-600 text-gray-950 font-bold text-xs rounded-xl transition">
                Forcer et continuer
            </button>
        </div>
    </div>
</div>

<script>
    document.addEventListener("DOMContentLoaded", function() {
        @if(session('insufficient_cash'))
            let requiredAmount = "{{ number_format(session('required_amount'), 0, ',', ' ') }} FCFA";
            let currentBalance = "{{ number_format(session('current_balance'), 0, ',', ' ') }} FCFA";
            
            // Injecter les montants dans la modale
            document.getElementById('modal-required-amount').textContent = requiredAmount;
            document.getElementById('modal-current-balance').textContent = currentBalance;
            
            // Afficher la modale proprement
            let modal = document.getElementById('cash-warning-modal');
            modal.classList.remove('hidden');

            // Bouton Confirmer de la modale
            document.getElementById('modal-confirm').addEventListener('click', function() {
                document.getElementById('force_cash').value = '1';
                modal.classList.add('hidden');
                document.getElementById('supply-form').submit();
            });

            // Bouton Annuler de la modale
            document.getElementById('modal-cancel').addEventListener('click', function() {
                document.getElementById('force_cash').value = '0';
                modal.classList.add('hidden');
            });
        @endif
    });

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

    const container = document.getElementById('products-container');

    container.addEventListener('change', function(e) {
        if (e.target.classList.contains('product-select')) {
            let selectedOption = e.target.options[e.target.selectedIndex];
            let price = selectedOption.getAttribute('data-price') || 0;
            let row = e.target.closest('.product-row');
            row.querySelector('.product-price').value = price;
            calculateGrandTotal();
        }
    });

    container.addEventListener('input', function(e) {
        if (e.target.classList.contains('product-qty') || e.target.classList.contains('product-price')) {
            calculateGrandTotal();
        }
    });

    container.addEventListener('click', function(e) {
        if (e.target.closest('.remove-row')) {
            let row = e.target.closest('.product-row');
            if (document.querySelectorAll('.product-row').length > 1) {
                row.remove();
                calculateGrandTotal();
            } else {
                row.querySelector('.product-select').value = '';
                row.querySelector('.product-qty').value = '';
                row.querySelector('.product-price').value = '';
                calculateGrandTotal();
            }
        }
    });

    document.getElementById('add-product').addEventListener('click', function() {
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
            <div class="col-span-2">
                <input type="number" name="products[${productIndex}][unit_price]" placeholder="Prix Achat" required class="product-price w-full px-4 py-3 bg-gray-50 dark:bg-gray-900 border border-gray-200 dark:border-gray-800 rounded-xl text-sm font-medium text-gray-800 dark:text-gray-200">
            </div>
            <div class="col-span-1 text-center">
                <button type="button" class="remove-row p-2 text-rose-400 hover:text-rose-600 hover:bg-rose-500/10 rounded-xl transition" title="Retirer la ligne">
                    <svg class="w-5 h-5 mx-auto" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
        `;
        container.appendChild(newRow);
        productIndex++;
    });
</script>
@endsection