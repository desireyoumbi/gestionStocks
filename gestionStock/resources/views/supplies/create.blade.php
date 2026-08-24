@extends('layouts.app')

@section('header_subtitle', 'Enregistrer une nouvelle entrée de stock')
@section('header_title', 'Nouvel Approvisionnement')

@section('content')
<div class="max-w-3xl mx-auto relative">
    <div class="bg-white dark:bg-gray-950 border border-gray-100 dark:border-gray-800 rounded-2xl p-6 shadow-sm">
        
        <form action="{{ route('supplies.store') }}" method="POST" class="space-y-6">
            @csrf

            <!-- Erreurs classiques -->
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

    <!-- MODAL FIXE EN CAS D'ERREUR DE CAISSE INSUFFISANTE -->
    @if(session('cash_error'))
        <div class="fixed inset-0 z-50 flex items-center justify-center bg-slate-900/60 backdrop-blur-sm p-4">
            <div class="bg-white dark:bg-gray-900 border border-rose-500/30 rounded-3xl p-8 max-w-md w-full shadow-2xl space-y-6 text-center animate-in fade-in zoom-in duration-200">
                <div class="w-16 h-16 bg-rose-500/10 text-rose-500 rounded-2xl flex items-center justify-center mx-auto text-2xl font-bold shadow-inner">
                    ⚠️
                </div>
                <div>
                    <h3 class="text-lg font-black text-slate-900 dark:text-white tracking-tight">Fonds insuffisants en caisse</h3>
                    <p class="text-xs text-slate-500 dark:text-gray-400 mt-2 leading-relaxed">
                        {{ session('cash_error') }}
                    </p>
                </div>
                <div class="flex flex-col gap-3 pt-2">
                    <!-- Lien de redirection vers la page de gestion/ajustement de la caisse (ex: reports.annual ou ta route de caisse) -->
                    <a href="{{ route('reports.annual') }}" class="w-full py-3 bg-amber-500 hover:bg-amber-600 text-slate-950 font-bold text-xs rounded-xl shadow-lg shadow-amber-500/20 transition flex items-center justify-center gap-2">
                        <span>Ajuster la caisse & ajouter des fonds</span>
                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M14 5l7 7m0 0l-7 7m7-7H3" />
                        </svg>
                    </a>
                    <button type="button" onclick="this.closest('.fixed').style.display='none'" class="w-full py-3 bg-gray-100 dark:bg-gray-800 hover:bg-gray-200 dark:hover:bg-gray-700 text-gray-700 dark:text-gray-300 font-bold text-xs rounded-xl transition">
                        Fermer et modifier ma commande
                    </button>
                </div>
            </div>
        </div>
    @endif
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