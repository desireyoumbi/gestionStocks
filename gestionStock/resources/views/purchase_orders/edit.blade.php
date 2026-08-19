@extends('layouts.app')

@section('page-title', 'Modifier la commande')

@section('content')

<div class="max-w-3xl mx-auto px-4 py-8">

    <!-- En-tête avec retour -->
    <div class="flex items-center gap-3 mb-6">
        <a href="{{ route('purchase_orders.show', $purchaseOrder) }}" class="w-10 h-10 bg-white border border-slate-200 rounded-xl flex items-center justify-center text-slate-400 hover:text-slate-700 hover:bg-slate-50 transition shadow-sm">
            <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
            </svg>
        </a>
        <div>
            <h1 class="text-2xl font-bold text-slate-900">Modifier la commande #{{ $purchaseOrder->id }}</h1>
            <p class="text-xs text-slate-400 mt-0.5">Mettez à jour les informations et les produits de cette commande</p>
        </div>
    </div>

    <!-- Gestion des erreurs -->
    @if ($errors->any())
        <div class="bg-red-50 border border-red-200 text-red-700 text-sm px-4 py-3 rounded-2xl mb-6 shadow-md">
            <ul class="list-disc list-inside space-y-0.5">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <!-- Formulaire (Centré avec ombre forte shadow-2xl) -->
    <div class="bg-white border border-slate-100 rounded-3xl shadow-2xl p-8">
        <form method="POST" action="{{ route('purchase_orders.update', $purchaseOrder) }}" id="orderForm" class="space-y-6">
            @csrf
            @method('PUT')

            <!-- Fournisseur -->
            <div>
                <label for="supplier_id" class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-2">Fournisseur *</label>
                <select name="supplier_id" id="supplier_id" required
                    class="w-full bg-slate-50 border border-slate-200 rounded-xl px-4 py-3 text-sm focus:outline-none focus:ring-2 focus:ring-amber-500/20 focus:border-amber-500 transition">
                    @foreach($suppliers as $supplier)
                        <option value="{{ $supplier->id }}" {{ $purchaseOrder->supplier_id == $supplier->id ? 'selected' : '' }}>
                            {{ $supplier->name }}
                        </option>
                    @endforeach
                </select>
            </div>

            <!-- Produits commandés -->
            <div>
                <div class="flex items-center justify-between mb-3">
                    <label class="block text-xs font-bold text-slate-500 uppercase tracking-wider">Produits commandés</label>
                    <button type="button" id="addLineBtn" class="text-xs font-bold text-amber-600 hover:text-amber-700 flex items-center gap-1 bg-amber-50 px-3 py-1.5 rounded-xl transition">
                        <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4" />
                        </svg>
                        Ajouter une ligne
                    </button>
                </div>

                <div id="linesContainer" class="space-y-3"></div>
            </div>

            <!-- Pied du formulaire -->
            <div class="flex flex-col sm:flex-row items-center justify-between pt-6 border-t border-slate-100 gap-4">
                <div class="text-sm font-medium text-slate-600 bg-slate-50 px-4 py-2.5 rounded-xl border border-slate-200 w-full sm:w-auto text-center sm:text-left">
                    Total estimé : <span id="totalDisplay" class="font-bold text-slate-900">0 FCFA</span>
                </div>
                <div class="flex items-center gap-3 w-full sm:w-auto justify-end">
                    <a href="{{ route('purchase_orders.show', $purchaseOrder) }}" class="bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold px-5 py-3 rounded-xl transition text-sm">
                        Annuler
                    </a>
                    <button type="submit" class="bg-amber-500 hover:bg-amber-600 text-slate-900 font-bold px-6 py-3 rounded-xl transition shadow-md hover:shadow-lg text-sm">
                        Enregistrer les modifications
                    </button>
                </div>
            </div>
        </form>
    </div>

</div>

<script>
    const products = @json($products->map(fn($p) => ['id' => $p->id, 'name' => $p->name, 'purchase_price' => $p->purchase_price]));
    const existingLines = @json($purchaseOrder->products->map(fn($p) => ['id' => $p->id, 'qty' => $p->pivot->qty, 'unit_price' => $p->pivot->unit_price]));
    const linesContainer = document.getElementById('linesContainer');
    let lineIndex = 0;

    function buildProductOptions(selectedId = '') {
        return products.map(p =>
            `<option value="${p.id}" data-price="${p.purchase_price}" ${String(p.id) === String(selectedId) ? 'selected' : ''}>${p.name}</option>`
        ).join('');
    }

    function addLine(selectedId = '', qty = 1, unitPrice = '') {
        const idx = lineIndex++;
        const row = document.createElement('div');
        row.className = 'flex items-center gap-3 bg-slate-50/70 p-3 rounded-2xl border border-slate-200/80';
        row.innerHTML = `
            <select name="products[${idx}][id]" required
                class="flex-1 px-3.5 py-2.5 bg-white border border-slate-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-amber-500/20 focus:border-amber-500 product-select">
                <option value="">Choisir un produit</option>
                ${buildProductOptions(selectedId)}
            </select>
            <input type="number" name="products[${idx}][qty]" min="1" value="${qty}" required placeholder="Qté"
                class="w-20 px-3 py-2.5 bg-white border border-slate-200 rounded-xl text-sm text-center focus:outline-none focus:ring-2 focus:ring-amber-500/20 focus:border-amber-500 qty-input">
            <input type="number" name="products[${idx}][unit_price]" min="0" step="0.01" value="${unitPrice}" required placeholder="Prix unit."
                class="w-32 px-3.5 py-2.5 bg-white border border-slate-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-amber-500/20 focus:border-amber-500 price-input">
            <button type="button" class="text-red-400 hover:text-red-600 remove-line shrink-0 p-1 hover:bg-red-50 rounded-lg transition" title="Retirer">
                <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                </svg>
            </button>
        `;
        linesContainer.appendChild(row);

        row.querySelector('.product-select').addEventListener('change', updateTotal);
        row.querySelector('.qty-input').addEventListener('input', updateTotal);
        row.querySelector('.price-input').addEventListener('input', updateTotal);
        row.querySelector('.remove-line').addEventListener('click', function () {
            row.remove();
            updateTotal();
        });

        updateTotal();
    }

    function updateTotal() {
        let total = 0;
        linesContainer.querySelectorAll('div.flex').forEach(row => {
            const qty = parseFloat(row.querySelector('.qty-input')?.value || 0);
            const price = parseFloat(row.querySelector('.price-input')?.value || 0);
            total += qty * price;
        });
        document.getElementById('totalDisplay').textContent = total.toLocaleString('fr-FR') + ' FCFA';
    }

    document.getElementById('addLineBtn').addEventListener('click', () => addLine());

    // Pré-remplissage avec les lignes existantes de la commande
    if (existingLines.length > 0) {
        existingLines.forEach(line => addLine(line.id, line.qty, line.unit_price));
    } else {
        addLine();
    }
</script>

@endsection