@extends('layouts.app')

@section('content')
<div class="max-w-5xl mx-auto px-4 sm:px-6 py-6">

    {{-- En-tête --}}
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-6">

        <div>
            <h1 class="text-2xl font-bold text-gray-900 dark:text-white">
                Modifier la vente #{{ $sale->id }}
            </h1>

            <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">
                Modifiez les quantités et les prix des produits de cette vente.
            </p>
        </div>

        <a href="{{ route('sales.index') }}"
           class="inline-flex items-center justify-center gap-2 px-4 py-2.5 rounded-xl
                  bg-gray-100 dark:bg-gray-800
                  text-gray-700 dark:text-gray-200
                  hover:bg-gray-200 dark:hover:bg-gray-700
                  transition">

            <svg class="w-4 h-4"
                 fill="none"
                 viewBox="0 0 24 24"
                 stroke="currentColor"
                 stroke-width="2">
                <path stroke-linecap="round"
                      stroke-linejoin="round"
                      d="M10.5 19.5L3 12m0 0l7.5-7.5M3 12h18"/>
            </svg>

            Retour
        </a>
    </div>


    {{-- Messages d'erreur --}}
    @if ($errors->any())
        <div class="mb-5 rounded-xl border border-red-200 dark:border-red-900
                    bg-red-50 dark:bg-red-950/40
                    p-4 text-red-700 dark:text-red-300">

            <div class="font-semibold mb-2">
                Impossible d'enregistrer la modification :
            </div>

            <ul class="list-disc list-inside text-sm space-y-1">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif


    {{-- Formulaire --}}
    <form action="{{ route('sales.update', $sale->id) }}"
          method="POST"
          id="sale-edit-form">

        @csrf
        @method('PUT')


        <div class="bg-white dark:bg-gray-950
                    rounded-2xl shadow-sm
                    border border-gray-200 dark:border-gray-800
                    overflow-hidden">

            {{-- Informations vente --}}
            <div class="px-5 py-4 border-b border-gray-200 dark:border-gray-800">
                <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-2">

                    <div>
                        <p class="text-sm text-gray-500 dark:text-gray-400">
                            Vente
                        </p>

                        <p class="font-semibold text-gray-900 dark:text-white">
                            #{{ $sale->id }}
                        </p>
                    </div>

                    <div class="text-sm text-gray-500 dark:text-gray-400">
                        {{ $sale->products->count() }}
                        {{ $sale->products->count() > 1 ? 'produits' : 'produit' }}
                    </div>

                </div>
            </div>


            {{-- Produits --}}
            <div class="divide-y divide-gray-200 dark:divide-gray-800">

                @foreach($sale->products as $product)

                    @php
                        $quantity = old(
                            "items.{$product->id}.quantity",
                            $product->pivot->qty
                        );

                        $unitPrice = old(
                            "items.{$product->id}.unit_price",
                            $product->pivot->unit_price
                        );

                        $lineTotal = (float) $quantity * (float) $unitPrice;
                    @endphp

                    <div class="p-5 sale-item"
                         data-product-id="{{ $product->id }}">

                        {{-- Produit --}}
                        <div class="flex flex-col lg:flex-row lg:items-center gap-5">

                            {{-- Informations produit --}}
                            <div class="flex-1 min-w-0">

                                <div class="flex items-start gap-3">

                                    <div class="w-11 h-11 rounded-xl
                                                bg-gray-100 dark:bg-gray-800
                                                flex items-center justify-center
                                                shrink-0">

                                        @if(!empty($product->image))
                                            <img src="{{ asset('storage/' . $product->image) }}"
                                                 alt="{{ $product->name }}"
                                                 class="w-11 h-11 rounded-xl object-cover">
                                        @else
                                            <svg class="w-5 h-5 text-gray-400"
                                                 fill="none"
                                                 viewBox="0 0 24 24"
                                                 stroke="currentColor"
                                                 stroke-width="2">
                                                <path stroke-linecap="round"
                                                      stroke-linejoin="round"
                                                      d="M20 7l-8-4-8 4m16 0v10l-8 4m8-14l-8 4m0 0L4 7m8 4v10"/>
                                            </svg>
                                        @endif
                                    </div>

                                    <div class="min-w-0">

                                        <h2 class="font-semibold text-gray-900 dark:text-white truncate">
                                            {{ $product->name }}
                                        </h2>

                                        @if($product->reference)
                                            <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">
                                                Réf. : {{ $product->reference }}
                                            </p>
                                        @endif

                                        <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">
                                            Prix d'achat :
                                            {{ number_format($product->purchase_price, 0, ',', ' ') }}
                                            FCFA
                                        </p>

                                    </div>
                                </div>

                            </div>


                            {{-- Quantité --}}
                            <div class="w-full lg:w-32">

                                <label for="quantity_{{ $product->id }}"
                                       class="block text-xs font-medium
                                              text-gray-500 dark:text-gray-400 mb-1.5">
                                    Quantité
                                </label>

                                <input
                                    type="number"
                                    id="quantity_{{ $product->id }}"
                                    name="items[{{ $product->id }}][quantity]"
                                    value="{{ $quantity }}"
                                    min="1"
                                    max="2147483647"
                                    required
                                    class="sale-quantity w-full rounded-xl
                                           border border-gray-300 dark:border-gray-700
                                           bg-white dark:bg-gray-900
                                           text-gray-900 dark:text-white
                                           px-3 py-2.5
                                           focus:ring-2 focus:ring-indigo-500
                                           focus:border-indigo-500"
                                >

                            </div>


                            {{-- Prix unitaire --}}
                            <div class="w-full lg:w-44">

                                <label for="unit_price_{{ $product->id }}"
                                       class="block text-xs font-medium
                                              text-gray-500 dark:text-gray-400 mb-1.5">
                                    Prix unitaire
                                </label>

                                <div class="relative">

                                    <input
                                        type="number"
                                        id="unit_price_{{ $product->id }}"
                                        name="items[{{ $product->id }}][unit_price]"
                                        value="{{ $unitPrice }}"
                                        min="0"
                                        step="0.01"
                                        required
                                        class="sale-price w-full rounded-xl
                                               border border-gray-300 dark:border-gray-700
                                               bg-white dark:bg-gray-900
                                               text-gray-900 dark:text-white
                                               px-3 py-2.5 pr-16
                                               focus:ring-2 focus:ring-indigo-500
                                               focus:border-indigo-500"
                                    >

                                    <span class="absolute right-3 top-1/2 -translate-y-1/2
                                                 text-xs text-gray-400">
                                        FCFA
                                    </span>

                                </div>

                            </div>


                            {{-- Total ligne --}}
                            <div class="w-full lg:w-44 lg:text-right">

                                <p class="text-xs font-medium text-gray-500 dark:text-gray-400 mb-1.5">
                                    Total
                                </p>

                                <p class="line-total text-lg font-bold text-gray-900 dark:text-white">
                                    {{ number_format($lineTotal, 0, ',', ' ') }} FCFA
                                </p>

                            </div>

                        </div>

                    </div>

                @endforeach

            </div>


            {{-- Total général --}}
            <div class="px-5 py-5
                        bg-gray-50 dark:bg-gray-900/60
                        border-t border-gray-200 dark:border-gray-800">

                <div class="flex items-center justify-between gap-4">

                    <div>
                        <p class="text-sm text-gray-500 dark:text-gray-400">
                            Total de la vente
                        </p>

                        <p class="text-xs text-gray-400 dark:text-gray-500 mt-1">
                            Le total sera recalculé automatiquement.
                        </p>
                    </div>

                    <p id="grand-total"
                       class="text-2xl font-bold text-gray-900 dark:text-white whitespace-nowrap">
                        0 FCFA
                    </p>

                </div>

            </div>


            {{-- Actions --}}
            <div class="px-5 py-4
                        border-t border-gray-200 dark:border-gray-800
                        flex flex-col-reverse sm:flex-row
                        sm:justify-end gap-3">

                <a href="{{ route('sales.index') }}"
                   class="inline-flex items-center justify-center gap-2
                          px-5 py-2.5 rounded-xl
                          bg-gray-100 dark:bg-gray-800
                          text-gray-700 dark:text-gray-200
                          hover:bg-gray-200 dark:hover:bg-gray-700
                          transition">

                    Annuler
                </a>


                <button type="submit"
                        class="inline-flex items-center justify-center gap-2
                               px-5 py-2.5 rounded-xl
                               bg-indigo-600 hover:bg-indigo-700
                               text-white font-medium
                               transition">

                    <svg class="w-4 h-4"
                         fill="none"
                         viewBox="0 0 24 24"
                         stroke="currentColor"
                         stroke-width="2">
                        <path stroke-linecap="round"
                              stroke-linejoin="round"
                              d="M5 13l4 4L19 7"/>
                    </svg>

                    Enregistrer les modifications
                </button>

            </div>

        </div>

    </form>

</div>


<script>
document.addEventListener('DOMContentLoaded', function () {

    const items = document.querySelectorAll('.sale-item');
    const grandTotal = document.getElementById('grand-total');

    function formatNumber(number) {
        return new Intl.NumberFormat('fr-FR').format(number);
    }

    function updateTotals() {

        let total = 0;

        items.forEach(function (item) {

            const quantityInput = item.querySelector('.sale-quantity');
            const priceInput = item.querySelector('.sale-price');
            const lineTotalElement = item.querySelector('.line-total');

            const quantity = parseFloat(quantityInput.value) || 0;
            const price = parseFloat(priceInput.value) || 0;

            const lineTotal = quantity * price;

            total += lineTotal;

            lineTotalElement.textContent =
                formatNumber(lineTotal) + ' FCFA';
        });

        grandTotal.textContent =
            formatNumber(total) + ' FCFA';
    }


    items.forEach(function (item) {

        const quantityInput = item.querySelector('.sale-quantity');
        const priceInput = item.querySelector('.sale-price');

        quantityInput.addEventListener('input', updateTotals);
        priceInput.addEventListener('input', updateTotals);

    });


    updateTotals();

});
</script>
@endsection