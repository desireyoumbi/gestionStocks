@extends('layouts.app')

@section('page-title', 'Nouvelle commande')

@section('content')

<div class="max-w-3xl mx-auto px-4 py-8">

    <!-- En-tête avec retour -->
    <div class="flex items-center gap-3 mb-6">
        <a href="{{ route('purchase_orders.index') }}"
           class="w-10 h-10 bg-white border border-slate-200 rounded-xl flex items-center justify-center text-slate-400 hover:text-slate-700 hover:bg-slate-50 transition shadow-sm">

            <svg class="w-5 h-5"
                 fill="none"
                 viewBox="0 0 24 24"
                 stroke="currentColor"
                 stroke-width="2">

                <path stroke-linecap="round"
                      stroke-linejoin="round"
                      d="M10 19l-7-7m0 0l7-7m-7 7h18" />

            </svg>

        </a>

        <div>
            <h1 class="text-2xl font-bold text-slate-900">
                Nouvelle commande fournisseur
            </h1>

            <p class="text-xs text-slate-400 mt-0.5">
                Renseignez les informations de la commande et ajoutez les produits
            </p>
        </div>
    </div>


    <!-- Gestion des erreurs -->
    @if ($errors->any())

        <div class="bg-red-50 border border-red-200 text-red-700 text-sm px-4 py-3 rounded-2xl mb-6 shadow-md">

            <ul class="list-disc list-inside space-y-0.5">

                @foreach ($errors->all() as $error)

                    <li>
                        {{ $error }}
                    </li>

                @endforeach

            </ul>

        </div>

    @endif


    <!-- Formulaire -->
    <div class="bg-white border border-slate-100 rounded-3xl shadow-2xl p-8">

        <form method="POST"
              action="{{ route('purchase_orders.store') }}"
              id="orderForm"
              class="space-y-6">

            @csrf


            <!-- Fournisseur -->
            <div>

                <label for="supplier_id"
                       class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-2">

                    Fournisseur *

                </label>


                <select name="supplier_id"
                        id="supplier_id"
                        required
                        class="w-full bg-slate-50 border border-slate-200 rounded-xl px-4 py-3 text-sm focus:outline-none focus:ring-2 focus:ring-amber-500/20 focus:border-amber-500 transition">

                    <option value="">
                        Sélectionner un fournisseur
                    </option>


                    @foreach($suppliers as $supplier)

                        <option value="{{ $supplier->id }}"
                            {{ old('supplier_id') == $supplier->id ? 'selected' : '' }}>

                            {{ $supplier->name }}

                        </option>

                    @endforeach

                </select>


                @if($suppliers->isEmpty())

                    <p class="text-xs text-amber-600 mt-1.5">

                        Aucun fournisseur enregistré.

                        <a href="{{ route('suppliers.create') }}"
                           class="underline font-semibold">

                            En créer un

                        </a>

                    </p>

                @endif


                <!-- Message dynamique fournisseur -->
                <p id="supplierInfo"
                   class="text-xs text-slate-400 mt-2 hidden">
                </p>

            </div>


            <!-- Lignes de produits -->
            <div>

                <div class="flex items-center justify-between mb-3">

                    <label class="block text-xs font-bold text-slate-500 uppercase tracking-wider">

                        Produits commandés

                    </label>


                    <button type="button"
                            id="addLineBtn"
                            disabled
                            class="text-xs font-bold text-amber-600 hover:text-amber-700 flex items-center gap-1 bg-amber-50 px-3 py-1.5 rounded-xl transition disabled:opacity-40 disabled:cursor-not-allowed">

                        <svg class="w-3.5 h-3.5"
                             fill="none"
                             viewBox="0 0 24 24"
                             stroke="currentColor"
                             stroke-width="2">

                            <path stroke-linecap="round"
                                  stroke-linejoin="round"
                                  d="M12 4v16m8-8H4" />

                        </svg>

                        Ajouter une ligne

                    </button>

                </div>


                <!-- Message avant sélection du fournisseur -->
                <div id="selectSupplierMsg"
                     class="bg-slate-50 border border-slate-200 rounded-2xl px-4 py-4 text-center">

                    <p class="text-sm font-medium text-slate-500">
                        Sélectionnez d'abord un fournisseur.
                    </p>

                    <p class="text-xs text-slate-400 mt-1">
                        Les produits proposés dépendront du fournisseur choisi.
                    </p>

                </div>


                <!-- Lignes -->
                <div id="linesContainer"
                     class="space-y-3">
                </div>


                <!-- Aucun produit -->
                <p id="noProductsMsg"
                   class="text-xs text-amber-600 mt-3 hidden">

                    Ce fournisseur ne possède aucun produit associé.

                </p>

            </div>


            <!-- Pied du formulaire -->
            <div class="flex flex-col sm:flex-row items-center justify-between pt-6 border-t border-slate-100 gap-4">


                <!-- Total -->
                <div class="text-sm font-medium text-slate-600 bg-slate-50 px-4 py-2.5 rounded-xl border border-slate-200 w-full sm:w-auto text-center sm:text-left">

                    Total estimé :

                    <span id="totalDisplay"
                          class="font-bold text-slate-900">

                        0 FCFA

                    </span>

                </div>


                <div class="flex items-center gap-3 w-full sm:w-auto justify-end">

                    <a href="{{ route('purchase_orders.index') }}"
                       class="bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold px-5 py-3 rounded-xl transition text-sm">

                        Annuler

                    </a>


                    <button type="submit"
                            id="submitBtn"
                            disabled
                            class="bg-amber-500 hover:bg-amber-600 text-slate-900 font-bold px-6 py-3 rounded-xl transition shadow-md hover:shadow-lg text-sm disabled:opacity-40 disabled:cursor-not-allowed">

                        Créer la commande

                    </button>

                </div>

            </div>

        </form>

    </div>

</div>


<script>

    /*
    |--------------------------------------------------------------------------
    | Données des fournisseurs
    |--------------------------------------------------------------------------
    |
    | Chaque fournisseur contient maintenant uniquement les produits
    | qui lui sont associés via la table product_supplier.
    |
    */

    const suppliers = @json(
        $suppliers->map(function ($supplier) {

            return [
                'id' => $supplier->id,
                'name' => $supplier->name,

                'products' => $supplier->products->map(function ($product) {

                    return [
                        'id' => $product->id,
                        'name' => $product->name,
                        'purchase_price' => $product->purchase_price,
                    ];

                })->values(),

            ];

        })->values()
    );


    const supplierSelect =
        document.getElementById('supplier_id');

    const linesContainer =
        document.getElementById('linesContainer');

    const addLineBtn =
        document.getElementById('addLineBtn');

    const noProductsMsg =
        document.getElementById('noProductsMsg');

    const selectSupplierMsg =
        document.getElementById('selectSupplierMsg');

    const supplierInfo =
        document.getElementById('supplierInfo');

    const submitBtn =
        document.getElementById('submitBtn');


    let lineIndex = 0;


    /*
    |--------------------------------------------------------------------------
    | Fournisseur actuellement sélectionné
    |--------------------------------------------------------------------------
    */

    function getSelectedSupplier() {

        const supplierId =
            supplierSelect.value;

        if (!supplierId) {
            return null;
        }

        return suppliers.find(
            supplier =>
                String(supplier.id) === String(supplierId)
        ) || null;
    }


    /*
    |--------------------------------------------------------------------------
    | Produits disponibles
    |--------------------------------------------------------------------------
    */

    function getAvailableProducts() {

        const supplier =
            getSelectedSupplier();

        if (!supplier) {
            return [];
        }

        return supplier.products || [];
    }


    /*
    |--------------------------------------------------------------------------
    | Construire les options produits
    |--------------------------------------------------------------------------
    */

    function buildProductOptions(selectedId = '') {

        const products =
            getAvailableProducts();

        if (products.length === 0) {

            return `
                <option value="">
                    Aucun produit disponible
                </option>
            `;

        }


        return `
            <option value="">
                Choisir un produit
            </option>

            ${
                products.map(product => {

                    const selected =
                        String(product.id) === String(selectedId)
                            ? 'selected'
                            : '';

                    return `
                        <option
                            value="${product.id}"
                            data-price="${product.purchase_price ?? ''}"
                            ${selected}
                        >
                            ${escapeHtml(product.name)}
                        </option>
                    `;

                }).join('')
            }
        `;
    }


    /*
    |--------------------------------------------------------------------------
    | Protection HTML
    |--------------------------------------------------------------------------
    */

    function escapeHtml(value) {

        const div =
            document.createElement('div');

        div.textContent =
            value ?? '';

        return div.innerHTML;
    }


    /*
    |--------------------------------------------------------------------------
    | Ajouter une ligne
    |--------------------------------------------------------------------------
    */

    function addLine(
        selectedId = '',
        qty = 1,
        unitPrice = ''
    ) {

        const products =
            getAvailableProducts();


        /*
         * Aucun fournisseur sélectionné.
         */
        if (!getSelectedSupplier()) {

            return;

        }


        /*
         * Le fournisseur n'a aucun produit.
         */
        if (products.length === 0) {

            noProductsMsg.classList.remove('hidden');

            return;

        }


        const idx =
            lineIndex++;


        const row =
            document.createElement('div');


        row.className =
            'flex flex-col sm:flex-row sm:items-center gap-3 bg-slate-50/70 p-3 rounded-2xl border border-slate-200/80';


        row.innerHTML = `

            <!-- Produit -->

            <select
                name="products[${idx}][id]"
                required
                class="flex-1 w-full px-3.5 py-2.5 bg-white border border-slate-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-amber-500/20 focus:border-amber-500 product-select">

                ${buildProductOptions(selectedId)}

            </select>


            <!-- Quantité -->

            <input
                type="number"
                name="products[${idx}][qty]"
                min="1"
                value="${qty}"
                required
                placeholder="Qté"
                class="w-full sm:w-20 px-3 py-2.5 bg-white border border-slate-200 rounded-xl text-sm text-center focus:outline-none focus:ring-2 focus:ring-amber-500/20 focus:border-amber-500 qty-input">


            <!-- Prix -->

            <input
                type="number"
                name="products[${idx}][unit_price]"
                min="0"
                step="0.01"
                value="${unitPrice}"
                required
                placeholder="Prix unit."
                class="w-full sm:w-32 px-3.5 py-2.5 bg-white border border-slate-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-amber-500/20 focus:border-amber-500 price-input">


            <!-- Supprimer -->

            <button
                type="button"
                class="text-red-400 hover:text-red-600 remove-line shrink-0 self-end sm:self-auto p-1 hover:bg-red-50 rounded-lg transition"
                title="Retirer">

                <svg
                    class="w-5 h-5"
                    fill="none"
                    viewBox="0 0 24 24"
                    stroke="currentColor"
                    stroke-width="2">

                    <path
                        stroke-linecap="round"
                        stroke-linejoin="round"
                        d="M6 18L18 6M6 6l12 12" />

                </svg>

            </button>

        `;


        linesContainer.appendChild(row);


        /*
        |--------------------------------------------------------------------------
        | Références des champs
        |--------------------------------------------------------------------------
        */

        const select =
            row.querySelector('.product-select');

        const priceInput =
            row.querySelector('.price-input');

        const qtyInput =
            row.querySelector('.qty-input');


        /*
        |--------------------------------------------------------------------------
        | Changement du produit
        |--------------------------------------------------------------------------
        */

        select.addEventListener(
            'change',
            function () {

                const opt =
                    select.options[
                        select.selectedIndex
                    ];


                const price =
                    opt.getAttribute('data-price');


                /*
                 * On remplit automatiquement
                 * le prix d'achat du produit.
                 */
                if (price !== null && price !== '') {

                    priceInput.value =
                        price;

                }


                /*
                 * Vérification des doublons.
                 */
                preventDuplicateProducts(select);


                updateTotal();

            }
        );


        /*
        |--------------------------------------------------------------------------
        | Quantité
        |--------------------------------------------------------------------------
        */

        qtyInput.addEventListener(
            'input',
            updateTotal
        );


        /*
        |--------------------------------------------------------------------------
        | Prix
        |--------------------------------------------------------------------------
        */

        priceInput.addEventListener(
            'input',
            updateTotal
        );


        /*
        |--------------------------------------------------------------------------
        | Suppression de la ligne
        |--------------------------------------------------------------------------
        */

        row.querySelector(
            '.remove-line'
        ).addEventListener(
            'click',
            function () {

                row.remove();

                updateTotal();

                updateSubmitState();

            }
        );


        updateTotal();

        updateSubmitState();
    }


    /*
    |--------------------------------------------------------------------------
    | Empêcher les doublons
    |--------------------------------------------------------------------------
    */

    function preventDuplicateProducts(changedSelect) {

        const selectedValue =
            changedSelect.value;


        if (!selectedValue) {
            return;
        }


        const selects =
            linesContainer.querySelectorAll(
                '.product-select'
            );


        let count = 0;


        selects.forEach(select => {

            if (
                String(select.value)
                === String(selectedValue)
            ) {

                count++;

            }

        });


        /*
         * Si le produit est déjà présent,
         * on réinitialise la sélection.
         */
        if (count > 1) {

            alert(
                'Ce produit est déjà présent dans la commande.'
            );

            changedSelect.value = '';

            const row =
                changedSelect.closest('div.flex');

            if (row) {

                const priceInput =
                    row.querySelector('.price-input');

                if (priceInput) {
                    priceInput.value = '';
                }

            }

        }

    }


    /*
    |--------------------------------------------------------------------------
    | Changement de fournisseur
    |--------------------------------------------------------------------------
    */

    function handleSupplierChange() {

        const supplier =
            getSelectedSupplier();


        /*
         * Réinitialisation des lignes.
         */
        linesContainer.innerHTML = '';


        lineIndex = 0;


        /*
         * Aucun fournisseur.
         */
        if (!supplier) {

            addLineBtn.disabled = true;

            submitBtn.disabled = true;

            selectSupplierMsg.classList.remove(
                'hidden'
            );

            noProductsMsg.classList.add(
                'hidden'
            );

            supplierInfo.classList.add(
                'hidden'
            );

            updateTotal();

            return;
        }


        /*
         * Fournisseur sélectionné.
         */
        selectSupplierMsg.classList.add(
            'hidden'
        );


        const products =
            supplier.products || [];


        /*
         * Affichage de l'information fournisseur.
         */
        supplierInfo.textContent =
            `${products.length} produit(s) disponible(s) chez ${supplier.name}.`;

        supplierInfo.classList.remove(
            'hidden'
        );


        /*
         * Fournisseur sans produit.
         */
        if (products.length === 0) {

            addLineBtn.disabled = true;

            submitBtn.disabled = true;

            noProductsMsg.classList.remove(
                'hidden'
            );

            updateTotal();

            return;
        }


        /*
         * Fournisseur avec produits.
         */
        noProductsMsg.classList.add(
            'hidden'
        );


        addLineBtn.disabled = false;


        /*
         * Ajouter automatiquement
         * la première ligne.
         */
        addLine();


        updateSubmitState();

        updateTotal();
    }


    /*
    |--------------------------------------------------------------------------
    | Vérification du bouton de soumission
    |--------------------------------------------------------------------------
    */

    function updateSubmitState() {

        const supplier =
            getSelectedSupplier();


        if (!supplier) {

            submitBtn.disabled = true;

            return;

        }


        const rows =
            linesContainer.querySelectorAll(
                '.product-select'
            );


        if (rows.length === 0) {

            submitBtn.disabled = true;

            return;

        }


        let valid =
            true;


        rows.forEach(select => {

            const row =
                select.closest('div.flex');

            if (!row) {
                return;
            }


            const qty =
                row.querySelector('.qty-input');

            const price =
                row.querySelector('.price-input');


            if (
                !select.value ||
                !qty ||
                !qty.value ||
                Number(qty.value) < 1 ||
                !price ||
                price.value === '' ||
                Number(price.value) < 0
            ) {

                valid = false;

            }

        });


        submitBtn.disabled =
            !valid;
    }


    /*
    |--------------------------------------------------------------------------
    | Calcul du total
    |--------------------------------------------------------------------------
    */

    function updateTotal() {

        let total =
            0;


        linesContainer
            .querySelectorAll('.product-select')
            .forEach(select => {

                const row =
                    select.closest('div.flex');

                if (!row) {
                    return;
                }


                const qty =
                    parseFloat(
                        row.querySelector(
                            '.qty-input'
                        )?.value || 0
                    );


                const price =
                    parseFloat(
                        row.querySelector(
                            '.price-input'
                        )?.value || 0
                    );


                total +=
                    qty * price;

            });


        document.getElementById(
            'totalDisplay'
        ).textContent =
            total.toLocaleString('fr-FR')
            + ' FCFA';
    }


    /*
    |--------------------------------------------------------------------------
    | Bouton Ajouter une ligne
    |--------------------------------------------------------------------------
    */

    addLineBtn.addEventListener(
        'click',
        function () {

            addLine();

        }
    );


    /*
    |--------------------------------------------------------------------------
    | Changement de fournisseur
    |--------------------------------------------------------------------------
    */

    supplierSelect.addEventListener(
        'change',
        function () {

            handleSupplierChange();

        }
    );


    /*
    |--------------------------------------------------------------------------
    | Surveillance du formulaire
    |--------------------------------------------------------------------------
    */

    document.getElementById(
        'orderForm'
    ).addEventListener(
        'input',
        function () {

            updateSubmitState();

        }
    );


    /*
    |--------------------------------------------------------------------------
    | Gestion de old('supplier_id')
    |--------------------------------------------------------------------------
    */

    document.addEventListener(
        'DOMContentLoaded',
        function () {

            /*
             * Si Laravel a conservé un ancien fournisseur
             * après une erreur de validation,
             * on recharge automatiquement ses produits.
             */
            if (supplierSelect.value) {

                handleSupplierChange();

            }

        }
    );

</script>

@endsection