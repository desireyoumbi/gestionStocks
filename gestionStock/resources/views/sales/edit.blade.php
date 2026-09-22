@extends('layouts.app')

@section('title', 'Modifier la vente')

@section('content')

<div class="max-w-4xl mx-auto px-4 sm:px-6 py-6">

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

                    <svg class="w-5 h-5"
                         fill="none"
                         viewBox="0 0 24 24"
                         stroke="currentColor"
                         stroke-width="2">

                        <path stroke-linecap="round"
                              stroke-linejoin="round"
                              d="M15.75 19.5L8.25 12l7.5-7.5"/>

                    </svg>

                </a>

                <div>

                    <h1 class="text-2xl font-bold text-gray-900 dark:text-white">
                        Modifier la vente #{{ $sale->id }}
                    </h1>

                    <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">
                        Modifiez la quantité ou le prix de vente.
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

        <div class="mb-6 rounded-2xl
                    border border-red-200 dark:border-red-900
                    bg-red-50 dark:bg-red-950/40
                    p-4">

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
    <form action="{{ route('sales.update', [
        'saleId' => $sale->id,
        'productId' => $product->id
    ]) }}"
          method="POST"
          id="sale-form">

        @csrf
        @method('PUT')


        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

            {{-- Produit --}}
            <div class="lg:col-span-2">

                <div class="bg-white dark:bg-gray-950
                            border border-gray-200 dark:border-gray-800
                            rounded-2xl shadow-sm overflow-hidden">

                    {{-- Header --}}
                    <div class="px-5 py-4
                                border-b border-gray-200 dark:border-gray-800">

                        <h2 class="font-semibold text-gray-900 dark:text-white">
                            Produit vendu
                        </h2>

                        <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">
                            Les informations de cette vente sont affichées ci-dessous.
                        </p>

                    </div>


                    {{-- Produit --}}
                    <div class="p-5">

                        <div class="grid grid-cols-1 sm:grid-cols-3 gap-5">

                            {{-- Nom du produit --}}
                            <div class="sm:col-span-3">

                                <label class="block text-sm font-medium
                                              text-gray-700 dark:text-gray-300 mb-2">
                                    Produit
                                </label>

                                <div class="w-full rounded-xl
                                            border border-gray-200 dark:border-gray-700
                                            bg-gray-50 dark:bg-gray-900
                                            text-gray-900 dark:text-white
                                            px-4 py-3">

                                    <div class="font-semibold">
                                        {{ $product->name }}
                                    </div>

                                    @if($product->reference)

                                        <div class="text-xs text-gray-500 dark:text-gray-400 mt-1">
                                            Référence : {{ $product->reference }}
                                        </div>

                                    @endif

                                </div>

                            </div>


                            {{-- Quantité --}}
                            <div>

                                <label for="quantity"
                                       class="block text-sm font-medium
                                              text-gray-700 dark:text-gray-300 mb-2">

                                    Quantité

                                </label>

                                <input type="number"
                                       name="quantity"
                                       id="quantity"
                                       value="{{ old('quantity', $saleItem->pivot->qty) }}"
                                       min="1"
                                       step="1"
                                       required
                                       class="w-full rounded-xl
                                              border border-gray-300 dark:border-gray-700
                                              bg-white dark:bg-gray-900
                                              text-gray-900 dark:text-white
                                              px-3 py-2.5
                                              focus:ring-2 focus:ring-gray-500
                                              focus:border-transparent">

                            </div>


                            {{-- Prix --}}
                            <div>

                                <label for="unit_price"
                                       class="block text-sm font-medium
                                              text-gray-700 dark:text-gray-300 mb-2">

                                    Prix unitaire

                                </label>

                                <div class="relative">

                                    <input type="number"
                                           name="unit_price"
                                           id="unit_price"
                                           value="{{ old('unit_price', $saleItem->pivot->unit_price) }}"
                                           min="0"
                                           step="0.01"
                                           required
                                           class="w-full rounded-xl
                                                  border border-gray-300 dark:border-gray-700
                                                  bg-white dark:bg-gray-900
                                                  text-gray-900 dark:text-white
                                                  px-3 py-2.5 pr-14
                                                  focus:ring-2 focus:ring-gray-500
                                                  focus:border-transparent">

                                    <span class="absolute right-3 top-1/2 -translate-y-1/2
                                                 text-xs text-gray-500 dark:text-gray-400">
                                        FCFA
                                    </span>

                                </div>

                            </div>


                            {{-- Total --}}
                            <div>

                                <label class="block text-sm font-medium
                                              text-gray-700 dark:text-gray-300 mb-2">

                                    Total

                                </label>

                                <div class="w-full rounded-xl
                                            border border-gray-200 dark:border-gray-700
                                            bg-gray-50 dark:bg-gray-900
                                            text-gray-900 dark:text-white
                                            px-3 py-2.5
                                            font-semibold">

                                    <span id="line-total">
                                        0 FCFA
                                    </span>

                                </div>

                            </div>

                        </div>

                    </div>

                </div>

            </div>


            {{-- Résumé et actions --}}
            <div class="lg:col-span-1">

                <div class="lg:sticky lg:top-6 space-y-5">

                    {{-- Nouveau total --}}
                    <div class="bg-gray-900 dark:bg-gray-800
                                rounded-2xl p-5
                                text-white shadow-sm">

                        <p class="text-sm text-gray-300">
                            Nouveau total
                        </p>

                        <p id="grand-total"
                           class="text-3xl font-bold mt-2">
                            0 FCFA
                        </p>

                        <p class="text-xs text-gray-400 mt-2">
                            Le montant de la vente sera également mis à jour dans la caisse.
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
                                  rows="3"
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

                            Enregistrer

                        </button>


                        <a href="{{ route('sales.index') }}"
                           class="mt-3 w-full inline-flex items-center justify-center
                                  px-4 py-3 rounded-xl
                                  border border-gray-300 dark:border-gray-700
                                  text-gray-700 dark:text-gray-300
                                  font-semibold
                                  hover:bg-gray-100 dark:hover:bg-gray-800
                                  transition">

                            Retour

                        </a>

                    </div>

                </div>

            </div>

        </div>

    </form>

</div>


<script>
document.addEventListener('DOMContentLoaded', function () {

    const quantityInput = document.getElementById('quantity');
    const priceInput = document.getElementById('unit_price');
    const lineTotal = document.getElementById('line-total');
    const grandTotal = document.getElementById('grand-total');


    function formatMoney(value) {
        return new Intl.NumberFormat('fr-FR').format(value) + ' FCFA';
    }


    function updateTotal() {

        const quantity = parseFloat(quantityInput.value) || 0;
        const price = parseFloat(priceInput.value) || 0;

        const total = quantity * price;

        lineTotal.textContent = formatMoney(total);
        grandTotal.textContent = formatMoney(total);
    }


    quantityInput.addEventListener('input', updateTotal);
    priceInput.addEventListener('input', updateTotal);

    updateTotal();

});
</script>

@endsection

