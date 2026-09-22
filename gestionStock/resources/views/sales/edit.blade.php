@extends('layouts.app')

@section('title', 'Modifier la vente')

@section('content')

<div class="max-w-6xl mx-auto px-4 sm:px-6 py-6">

    {{-- En-tête --}}
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-6">

        <div>
            <div class="flex items-center gap-3">
                <a href="{{ route('sales.index') }}"
                   class="w-10 h-10 rounded-xl flex items-center justify-center
                          bg-gray-100 dark:bg-gray-800
                          text-gray-600 dark:text-gray-300
                          hover:bg-gray-200 dark:hover:bg-gray-700
                          transition">
                    <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round"
                              d="M15.75 19.5L8.25 12l7.5-7.5"/>
                    </svg>
                </a>

                <div>
                    <h1 class="text-2xl font-bold text-gray-900 dark:text-white">
                        Modifier la vente #{{ $sale->id }}
                    </h1>

                    <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">
                        Modifiez les produits, quantités ou prix de cette vente.
                    </p>
                </div>
            </div>
        </div>

        <div class="text-sm text-gray-500 dark:text-gray-400">
            Créée le {{ $sale->created_at->format('d/m/Y à H:i') }}
        </div>

    </div>


    {{-- Messages d'erreur --}}
    @if ($errors->any())
        <div class="mb-6 rounded-2xl border border-red-200 dark:border-red-900
                    bg-red-50 dark:bg-red-950/40 p-4">

            <div class="flex items-start gap-3">

                <svg class="w-5 h-5 text-red-600 dark:text-red-400 mt-0.5 shrink-0"
                     fill="none"
                     viewBox="0 0 24 24"
                     stroke="currentColor"
                     stroke-width="2">
                    <path stroke-linecap="round"
                          stroke-linejoin="round"
                          d="M12 9v3.75m0 3.75h.007M12 3.75a8.25 8.25 0 100 16.5 8.25 8.25 0 000-16.5z"/>
                </svg>

                <div>
                    <p class="font-semibold text-red-800 dark:text-red-300">
                        Impossible de modifier la vente
                    </p>

                    <ul class="mt-1 text-sm text-red-700 dark:text-red-400 list-disc list-inside">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>

            </div>
        </div>
    @endif


    {{-- Formulaire --}}
    <form action="{{ route('sales.update', $sale->id) }}"
          method="POST"
          id="sale-form">

        @csrf
        @method('PUT')


        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

            {{-- Partie produits --}}
            <div class="lg:col-span-2">

                <div class="bg-white dark:bg-gray-950
                            border border-gray-200 dark:border-gray-800
                            rounded-2xl shadow-sm overflow-hidden">

                    {{-- Header --}}
                    <div class="px-5 py-4 border-b border-gray-200 dark:border-gray-800
                                flex items-center justify-between">

                        <div>
                            <h2 class="font-semibold text-gray-900 dark:text-white">
                                Produits de la vente
                            </h2>

                            <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">
                                Modifiez les quantités et les prix si nécessaire.
                            </p>
                        </div>

                        <button type="button"
                                id="add-product"
                                class="inline-flex items-center gap-2
                                       px-4 py-2 rounded-xl
                                       bg-gray-900 dark:bg-white
                                       text-white dark:text-gray-900
                                       text-sm font-semibold
                                       hover:opacity-90 transition">

                            <svg class="w-4 h-4"
                                 fill="none"
                                 viewBox="0 0 24 24"
                                 stroke="currentColor"
                                 stroke-width="2">
                                <path stroke-linecap="round"
                                      stroke-linejoin="round"
                                      d="M12 4.5v15m7.5-7.5h-15"/>
                            </svg>

                            Ajouter
                        </button>

                    </div>


                    {{-- Lignes --}}
                    <div id="products-container" class="divide-y divide-gray-200 dark:divide-gray-800">

                        @foreach ($sale->products as $index => $product)

                            <div class="product-row p-5"
                                 data-row>

                                <div class="grid grid-cols-1 sm:grid-cols-12 gap-4 items-end">

                                    {{-- Produit --}}
                                    <div class="sm:col-span-5">

                                        <label class="block text-sm font-medium
                                                      text-gray-700 dark:text-gray-300 mb-2">
                                            Produit
                                        </label>

                                        <select name="items[{{ $index }}][product_id]"
                                                class="product-select w-full rounded-xl
                                                       border border-gray-300 dark:border-gray-700
                                                       bg-white dark:bg-gray-900
                                                       text-gray-900 dark:text-white
                                                       px-3 py-2.5
                                                       focus:ring-2 focus:ring-gray-500
                                                       focus:border-transparent"
                                                required>

                                            <option value="">Sélectionner un produit</option>

                                            @foreach ($products as $availableProduct)

                                                <option value="{{ $availableProduct->id }}"
                                                        data-purchase-price="{{ $availableProduct->purchase_price }}"
                                                        data-selling-price="{{ $availableProduct->selling_price }}"
                                                        @selected($availableProduct->id == $product->id)>
                                                    {{ $availableProduct->name }}
                                                    @if($availableProduct->reference)
                                                        — {{ $availableProduct->reference }}
                                                    @endif
                                                </option>

                                            @endforeach

                                        </select>

                                    </div>


                                    {{-- Quantité --}}
                                    <div class="sm:col-span-2">

                                        <label class="block text-sm font-medium
                                                      text-gray-700 dark:text-gray-300 mb-2">
                                            Quantité
                                        </label>

                                        <input type="number"
                                               name="items[{{ $index }}][quantity]"
                                               value="{{ old("items.$index.quantity", $product->pivot->qty) }}"
                                               min="1"
                                               step="1"
                                               class="quantity-input w-full rounded-xl
                                                      border border-gray-300 dark:border-gray-700
                                                      bg-white dark:bg-gray-900
                                                      text-gray-900 dark:text-white
                                                      px-3 py-2.5
                                                      focus:ring-2 focus:ring-gray-500
                                                      focus:border-transparent"
                                               required>

                                    </div>


                                    {{-- Prix --}}
                                    <div class="sm:col-span-3">

                                        <label class="block text-sm font-medium
                                                      text-gray-700 dark:text-gray-300 mb-2">
                                            Prix unitaire
                                        </label>

                                        <div class="relative">

                                            <input type="number"
                                                   name="items[{{ $index }}][unit_price]"
                                                   value="{{ old("items.$index.unit_price", $product->pivot->unit_price) }}"
                                                   min="0"
                                                   step="0.01"
                                                   class="price-input w-full rounded-xl
                                                          border border-gray-300 dark:border-gray-700
                                                          bg-white dark:bg-gray-900
                                                          text-gray-900 dark:text-white
                                                          px-3 py-2.5 pr-14
                                                          focus:ring-2 focus:ring-gray-500
                                                          focus:border-transparent"
                                                   required>

                                            <span class="absolute right-3 top-1/2 -translate-y-1/2
                                                         text-xs text-gray-500 dark:text-gray-400">
                                                FCFA
                                            </span>

                                        </div>

                                    </div>


                                    {{-- Suppression --}}
                                    <div class="sm:col-span-2">

                                        <button type="button"
                                                class="remove-product w-full inline-flex
                                                       items-center justify-center gap-2
                                                       px-3 py-2.5 rounded-xl
                                                       border border-red-200 dark:border-red-900
                                                       text-red-600 dark:text-red-400
                                                       hover:bg-red-50 dark:hover:bg-red-950/40
                                                       transition">

                                            <svg class="w-4 h-4"
                                                 fill="none"
                                                 viewBox="0 0 24 24"
                                                 stroke="currentColor"
                                                 stroke-width="2">
                                                <path stroke-linecap="round"
                                                      stroke-linejoin="round"
                                                      d="M6 7.5h12M9.75 7.5V5.25A1.5 1.5 0 0111.25 3.75h1.5a1.5 1.5 0 011.5 1.5V7.5m-7.5 0v11.25A2.25 2.25 0 009 21h6a2.25 2.25 0 002.25-2.25V7.5M10.5 11.25v6m3-6v6"/>
                                            </svg>

                                            Supprimer
                                        </button>

                                    </div>

                                </div>


                                {{-- Total de la ligne --}}
                                <div class="mt-3 text-right">

                                    <span class="text-xs text-gray-500 dark:text-gray-400">
                                        Total ligne :
                                    </span>

                                    <span class="line-total ml-1 font-semibold
                                                 text-gray-900 dark:text-white">
                                        0 FCFA
                                    </span>

                                </div>

                            </div>

                        @endforeach

                    </div>


                    {{-- Aucun produit --}}
                    <div id="empty-products"
                         class="hidden px-5 py-10 text-center">

                        <p class="text-sm text-gray-500 dark:text-gray-400">
                            Aucun produit dans cette vente.
                        </p>

                        <button type="button"
                                id="add-product-empty"
                                class="mt-3 text-sm font-semibold
                                       text-gray-900 dark:text-white
                                       underline">
                            Ajouter un produit
                        </button>

                    </div>

                </div>

            </div>


            {{-- Résumé --}}
            <div class="lg:col-span-1">

                <div class="lg:sticky lg:top-6 space-y-5">

                    {{-- Total --}}
                    <div class="bg-gray-900 dark:bg-gray-800
                                rounded-2xl p-5 text-white shadow-sm">

                        <p class="text-sm text-gray-300">
                            Nouveau total
                        </p>

                        <p id="grand-total"
                           class="text-3xl font-bold mt-2">
                            0 FCFA
                        </p>

                        <p class="text-xs text-gray-400 mt-2">
                            Le montant sera également mis à jour dans la caisse.
                        </p>

                    </div>


                    {{-- Motif --}}
                    <div class="bg-white dark:bg-gray-950
                                border border-gray-200 dark:border-gray-800
                                rounded-2xl p-5 shadow-sm">

                        <label for="motif"
                               class="block text-sm font-semibold
                                      text-gray-700 dark:text-gray-300 mb-2">
                            Motif
                        </label>

                        <textarea name="motif"
                                  id="motif"
                                  rows="4"
                                  maxlength="255"
                                  class="w-full rounded-xl
                                         border border-gray-300 dark:border-gray-700
                                         bg-white dark:bg-gray-900
                                         text-gray-900 dark:text-white
                                         px-3 py-2.5
                                         focus:ring-2 focus:ring-gray-500
                                         focus:border-transparent"
                                  placeholder="Motif de la vente...">{{ old('motif', $sale->motif) }}</textarea>

                    </div>


                    {{-- Actions --}}
                    <div class="bg-white dark:bg-gray-950
                                border border-gray-200 dark:border-gray-800
                                rounded-2xl p-5 shadow-sm">

                        <button type="submit"
                                class="w-full inline-flex items-center justify-center gap-2
                                       px-4 py-3 rounded-xl
                                       bg-gray-900 dark:bg-white
                                       text-white dark:text-gray-900
                                       font-semibold
                                       hover:opacity-90 transition">

                            <svg class="w-5 h-5"
                                 fill="none"
                                 viewBox="0 0 24 24"
                                 stroke="currentColor"
                                 stroke-width="2">
                                <path stroke-linecap="round"
                                      stroke-linejoin="round"
                                      d="M5.25 12h13.5M12.75 5.25L19.5 12l-6.75 6.75"/>
                            </svg>

                            Enregistrer les modifications

                        </button>


                        <a href="{{ route('sales.index') }}"
                           class="mt-3 w-full inline-flex items-center justify-center
                                  px-4 py-3 rounded-xl
                                  border border-gray-300 dark:border-gray-700
                                  text-gray-700 dark:text-gray-300
                                  font-semibold
                                  hover:bg-gray-100 dark:hover:bg-gray-800
                                  transition">

                            Annuler

                        </a>

                    </div>

                </div>

            </div>

        </div>

    </form>

</div>


{{-- Template d'une nouvelle ligne --}}
<template id="product-template">

    <div class="product-row p-5"
         data-row>

        <div class="grid grid-cols-1 sm:grid-cols-12 gap-4 items-end">

            <div class="sm:col-span-5">

                <label class="block text-sm font-medium
                              text-gray-700 dark:text-gray-300 mb-2">
                    Produit
                </label>

                <select class="product-select w-full rounded-xl
                               border border-gray-300 dark:border-gray-700
                               bg-white dark:bg-gray-900
                               text-gray-900 dark:text-white
                               px-3 py-2.5
                               focus:ring-2 focus:ring-gray-500
                               focus:border-transparent"
                        required>

                    <option value="">Sélectionner un produit</option>

                    @foreach ($products as $availableProduct)

                        <option value="{{ $availableProduct->id }}"
                                data-purchase-price="{{ $availableProduct->purchase_price }}"
                                data-selling-price="{{ $availableProduct->selling_price }}">
                            {{ $availableProduct->name }}
                            @if($availableProduct->reference)
                                — {{ $availableProduct->reference }}
                            @endif
                        </option>

                    @endforeach

                </select>

            </div>


            <div class="sm:col-span-2">

                <label class="block text-sm font-medium
                              text-gray-700 dark:text-gray-300 mb-2">
                    Quantité
                </label>

                <input type="number"
                       class="quantity-input w-full rounded-xl
                              border border-gray-300 dark:border-gray-700
                              bg-white dark:bg-gray-900
                              text-gray-900 dark:text-white
                              px-3 py-2.5
                              focus:ring-2 focus:ring-gray-500
                              focus:border-transparent"
                       min="1"
                       step="1"
                       required>

            </div>


            <div class="sm:col-span-3">

                <label class="block text-sm font-medium
                              text-gray-700 dark:text-gray-300 mb-2">
                    Prix unitaire
                </label>

                <div class="relative">

                    <input type="number"
                           class="price-input w-full rounded-xl
                                  border border-gray-300 dark:border-gray-700
                                  bg-white dark:bg-gray-900
                                  text-gray-900 dark:text-white
                                  px-3 py-2.5 pr-14"
                           min="0"
                           step="0.01"
                           required>

                    <span class="absolute right-3 top-1/2 -translate-y-1/2
                                 text-xs text-gray-500 dark:text-gray-400">
                        FCFA
                    </span>

                </div>

            </div>


            <div class="sm:col-span-2">

                <button type="button"
                        class="remove-product w-full inline-flex
                               items-center justify-center gap-2
                               px-3 py-2.5 rounded-xl
                               border border-red-200 dark:border-red-900
                               text-red-600 dark:text-red-400
                               hover:bg-red-50 dark:hover:bg-red-950/40
                               transition">

                    <svg class="w-4 h-4"
                         fill="none"
                         viewBox="0 0 24 24"
                         stroke="currentColor"
                         stroke-width="2">
                        <path stroke-linecap="round"
                              stroke-linejoin="round"
                              d="M6 7.5h12M9.75 7.5V5.25A1.5 1.5 0 0111.25 3.75h1.5a1.5 1.5 0 011.5 1.5V7.5m-7.5 0v11.25A2.25 2.25 0 009 21h6a2.25 2.25 0 002.25-2.25V7.5M10.5 11.25v6m3-6v6"/>
                    </svg>

                    Supprimer
                </button>

            </div>

        </div>


        <div class="mt-3 text-right">

            <span class="text-xs text-gray-500 dark:text-gray-400">
                Total ligne :
            </span>

            <span class="line-total ml-1 font-semibold
                         text-gray-900 dark:text-white">
                0 FCFA
            </span>

        </div>

    </div>

</template>


<script>
document.addEventListener('DOMContentLoaded', function () {

    const container = document.getElementById('products-container');
    const template = document.getElementById('product-template');
    const addButton = document.getElementById('add-product');
    const addEmptyButton = document.getElementById('add-product-empty');
    const emptyProducts = document.getElementById('empty-products');
    const grandTotal = document.getElementById('grand-total');

    let index = {{ $sale->products->count() }};


    /*
     * Formater un montant en FCFA
     */
    function formatMoney(value) {
        return new Intl.NumberFormat('fr-FR').format(value) + ' FCFA';
    }


    /*
     * Mettre à jour les noms des champs.
     *
     * Important :
     * quand une ligne est supprimée, on renumérote les lignes
     * afin que Laravel reçoive :
     *
     * items[0]
     * items[1]
     * items[2]
     */
    function reindexRows() {

        const rows = container.querySelectorAll('[data-row]');

        rows.forEach((row, rowIndex) => {

            const select = row.querySelector('.product-select');
            const quantity = row.querySelector('.quantity-input');
            const price = row.querySelector('.price-input');

            if (select) {
                select.name = `items[${rowIndex}][product_id]`;
            }

            if (quantity) {
                quantity.name = `items[${rowIndex}][quantity]`;
            }

            if (price) {
                price.name = `items[${rowIndex}][unit_price]`;
            }
        });

        index = rows.length;

        updateEmptyState();
        updateTotal();
        updateDuplicateOptions();
    }


    /*
     * Calcul du total.
     */
    function updateTotal() {

        let total = 0;

        const rows = container.querySelectorAll('[data-row]');

        rows.forEach(row => {

            const quantity = parseFloat(
                row.querySelector('.quantity-input')?.value || 0
            );

            const price = parseFloat(
                row.querySelector('.price-input')?.value || 0
            );

            const lineTotal = quantity * price;

            total += lineTotal;

            const lineTotalElement = row.querySelector('.line-total');

            if (lineTotalElement) {
                lineTotalElement.textContent = formatMoney(lineTotal);
            }
        });

        grandTotal.textContent = formatMoney(total);
    }


    /*
     * Masquer les produits déjà sélectionnés
     * dans les autres lignes.
     */
    function updateDuplicateOptions() {

        const selects = [...container.querySelectorAll('.product-select')];

        const selectedValues = selects
            .map(select => select.value)
            .filter(value => value !== '');

        selects.forEach(select => {

            [...select.options].forEach(option => {

                if (!option.value) {
                    return;
                }

                const selectedElsewhere =
                    selectedValues.includes(option.value) &&
                    option.value !== select.value;

                option.disabled = selectedElsewhere;
            });
        });
    }


    /*
     * État "aucun produit".
     */
    function updateEmptyState() {

        const hasRows =
            container.querySelectorAll('[data-row]').length > 0;

        if (hasRows) {
            emptyProducts.classList.add('hidden');
        } else {
            emptyProducts.classList.remove('hidden');
        }
    }


    /*
     * Ajouter une nouvelle ligne.
     */
    function addProductRow() {

        const fragment = template.content.cloneNode(true);

        container.appendChild(fragment);

        reindexRows();

        const rows = container.querySelectorAll('[data-row]');
        const lastRow = rows[rows.length - 1];

        if (lastRow) {
            const select = lastRow.querySelector('.product-select');

            if (select) {
                select.focus();
            }
        }
    }


    /*
     * Supprimer une ligne.
     */
    container.addEventListener('click', function (event) {

        const button = event.target.closest('.remove-product');

        if (!button) {
            return;
        }

        const row = button.closest('[data-row]');

        if (!row) {
            return;
        }

        row.remove();

        reindexRows();
    });


    /*
     * Ajouter un produit.
     */
    addButton.addEventListener('click', addProductRow);

    if (addEmptyButton) {
        addEmptyButton.addEventListener('click', addProductRow);
    }


    /*
     * Recalculer lorsque l'utilisateur modifie
     * quantité ou prix.
     */
    container.addEventListener('input', function (event) {

        if (
            event.target.classList.contains('quantity-input') ||
            event.target.classList.contains('price-input')
        ) {
            updateTotal();
        }
    });


    /*
     * Lorsqu'un produit est changé,
     * proposer son prix de vente actuel.
     */
    container.addEventListener('change', function (event) {

        if (event.target.classList.contains('product-select')) {

            const select = event.target;
            const row = select.closest('[data-row]');
            const option = select.options[select.selectedIndex];

            if (row && option) {

                const priceInput = row.querySelector('.price-input');

                if (
                    priceInput &&
                    option.dataset.sellingPrice !== undefined
                ) {
                    priceInput.value = option.dataset.sellingPrice;
                }
            }

            updateDuplicateOptions();
            updateTotal();
        }
    });


    /*
     * Initialisation.
     */
    reindexRows();
    updateTotal();

});
</script>

@endsection