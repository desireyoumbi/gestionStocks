@extends('layouts.app')

@section('page-title', 'Tableau de bord')

@section('content')

@php
    // Calculs pour les 3 états de stock
    $availableCount = $availableCount ?? 0;
    $lowStockCount = $lowStockCount ?? 0;
    $outOfStockCount = $outOfStockCount ?? 0;

    $pctDisponible = $totalProducts > 0 ? round(($availableCount / $totalProducts) * 100) : 0;
    $pctLowStock = $totalProducts > 0 ? round(($lowStockCount / $totalProducts) * 100) : 0;
    $pctRupture = $totalProducts > 0 ? round(($outOfStockCount / $totalProducts) * 100) : 0;

    // --- CALCULS DE CAISSE EN TEMPS REEL (Réservé aux non-vendeurs pour optimiser/protéger) ---
    $boutique = Auth::user()->boutique;
    $currentCashBalance = ($boutique && Auth::user()->role !== 'vendeur') ? $boutique->getCashBalance() : 0;
    
    $totalCashIn = ($boutique && Auth::user()->role !== 'vendeur') ? \App\Models\CashMovement::where('boutique_id', $boutique->id)->where('type', 'in')->sum('amount') : 0;
    $totalCashOut = ($boutique && Auth::user()->role !== 'vendeur') ? \App\Models\CashMovement::where('boutique_id', $boutique->id)->where('type', 'out')->sum('amount') : 0;
    $unjustifiedCount = ($boutique && Auth::user()->role !== 'vendeur') ? \App\Models\CashMovement::where('boutique_id', $boutique->id)->where('is_unjustified', true)->count() : 0;
@endphp

<div class="grid grid-cols-1 xl:grid-cols-3 gap-8 items-start">

    <!-- COLONNE PRINCIPALE (2/3 de l'écran sur grand écran) -->
    <div class="xl:col-span-2 space-y-6">

        <!-- Bannière de bienvenue -->
        <div class="bg-gradient-to-r from-amber-500 to-amber-400 rounded-3xl p-8 flex items-center justify-between overflow-hidden relative shadow-2xl shadow-amber-500/25">
            <div class="relative z-10">
                <p class="text-amber-950/70 text-xs font-bold uppercase tracking-wider mb-1">Bon retour</p>
                <h1 class="text-2xl sm:text-3xl font-black text-slate-900 tracking-tight">{{ Auth::user()->name ?? 'Utilisateur' }}</h1>
                <p class="text-slate-900/80 text-sm mt-2 max-w-md font-medium leading-relaxed">
                    Voici un aperçu de l'activité de votre boutique aujourd'hui.
                </p>
                <div class="flex flex-wrap gap-3 mt-5">
                    @if(Auth::user()->role !== 'vendeur')
                        <a href="{{ route('products.create') }}" class="bg-slate-900 text-white text-xs font-bold px-5 py-3 rounded-xl hover:bg-slate-800 transition shadow-lg">
                            + Ajouter un produit
                        </a>
                        <a href="{{ route('suppliers.index') }}" class="bg-white/60 text-slate-900 text-xs font-bold px-5 py-3 rounded-xl hover:bg-white/90 transition backdrop-blur-sm shadow-sm">
                            Voir les fournisseurs
                        </a>
                    @else
                        <a href="{{ route('sales.create') }}" class="bg-slate-900 text-white text-xs font-bold px-5 py-3 rounded-xl hover:bg-slate-800 transition shadow-lg">
                            + Effectuer une vente
                        </a>
                    @endif
                </div>
            </div>
            <!-- Motif décoratif -->
            <div class="absolute -right-6 -top-6 w-40 h-40 bg-white/10 rounded-full pointer-events-none"></div>
            <div class="absolute right-16 bottom-0 w-24 h-24 bg-white/10 rounded-full pointer-events-none"></div>
        </div>

        <!-- Cartes statistiques de base -->
        <div class="grid grid-cols-2 lg:grid-cols-2 gap-4">
            <div class="bg-white border border-slate-200/80 rounded-3xl p-6 shadow-2xl shadow-slate-300/50 flex flex-col justify-between">
                <div class="w-10 h-10 rounded-2xl bg-amber-50 flex items-center justify-center mb-4 text-amber-500 shadow-sm">
                    <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4" />
                    </svg>
                </div>
                <div>
                    <p class="text-2xl font-black text-slate-900">{{ $totalProducts }}</p>
                    <p class="text-xs font-semibold text-slate-400 mt-0.5">Produits au total</p>
                </div>
            </div>

            <div class="bg-white border border-slate-200/80 rounded-3xl p-6 shadow-2xl shadow-slate-300/50 flex flex-col justify-between">
                <div class="w-10 h-10 rounded-2xl bg-red-50 flex items-center justify-center mb-4 text-red-500 shadow-sm">
                    <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m9-.75a9 9 0 11-18 0 9 9 0 0118 0zm-9 3.75h.008v.008H12v-.008z" />
                    </svg>
                </div>
                <div>
                    <p class="text-2xl font-black text-slate-900">{{ $outOfStockCount }}</p>
                    <p class="text-xs font-semibold text-slate-400 mt-0.5">En rupture de stock</p>
                </div>
            </div>
        </div>

        <!-- MASQUÉ POUR LE VENDEUR : WIDGET DE SUIVI DE LA CAISSE EN TEMPS REEL -->
        @if(Auth::user()->role !== 'vendeur')
            <div class="bg-gradient-to-br from-slate-900 to-slate-800 rounded-3xl p-8 text-white shadow-2xl shadow-slate-900/40 space-y-6">
                <div class="flex items-center justify-between">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 rounded-2xl bg-white/10 flex items-center justify-center text-amber-400 font-bold">💼</div>
                        <div>
                            <h2 class="text-base font-black tracking-tight text-white">État de la Caisse</h2>
                            <p class="text-xs text-slate-400 font-medium">Trésorerie et liquidités disponibles</p>
                        </div>
                    </div>
                    @if($unjustifiedCount > 0)
                        <span class="text-[10px] font-black text-red-400 bg-red-950/60 border border-red-500/30 px-3 py-1 rounded-xl">
                            ⚠️ {{ $unjustifiedCount }} sortie(s) non justifiée(s)
                        </span>
                    @else
                        <span class="text-[10px] font-bold text-emerald-400 bg-emerald-950/60 border border-emerald-500/30 px-3 py-1 rounded-xl">
                            Caisse équilibrée
                        </span>
                    @endif
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 pt-2">
                    <div class="p-4 rounded-2xl bg-white/5 border border-white/10">
                        <p class="text-xs text-slate-400 font-bold mb-1">Total Entrées (Ventes)</p>
                        <p class="text-lg font-black text-emerald-400">+ {{ number_format($totalCashIn, 0, ',', ' ') }} FCFA</p>
                    </div>
                    <div class="p-4 rounded-2xl bg-white/5 border border-white/10">
                        <p class="text-xs text-slate-400 font-bold mb-1">Total Sorties (Dép./Appro)</p>
                        <p class="text-lg font-black text-red-400">- {{ number_format($totalCashOut, 0, ',', ' ') }} FCFA</p>
                    </div>
                    <div class="p-4 rounded-2xl bg-amber-500/10 border border-amber-500/30 flex flex-col justify-between">
                        <p class="text-xs text-amber-300 font-bold mb-1">Solde Actuel en Caisse</p>
                        <p class="text-xl font-black {{ $currentCashBalance >= 0 ? 'text-amber-400' : 'text-red-400' }}">
                            {{ number_format($currentCashBalance, 0, ',', ' ') }} FCFA
                        </p>
                    </div>
                </div>
            </div>

            <!-- MASQUÉ POUR LE VENDEUR : SECTION FINANCIÈRE AMÉLIORÉE & GAINS PAR PRODUIT -->
            <div class="bg-white border border-slate-200/80 rounded-3xl p-8 shadow-2xl shadow-slate-300/50 space-y-8">
                <div class="flex items-center justify-between">
                    <div>
                        <h2 class="text-base font-black text-slate-900 tracking-tight">Aperçu Financier du Mois</h2>
                        <p class="text-xs font-medium text-slate-400 mt-0.5">Suivi des performances et de la rentabilité</p>
                    </div>
                    <span class="text-[10px] font-bold text-emerald-600 bg-emerald-50 px-2.5 py-1 rounded-lg">Temps réel</span>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                    <!-- Chiffre d'affaires -->
                    <div class="p-5 rounded-2xl bg-slate-50 border border-slate-200/60 flex flex-col justify-between">
                        <div class="flex items-center justify-between mb-3">
                            <span class="text-xs font-bold text-slate-500">Chiffre d'affaires</span>
                            <span class="w-7 h-7 rounded-xl bg-blue-100 text-blue-600 flex items-center justify-center text-xs">📈</span>
                        </div>
                        <div>
                            <p class="text-xl font-black text-slate-900">{{ number_format($caMonth ?? 0, 0, ',', ' ') }} FCFA</p>
                            <p class="text-[11px] font-semibold text-blue-600 mt-1">Semaine : {{ number_format($caWeek ?? 0, 0, ',', ' ') }} FCFA</p>
                        </div>
                    </div>

                    <!-- Dépenses -->
                    <div class="p-5 rounded-2xl bg-slate-50 border border-slate-200/60 flex flex-col justify-between">
                        <div class="flex items-center justify-between mb-3">
                            <span class="text-xs font-bold text-slate-500">Dépenses</span>
                            <span class="w-7 h-7 rounded-xl bg-orange-100 text-orange-600 flex items-center justify-center text-xs">💸</span>
                        </div>
                        <div>
                            <p class="text-xl font-black text-slate-900">{{ number_format($expensesMonth ?? 0, 0, ',', ' ') }} FCFA</p>
                            <p class="text-[11px] font-semibold text-orange-600 mt-1">Semaine : {{ number_format($expensesWeek ?? 0, 0, ',', ' ') }} FCFA</p>
                        </div>
                    </div>

                    <!-- Bénéfice Net -->
                    <div class="p-5 rounded-2xl bg-slate-50 border border-slate-200/60 flex flex-col justify-between">
                        <div class="flex items-center justify-between mb-3">
                            <span class="text-xs font-bold text-slate-500">Bénéfice Net</span>
                            <span class="w-7 h-7 rounded-xl bg-emerald-100 text-emerald-600 flex items-center justify-center text-xs">💰</span>
                        </div>
                        <div>
                            <p class="text-xl font-black {{ ($netProfitMonth ?? 0) >= 0 ? 'text-emerald-600' : 'text-red-600' }}">
                                {{ number_format($netProfitMonth ?? 0, 0, ',', ' ') }} FCFA
                            </p>
                            <p class="text-[11px] font-semibold text-emerald-600 mt-1">
                                Semaine : {{ number_format($netProfitWeek ?? 0, 0, ',', ' ') }} FCFA
                            </p>
                        </div>
                    </div>
                </div>

                <!-- TABLEAU DU DÉTAIL DES VENTES ET GAINS PAR PRODUIT -->
                <div class="pt-4 border-t border-slate-100">
                    <h3 class="text-xs font-black text-slate-900 uppercase tracking-wider mb-4">Détail des ventes et gains par produit (Ce mois)</h3>
                    
                    @if(!empty($productSalesStats) && count($productSalesStats) > 0)
                        <div class="overflow-x-auto">
                            <table class="w-full text-left border-collapse">
                                <thead>
                                    <tr class="border-b border-slate-200 text-[11px] font-bold text-slate-400">
                                        <th class="pb-3">Produit</th>
                                        <th class="pb-3 text-center">Qté vendue</th>
                                        <th class="pb-3 text-right">CA Généré</th>
                                        <th class="pb-3 text-right">Bénéfice estimé</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-slate-100 text-xs">
                                    @foreach($productSalesStats as $stat)
                                        <tr>
                                            <td class="py-3 font-bold text-slate-800">{{ $stat['name'] }}</td>
                                            <td class="py-3 text-center">
                                                <span class="bg-slate-100 font-bold px-2.5 py-1 rounded-lg text-slate-700">{{ $stat['total_qty'] }}</span>
                                            </td>
                                            <td class="py-3 text-right font-black text-slate-900">{{ number_format($stat['total_revenue'], 0, ',', ' ') }} FCFA</td>
                                            <td class="py-3 text-right font-black {{ $stat['total_profit'] >= 0 ? 'text-emerald-600' : 'text-red-600' }}">
                                                {{ number_format($stat['total_profit'], 0, ',', ' ') }} FCFA
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <div class="text-center py-6 bg-slate-50 rounded-2xl border border-slate-200/60">
                            <p class="text-xs text-slate-400 font-medium">Aucune vente enregistrée pour le moment ce mois-ci.</p>
                        </div>
                    @endif
                </div>
            </div>
        @endif

        <!-- Zone État du stock -->
        <div class="bg-white border border-slate-200/80 rounded-3xl p-8 shadow-2xl shadow-slate-300/50">
            <div class="flex items-center justify-between mb-6">
                <div>
                    <h2 class="text-base font-black text-slate-900 tracking-tight">État du stock</h2>
                    <p class="text-xs font-medium text-slate-400 mt-0.5">{{ $totalProducts }} produits référencés dans le système</p>
                </div>
            </div>

            @if($totalProducts > 0)
            <div class="flex flex-col sm:flex-row items-center gap-8">
                <!-- Cercle graphique circulaire à 3 segments -->
                <div class="relative w-40 h-40 shrink-0 rounded-full shadow-inner"
                     style="background: conic-gradient(#10b981 0% {{ $pctDisponible }}%, #f59e0b {{ $pctDisponible }}% {{ $pctDisponible + $pctLowStock }}%, #ef4444 {{ $pctDisponible + $pctLowStock }}% 100%);">
                    <div class="absolute inset-3 bg-white rounded-full flex items-center justify-center flex-col shadow-sm">
                        <span class="text-xl font-black text-slate-900">{{ $totalProducts }}</span>
                        <span class="text-[10px] font-bold text-slate-400 uppercase tracking-widest">total</span>
                    </div>
                </div>

                <!-- Barres de progression et statistiques détaillées -->
                <div class="flex-1 w-full space-y-4">
                    <!-- 1. Disponibles -->
                    <div>
                        <div class="flex items-center justify-between text-xs mb-1">
                            <div class="flex items-center gap-2">
                                <span class="w-2.5 h-2.5 rounded-full bg-emerald-500"></span>
                                <span class="font-bold text-slate-600">Disponibles (> seuil)</span>
                            </div>
                            <span class="font-black text-slate-900">{{ $availableCount }} ({{ $pctDisponible }}%)</span>
                        </div>
                        <div class="w-full h-2 bg-slate-100 rounded-full overflow-hidden">
                            <div class="h-full bg-emerald-500 rounded-full transition-all duration-500" style="width: {{ $pctDisponible }}%"></div>
                        </div>
                    </div>

                    <!-- 2. Stock faible -->
                    <div>
                        <div class="flex items-center justify-between text-xs mb-1">
                            <div class="flex items-center gap-2">
                                <span class="w-2.5 h-2.5 rounded-full bg-amber-500"></span>
                                <span class="font-bold text-slate-600">Stock faible (&le; seuil)</span>
                            </div>
                            <span class="font-black text-slate-900">{{ $lowStockCount }} ({{ $pctLowStock }}%)</span>
                        </div>
                        <div class="w-full h-2 bg-slate-100 rounded-full overflow-hidden">
                            <div class="h-full bg-amber-500 rounded-full transition-all duration-500" style="width: {{ $pctLowStock }}%"></div>
                        </div>
                    </div>

                    <!-- 3. En rupture -->
                    <div>
                        <div class="flex items-center justify-between text-xs mb-1">
                            <div class="flex items-center gap-2">
                                <span class="w-2.5 h-2.5 rounded-full bg-red-500"></span>
                                <span class="font-bold text-slate-600">En rupture (0)</span>
                            </div>
                            <span class="font-black text-slate-900">{{ $outOfStockCount }} ({{ $pctRupture }}%)</span>
                        </div>
                        <div class="w-full h-2 bg-slate-100 rounded-full overflow-hidden">
                            <div class="h-full bg-red-500 rounded-full transition-all duration-500" style="width: {{ $pctRupture }}%"></div>
                        </div>
                    </div>
                </div>
            </div>
            @else
            <div class="text-center py-10">
                <p class="text-xs font-medium text-slate-400">Aucun produit enregistré pour l'instant.</p>
                @if(Auth::user()->role !== 'vendeur')
                    <a href="{{ route('products.create') }}" class="inline-block mt-3 text-xs font-bold text-amber-600 hover:text-amber-700">
                        Ajouter votre premier produit &rarr;
                    </a>
                @endif
            </div>
            @endif
        </div>

    </div>

    <!-- COLONNE LATÉRALE -->
    <div class="space-y-6">

        <!-- Produit Phare de la Semaine -->
        <div class="bg-white border border-slate-200/80 rounded-3xl p-6 shadow-2xl shadow-slate-300/50">
            <div class="flex items-center justify-between mb-4">
                <h2 class="text-base font-black text-slate-900 tracking-tight">Top Vente</h2>
                <span class="text-[10px] font-bold text-amber-600 bg-amber-50 px-2.5 py-1 rounded-lg">Semaine</span>
            </div>

            @if(isset($bestProduct))
                <div class="p-4 rounded-2xl bg-amber-50/50 border border-amber-200/60 flex items-center gap-3">
                    <div class="w-10 h-10 rounded-xl bg-amber-500 text-white flex items-center justify-center shrink-0 font-bold text-sm shadow-md">🔥</div>
                    <div class="min-w-0 flex-1">
                        <p class="text-xs font-black text-slate-900 truncate">{{ $bestProduct->name }}</p>
                        <p class="text-[11px] text-slate-500 mt-0.5">Prix : {{ number_format($bestProduct->selling_price, 0, ',', ' ') }} FCFA</p>
                    </div>
                </div>
            @else
                <div class="text-center py-6 bg-slate-50 rounded-2xl border border-slate-200/60">
                    <p class="text-xs text-slate-400 font-medium">Aucune vente enregistrée cette semaine.</p>
                </div>
            @endif
        </div>

        <!-- Liste des produits en stock faible -->
        <div class="bg-white border border-slate-200/80 rounded-3xl p-6 shadow-2xl shadow-slate-300/50">
            <div class="flex items-center justify-between mb-4">
                <h2 class="text-base font-black text-slate-900 tracking-tight">Alertes Stock Faible</h2>
                <span class="text-[10px] font-bold text-orange-600 bg-orange-50 px-2.5 py-1 rounded-lg">Par seuil</span>
            </div>

            @if(isset($lowStockProducts) && $lowStockProducts->count() > 0)
                <div class="space-y-3 max-h-72 overflow-y-auto pr-1">
                    @foreach($lowStockProducts as $product)
                        <div class="p-3.5 rounded-2xl bg-slate-50 border border-slate-200/60 flex items-center justify-between transition hover:bg-orange-50/30">
                            <div class="min-w-0 flex-1 mr-3">
                                <p class="text-xs font-bold text-slate-800 truncate">{{ $product->name }}</p>
                                <p class="text-[10px] text-slate-400 mt-0.5">Seuil d'alerte : {{ $product->alert_threshold }}</p>
                            </div>
                            <span class="px-2.5 py-1 text-[10px] font-black rounded-lg shrink-0 {{ $product->quantity == 0 ? 'bg-red-100 text-red-600' : 'bg-orange-100 text-orange-600' }}">
                                {{ $product->quantity }} restants
                            </span>
                        </div>
                    @endforeach
                </div>
            @else
                <div class="text-center py-6 bg-slate-50 rounded-2xl border border-slate-200/60">
                    <p class="text-xs text-slate-400 font-medium">Aucun produit en stock critique.</p>
                </div>
            @endif
        </div>

        <!-- MES ACTIVITÉS DYNAMIQUES -->
        <div class="bg-white border border-slate-200/80 rounded-3xl p-6 shadow-2xl shadow-slate-300/50">
            <div class="flex items-center justify-between mb-5">
                <h2 class="text-base font-black text-slate-900 tracking-tight">Mes Activités</h2>
                <span class="text-[10px] font-bold text-amber-600 bg-amber-50 px-2.5 py-1 rounded-lg">Récent</span>
            </div>

            <div class="space-y-3">
                <!-- 1. Dernière activité dynamique -->
                @if(isset($latestActivity) && $latestActivity)
                    <div class="p-3.5 rounded-2xl bg-slate-50 border border-slate-200/60 flex items-start gap-3 transition hover:bg-amber-50/40">
                        <div class="w-8 h-8 rounded-xl bg-amber-100 text-amber-600 flex items-center justify-center shrink-0 font-bold text-xs mt-0.5">📦</div>
                        <div class="flex-1 min-w-0">
                            <p class="text-xs font-bold text-slate-800 truncate">
                                {{ ucfirst($latestActivity->type) }} : {{ $latestActivity->products->first()->name ?? 'Produit' }}
                            </p>
                            <p class="text-[11px] text-slate-400 mt-0.5 truncate">
                                {{ $latestActivity->motif ?? 'Mouvement enregistré' }}
                            </p>
                        </div>
                        <span class="text-[10px] font-medium text-slate-400 shrink-0">
                            {{ $latestActivity->created_at->diffForHumans() }}
                        </span>
                    </div>
                @else
                    <div class="p-3.5 rounded-2xl bg-slate-50 border border-slate-200/60 text-center">
                        <p class="text-xs text-slate-400 font-medium">Aucune activité récente.</p>
                    </div>
                @endif

                <!-- 2. Session active (Rôle & Sécurité) -->
                <div class="p-3.5 rounded-2xl bg-slate-50 border border-slate-200/60 flex items-start gap-3 transition hover:bg-emerald-50/40">
                    <div class="w-8 h-8 rounded-xl bg-emerald-100 text-emerald-600 flex items-center justify-center shrink-0 font-bold text-xs mt-0.5">✨</div>
                    <div class="flex-1 min-w-0">
                        <p class="text-xs font-bold text-slate-800 truncate">Session active</p>
                        <p class="text-[11px] text-slate-400 mt-0.5">Connecté en tant que {{ ucfirst(Auth::user()->role ?? 'Utilisateur') }}</p>
                    </div>
                    <span class="text-[10px] font-bold text-emerald-600 shrink-0">Actif</span>
                </div>
            </div>
        </div>

        @if(Auth::user()->role !== 'vendeur')
            <div class="bg-gradient-to-br from-slate-900 to-slate-800 rounded-3xl p-6 text-white shadow-2xl shadow-slate-900/40">
                <div class="w-10 h-10 rounded-2xl bg-white/10 flex items-center justify-center mb-4 text-amber-400">
                    <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
                    </svg>
                </div>
                <h3 class="text-sm font-black tracking-tight mb-1">Bilans et Rapports</h3>
                <p class="text-xs text-slate-300 leading-relaxed font-medium mb-4">
                    Consultez l'historique des performances financières mois par mois et le bilan annuel de la boutique.
                </p>
                <a href="{{ route('reports.annual') }}" class="inline-flex items-center gap-2 text-xs font-bold text-amber-400 hover:text-amber-300 transition">
                    <span>Voir les bilans annuels</span>
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M14 5l7 7m0 0l-7 7m7-7H3" />
                    </svg>
                </a>
            </div>
        @endif

    </div>

</div>

@endsection