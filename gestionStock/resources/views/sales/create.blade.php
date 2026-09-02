@extends('layouts.app')

@section('header_subtitle', 'Nouvelle vente multiple')
@section('header_title', 'Enregistrer des ventes')

@section('content')
<div class="max-w-4xl mx-auto space-y-6" x-data="saleForm()">
    
    <!-- Messages d'erreur globaux -->
    @if ($errors->any())
        <div class="p-4 rounded-2xl bg-rose-500/10 border border-rose-500/20 text-rose-500 text-sm font-semibold">
            <ul class="list-disc list-inside space-y-1">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('sales.store') }}" method="POST" class="space-y-6">
        @csrf

        <!-- Motif global de la vente -->
        <div class="bg-white dark:bg-gray-950 border border-gray-100 dark:border-gray-800 rounded-2xl p-5 shadow-sm">
            <label class="block text-sm font-bold text-gray-700 dark:text-gray-300 uppercase tracking-wider mb-2">Motif / Référence de la facture</label>
            <input type="text" name="motif" value="{{ old('motif', 'Vente') }}" class="w-full px-4 py-3 bg-gray-50 dark:bg-gray-900 border border-gray-200 dark:border-gray-800 rounded-xl text-sm text-gray-800 dark:text-gray-200 focus:outline-none focus:border-amber-500 transition">
        </div>

        <!-- Lignes de produits -->
        <div class="bg-white dark:bg-gray-950 border border-gray-100 dark:border-gray-800 rounded-2xl p-5 shadow-sm space-y-4">
            <div class="flex items-center justify-between border-b border-gray-100 dark:border-gray-900 pb-3">
                <h3 class="text-sm font-bold uppercase tracking-wider text-gray-400">Produits concernés</h3>
                <button type="button" @click="addRow()" class="px-4 py-2 bg-amber-500 hover:bg-amber-600 text-gray-900 font-bold text-sm rounded-xl transition shadow">
                    + Ajouter un produit
                </button>
            </div>

            <div class="space-y-3">
                <template x-for="(row, index) in rows" :key="index">
                    <div class="flex flex-wrap sm:flex-nowrap items-center gap-3 bg-gray-50 dark:bg-gray-900/50 p-4 rounded-xl border border-gray-200 dark:border-gray-800">
                        
                        <!-- Sélection du produit -->
                        <div class="flex-1 min-w-[200px]">
                            <label class="block text-xs font-bold text-gray-500 dark:text-gray-400 uppercase mb-1">Produit</label>
                            <select :name="`items[${index}][product_id]`" x-model="row.product_id" @change="updatePrice(row)" required class="w-full px-3 py-2.5 bg-white dark:bg-gray-900 border border-gray-200 dark:border-gray-800 rounded-lg text-sm text-gray-800 dark:text-gray-200 focus:outline-none focus:border-amber-500">
                                <option value="">-- Choisir un produit --</option>
                                @foreach($products as $product)
                                    <option value="{{ $product->id }}" data-price="{{ $product->selling_price }}">
                                        {{ $product->name }} (Stock : {{ $product->quantity }}) {{ $product->quantity <= 0 ? ' [ÉPUISÉ]' : '' }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <!-- Prix de vente unitaire (Modifiable / Négocié) -->
                        <div class="w-36">
                            <label class="block text-xs font-bold text-gray-500 dark:text-gray-400 uppercase mb-1">Prix Unitaire</label>
                            <input type="number" :name="`items[${index}][unit_price]`" x-model.number="row.selling_price" min="1" step="any" required class="w-full px-3 py-2.5 bg-white dark:bg-gray-900 border border-gray-200 dark:border-gray-800 rounded-lg text-sm font-semibold text-gray-800 dark:text-gray-200 focus:outline-none focus:border-amber-500">
                        </div>

                        <!-- Quantité -->
                        <div class="w-24">
                            <label class="block text-xs font-bold text-gray-500 dark:text-gray-400 uppercase mb-1">Qté</label>
                            <input type="number" :name="`items[${index}][quantity]`" x-model.number="row.quantity" min="1" required class="w-full px-3 py-2.5 bg-white dark:bg-gray-900 border border-gray-200 dark:border-gray-800 rounded-lg text-sm text-gray-800 dark:text-gray-200 focus:outline-none focus:border-amber-500">
                        </div>

                        <!-- Sous-total de la ligne -->
                        <div class="w-32">
                            <label class="block text-xs font-bold text-gray-500 dark:text-gray-400 uppercase mb-1">Total Ligne</label>
                            <div class="px-3 py-2.5 bg-rose-50 dark:bg-rose-950/30 border border-rose-100 dark:border-rose-900/50 rounded-lg text-sm font-black text-rose-600 dark:text-rose-400">
                                <span x-text="formatNumber(getSubtotal(row))"></span> F
                            </div>
                        </div>

                        <!-- Bouton Supprimer la ligne -->
                        <div class="pt-5">
                            <button type="button" @click="removeRow(index)" x-show="rows.length > 1" class="p-2.5 bg-rose-500/10 text-rose-500 hover:bg-rose-500 hover:text-white rounded-lg transition" title="Supprimer la ligne">
                                <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2.032 2.032 0 0116.138 21H7.862a2.032 2.032 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" /></svg>
                            </button>
                        </div>

                    </div>
                </template>
            </div>
        </div>

        <!-- RÉCAPITULATIF MONTANT TOTAL -->
        <div class="bg-white dark:bg-gray-950 border border-gray-100 dark:border-gray-800 rounded-2xl p-5 shadow-sm flex items-center justify-between">
            <span class="text-sm font-black uppercase tracking-wider text-gray-500">Montant Total de la Vente :</span>
            <span class="text-2xl font-black text-rose-600 dark:text-rose-400">
                <span x-text="formatNumber(getTotalAmount())"></span> FCFA
            </span>
        </div>

        <!-- Bouton de validation global -->
        <div class="flex items-center justify-end gap-3">
            <a href="{{ route('sales.index') }}" class="px-5 py-3 bg-gray-100 dark:bg-gray-900 hover:bg-gray-200 dark:hover:bg-gray-800 text-gray-700 dark:text-gray-300 font-bold text-sm rounded-xl border border-gray-200 dark:border-gray-800 transition">
                Annuler
            </a>
            <button type="submit" class="px-6 py-3 bg-rose-500 hover:bg-rose-600 text-white font-bold text-sm rounded-xl shadow-lg shadow-rose-500/20 transition">
                Enregistrer la vente
            </button>
        </div>

    </form>
</div>

<script>
    const productsData = @json($products->keyBy('id'));

    function saleForm() {
        return {
            rows: [{ product_id: '', quantity: 1, selling_price: 0 }],
            addRow() {
                this.rows.push({ product_id: '', quantity: 1, selling_price: 0 });
            },
            removeRow(index) {
                if (this.rows.length > 1) {
                    this.rows.splice(index, 1);
                }
            },
            updatePrice(row) {
                if (!row.product_id || !productsData[row.product_id]) {
                    row.selling_price = 0;
                    return;
                }
                // Pré-remplit avec le prix de vente de base du produit sélectionné,
                // reste modifiable ensuite par l'utilisateur (négociation).
                row.selling_price = parseFloat(productsData[row.product_id].selling_price) || 0;
            },
            getSubtotal(row) {
                const price = parseFloat(row.selling_price) || 0;
                const qty = parseInt(row.quantity) || 0;
                return price * qty;
            },
            getTotalAmount() {
                let total = 0;
                this.rows.forEach(row => {
                    total += this.getSubtotal(row);
                });
                return total;
            },
            formatNumber(value) {
                return new Intl.NumberFormat('fr-FR').format(value);
            }
        }
    }
</script>
@endsection