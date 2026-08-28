@extends('layouts.app')
@section('header_title', 'Mes fournisseurs')
@section('content')
<div class="space-y-6">

    {{-- EN-TÊTE DE PAGE --}}
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
        <div>
            <h1 class="text-2xl font-black text-slate-900 tracking-tight">Fournisseurs</h1>
            <p class="text-sm text-slate-500 mt-0.5">Gérez et suivez l'ensemble de vos fournisseurs et partenaires.</p>
        </div>
        
        <div class="flex items-center gap-2 shrink-0">
            <a href="{{ route('supplier.export.pdf') }}" class="bg-red-600 hover:bg-red-700 text-white text-sm font-bold px-4 py-2.5 rounded-xl shadow-md hover:shadow-lg transition flex items-center gap-2">
                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z" />
                </svg>
                Exporter PDF
            </a>

            <a href="{{ route('suppliers.create') }}" class="bg-amber-500 hover:bg-amber-600 text-slate-900 text-sm font-bold px-4 py-2.5 rounded-xl shadow-md hover:shadow-lg transition flex items-center gap-1.5">
                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4" />
                </svg>
                Nouveau fournisseur
            </a>
        </div>
    </div>

    {{-- Message flash --}}
    @if(session('succes'))
        <div class="bg-emerald-50 border border-emerald-200 text-emerald-800 px-4 py-3 rounded-2xl text-sm font-medium flex items-center shadow-md">
            <span class="mr-2">✅</span> {{ session('succes') }}
        </div>
    @endif

    {{-- BARRE DE RECHERCHE --}}
    <div class="bg-white p-6 rounded-3xl shadow-xl border border-slate-100">
        <div class="relative">
            <svg class="w-5 h-5 absolute left-3.5 top-1/2 -translate-y-1/2 text-slate-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
            </svg>
            <input type="text" id="liveSearch" placeholder="Rechercher par nom, téléphone, email, produit ou adresse..." 
                class="w-full bg-slate-50 border border-slate-200 rounded-xl pl-11 pr-4 py-3 text-sm text-slate-800 placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-amber-500/20 focus:border-amber-500 transition">
        </div>
    </div>

    {{-- TABLEAU --}}
    <div class="bg-white rounded-3xl shadow-xl border border-slate-100 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse" id="suppliersTable">
                <thead>
                    <tr class="bg-slate-50/70 border-b border-slate-100 text-xs font-bold text-slate-400 uppercase tracking-wider">
                        <th class="px-6 py-4">Nom</th>
                        <th class="px-6 py-4">Produits livrés</th>
                        <th class="px-6 py-4">Téléphone</th>
                        <th class="px-6 py-4">Email</th>
                        <th class="px-6 py-4">Adresse</th>
                        <th class="px-6 py-4 text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 text-sm">
                    @forelse($suppliers as $supplier)
                        <tr class="supplier-row hover:bg-slate-50/70 transition">
                            <td class="px-6 py-4 font-bold text-slate-900 supplier-name">{{ $supplier->name }}</td>
                            
                            {{-- Affichage des produits --}}
                            <td class="px-6 py-4 supplier-products">
                                <div class="flex flex-wrap gap-1">
                                    @foreach($supplier->products as $product)
                                        <span class="px-2 py-0.5 bg-amber-100 text-amber-700 rounded-full text-[10px] font-bold uppercase tracking-wide">
                                            {{ $product->name }}
                                        </span>
                                    @endforeach
                                    @if($supplier->products->isEmpty())
                                        <span class="text-slate-400 italic text-xs">Aucun produit</span>
                                    @endif
                                </div>
                            </td>

                            <td class="px-6 py-4 text-slate-500 font-medium supplier-phone">{{ $supplier->phone }}</td>
                            <td class="px-6 py-4 text-slate-500 supplier-email">{{ $supplier->email ?? '—' }}</td>
                            <td class="px-6 py-4 text-slate-500 supplier-address">{{ $supplier->adresse ?? '—' }}</td>
                            
                            <td class="px-6 py-4 text-right">
                                <div class="flex items-center justify-end gap-2">
                                    <a href="{{ route('suppliers.edit', $supplier->id) }}" class="p-2.5 bg-indigo-50 text-indigo-600 hover:bg-indigo-600 hover:text-white rounded-xl transition">
                                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" /></svg>
                                    </a>
                                    <form action="{{ route('suppliers.destroy', $supplier->id) }}" method="POST" class="inline-block" onsubmit="return confirm('Supprimer ?');">
                                        @csrf @method('DELETE')
                                        <button type="submit" class="p-2.5 bg-red-50 text-red-600 hover:bg-red-600 hover:text-white rounded-xl transition">
                                            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" /></svg>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="6" class="px-6 py-16 text-center text-slate-500">Aucun fournisseur trouvé.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

<script>
    document.getElementById('liveSearch').addEventListener('input', function(e) {
        let term = e.target.value.toLowerCase();
        let rows = document.querySelectorAll('.supplier-row');

        rows.forEach(row => {
            let name = row.querySelector('.supplier-name').textContent.toLowerCase();
            let phone = row.querySelector('.supplier-phone').textContent.toLowerCase();
            let email = row.querySelector('.supplier-email').textContent.toLowerCase();
            let products = row.querySelector('.supplier-products').textContent.toLowerCase();
            let address = row.querySelector('.supplier-address').textContent.toLowerCase();

            if (name.includes(term) || phone.includes(term) || email.includes(term) || products.includes(term) || address.includes(term)) {
                row.style.display = '';
            } else {
                row.style.display = 'none';
            }
        });
    });
</script>
@endsection