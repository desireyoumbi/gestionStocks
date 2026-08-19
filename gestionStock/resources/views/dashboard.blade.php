@extends('layouts.app')

@section('page-title', 'Tableau de bord')

@section('content')

@php
    $totalDisponible = max($totalProducts - $outOfStockCount, 0);
    $pctDisponible = $totalProducts > 0 ? round(($totalDisponible / $totalProducts) * 100) : 0;
    $pctRupture = $totalProducts > 0 ? 100 - $pctDisponible : 0;
@endphp

<div class="grid grid-cols-1 xl:grid-cols-3 gap-8 items-start">

    <!-- COLONNE PRINCIPALE (2/3 de l'écran sur grand écran) -->
    <div class="xl:col-span-2 space-y-6">

        <!-- Bannière de bienvenue -->
        <div class="bg-gradient-to-r from-amber-500 to-amber-400 rounded-3xl p-8 flex items-center justify-between overflow-hidden relative shadow-2xl shadow-amber-500/25">
            <div class="relative z-10">
                <p class="text-amber-950/70 text-xs font-bold uppercase tracking-wider mb-1">Bon retour</p>
                <h1 class="text-2xl sm:text-3xl font-black text-slate-900 tracking-tight">{{ Auth::user()->name ?? 'Utilisateur' }} 👋</h1>
                <p class="text-slate-900/80 text-sm mt-2 max-w-md font-medium leading-relaxed">
                    Voici un aperçu de l'activité de votre boutique aujourd'hui.
                </p>
                <div class="flex flex-wrap gap-3 mt-5">
                    <a href="{{ route('products.create') }}" class="bg-slate-900 text-white text-xs font-bold px-5 py-3 rounded-xl hover:bg-slate-800 transition shadow-lg">
                        + Ajouter un produit
                    </a>
                    <a href="{{ route('suppliers.index') }}" class="bg-white/60 text-slate-900 text-xs font-bold px-5 py-3 rounded-xl hover:bg-white/90 transition backdrop-blur-sm shadow-sm">
                        Voir les fournisseurs
                    </a>
                </div>
            </div>
            <!-- Motif décoratif -->
            <div class="absolute -right-6 -top-6 w-40 h-40 bg-white/10 rounded-full pointer-events-none"></div>
            <div class="absolute right-16 bottom-0 w-24 h-24 bg-white/10 rounded-full pointer-events-none"></div>
        </div>

        <!-- Cartes statistiques (Ombres et contrastes renforcés) -->
        <div class="grid grid-cols-2 lg:grid-cols-3 gap-4">
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

            <div class="bg-white border border-slate-200/80 rounded-3xl p-6 shadow-2xl shadow-slate-300/50 flex flex-col justify-between col-span-2 lg:col-span-1">
                <div class="w-10 h-10 rounded-2xl bg-indigo-50 flex items-center justify-center mb-4 text-indigo-500 shadow-sm">
                    <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10" />
                    </svg>
                </div>
                <div>
                    <p class="text-2xl font-black text-slate-900">{{ $totalCategories }}</p>
                    <p class="text-xs font-semibold text-slate-400 mt-0.5">Catégories</p>
                </div>
            </div>
        </div>

        <!-- Zone État du stock : donut + résumé (Ombre renforcée) -->
        <div class="bg-white border border-slate-200/80 rounded-3xl p-8 shadow-2xl shadow-slate-300/50">
            <div class="flex items-center justify-between mb-6">
                <div>
                    <h2 class="text-base font-black text-slate-900 tracking-tight">État du stock</h2>
                    <p class="text-xs font-medium text-slate-400 mt-0.5">{{ $totalProducts }} produits référencés dans le système</p>
                </div>
            </div>

            @if($totalProducts > 0)
            <div class="flex flex-col sm:flex-row items-center gap-8">
                <!-- Donut en CSS pur (conic-gradient) -->
                <div class="relative w-40 h-40 shrink-0 rounded-full shadow-inner"
                     style="background: conic-gradient(#10b981 0% {{ $pctDisponible }}%, #ef4444 {{ $pctDisponible }}% 100%);">
                    <div class="absolute inset-3 bg-white rounded-full flex items-center justify-center flex-col shadow-sm">
                        <span class="text-xl font-black text-slate-900">{{ $totalProducts }}</span>
                        <span class="text-[10px] font-bold text-slate-400 uppercase tracking-widest">total</span>
                    </div>
                </div>

                <div class="flex-1 w-full space-y-4">
                    <div class="flex items-center justify-between">
                        <div class="flex items-center gap-2">
                            <span class="w-2.5 h-2.5 rounded-full bg-emerald-500"></span>
                            <span class="text-xs font-bold text-slate-600">Disponibles</span>
                        </div>
                        <span class="text-xs font-black text-slate-900">{{ $totalDisponible }} ({{ $pctDisponible }}%)</span>
                    </div>
                    <div class="w-full h-2 bg-slate-100 rounded-full overflow-hidden">
                        <div class="h-full bg-emerald-500 rounded-full transition-all duration-500" style="width: {{ $pctDisponible }}%"></div>
                    </div>

                    <div class="flex items-center justify-between">
                        <div class="flex items-center gap-2">
                            <span class="w-2.5 h-2.5 rounded-full bg-red-500"></span>
                            <span class="text-xs font-bold text-slate-600">En rupture</span>
                        </div>
                        <span class="text-xs font-black text-slate-900">{{ $outOfStockCount }} ({{ $pctRupture }}%)</span>
                    </div>
                    <div class="w-full h-2 bg-slate-100 rounded-full overflow-hidden">
                        <div class="h-full bg-red-500 rounded-full transition-all duration-500" style="width: {{ $pctRupture }}%"></div>
                    </div>
                </div>
            </div>
            @else
            <div class="text-center py-10">
                <p class="text-xs font-medium text-slate-400">Aucun produit enregistré pour l'instant.</p>
                <a href="{{ route('products.create') }}" class="inline-block mt-3 text-xs font-bold text-amber-600 hover:text-amber-700">
                    Ajouter votre premier produit &rarr;
                </a>
            </div>
            @endif
        </div>

    </div>

    <!-- COLONNE LATÉRALE : "Mes activités" (Ombre très prononcée) -->
    <div class="space-y-6">

        <div class="bg-white border border-slate-200/80 rounded-3xl p-6 shadow-2xl shadow-slate-300/50">
            <div class="flex items-center justify-between mb-5">
                <h2 class="text-base font-black text-slate-900 tracking-tight">Mes Activités</h2>
                <span class="text-[10px] font-bold text-amber-600 bg-amber-50 px-2.5 py-1 rounded-lg">Récent</span>
            </div>

            <!-- Liste des mini-cartes d'activité -->
            <div class="space-y-3">
                <!-- Élément 1 -->
                <div class="p-3.5 rounded-2xl bg-slate-50 border border-slate-200/60 flex items-start gap-3 transition hover:bg-amber-50/40">
                    <div class="w-8 h-8 rounded-xl bg-amber-100 text-amber-600 flex items-center justify-center shrink-0 font-bold text-xs mt-0.5">
                        📦
                    </div>
                    <div class="flex-1 min-w-0">
                        <p class="text-xs font-bold text-slate-800 truncate">Vérification du stock</p>
                        <p class="text-[11px] text-slate-400 mt-0.5">Actualisation des inventaires</p>
                    </div>
                    <span class="text-[10px] font-medium text-slate-400 shrink-0">Aujourd'hui</span>
                </div>

                <!-- Élément 2 -->
                <div class="p-3.5 rounded-2xl bg-slate-50 border border-slate-200/60 flex items-start gap-3 transition hover:bg-amber-50/40">
                    <div class="w-8 h-8 rounded-xl bg-indigo-100 text-indigo-600 flex items-center justify-center shrink-0 font-bold text-xs mt-0.5">
                        🏷️
                    </div>
                    <div class="flex-1 min-w-0">
                        <p class="text-xs font-bold text-slate-800 truncate">Gestion des catégories</p>
                        <p class="text-[11px] text-slate-400 mt-0.5">{{ $totalCategories }} catégories actives</p>
                    </div>
                    <span class="text-[10px] font-medium text-slate-400 shrink-0">Récent</span>
                </div>

                <!-- Élément 3 -->
                <div class="p-3.5 rounded-2xl bg-slate-50 border border-slate-200/60 flex items-start gap-3 transition hover:bg-amber-50/40">
                    <div class="w-8 h-8 rounded-xl bg-emerald-100 text-emerald-600 flex items-center justify-center shrink-0 font-bold text-xs mt-0.5">
                        ✨
                    </div>
                    <div class="flex-1 min-w-0">
                        <p class="text-xs font-bold text-slate-800 truncate">Session administrateur</p>
                        <p class="text-[11px] text-slate-400 mt-0.5">Connecté en tant que {{ Auth::user()->role ?? 'Admin' }}</p>
                    </div>
                    <span class="text-[10px] font-medium text-emerald-600 shrink-0 font-bold">Actif</span>
                </div>
            </div>
        </div>

        <!-- Bloc d'aide / Raccourci rapide additionnel -->
        <div class="bg-gradient-to-br from-slate-900 to-slate-800 rounded-3xl p-6 text-white shadow-2xl shadow-slate-900/40">
            <div class="w-10 h-10 rounded-2xl bg-white/10 flex items-center justify-center mb-4 text-amber-400">
                <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
            </div>
            <h3 class="text-sm font-black tracking-tight mb-1">Besoin d'assistance ?</h3>
            <p class="text-xs text-slate-300 leading-relaxed font-medium mb-4">
                Consultez la liste de vos fournisseurs ou ajoutez rapidement de nouveaux articles à votre catalogue.
            </p>
            <a href="{{ route('suppliers.index') }}" class="inline-flex items-center gap-2 text-xs font-bold text-amber-400 hover:text-amber-300 transition">
                <span>Accéder aux fournisseurs</span>
                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M14 5l7 7m0 0l-7 7m7-7H3" />
                </svg>
            </a>
        </div>

    </div>

</div>

@endsection