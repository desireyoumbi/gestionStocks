<!DOCTYPE html>
<html lang="fr" class="dark">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ Auth::user()->boutique->nom ?? 'E-Stockly' }}</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = { darkMode: 'class', }
    </script>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    @php
        // Récupération de la couleur personnalisée de la boutique (ou orange/amber par défaut)
        $primaryColor = Auth::user()->boutique->primary_color ?? '#f59e0b';
    @endphp
    <style>
        :root {
            --primary: {{ $primaryColor }};
        }
        .bg-custom-primary { background-color: var(--primary); }
        .text-custom-primary { color: var(--primary); }
        .border-custom-primary { border-color: var(--primary); }
        .no-scrollbar::-webkit-scrollbar { display: none; }
        .no-scrollbar { -ms-overflow-style: none; scrollbar-width: none; }

        /* --- CORRECTION GLOBALE DU MODE SOMBRE POUR LES FORMULAIRES --- */
        html.dark input:not([type="checkbox"]):not([type="radio"]):not([type="submit"]):not([type="button"]),
        html.dark select,
        html.dark textarea {
            background-color: #111827 !important; /* bg-gray-900 */
            color: #f9fafb !important;            /* text-gray-50 */
            border-color: #374151 !important;     /* border-gray-700 */
        }

        /* Correction des placeholders en mode dark */
        html.dark input::placeholder,
        html.dark textarea::placeholder {
            color: #9ca3af !important;            /* text-gray-400 */
        }
    </style>
</head>
<body class="bg-amber-100/50 dark:bg-gray-900 font-sans antialiased text-gray-800 dark:text-gray-100 transition-colors duration-200">

    <div class="min-h-screen p-2 sm:p-4 flex justify-center items-start overflow-hidden">
        <div class="w-full max-w-7xl h-[calc(100vh-1rem)] sm:h-[calc(100vh-2rem)] bg-gray-950 rounded-3xl shadow-2xl overflow-hidden flex flex-col lg:flex-row relative">

            <!-- OVERLAY MOBILE POUR FERMER LE MENU -->
            <div id="sidebar-overlay" class="fixed inset-0 bg-black/60 backdrop-blur-sm z-40 hidden lg:hidden transition-opacity"></div>

            <!-- SIDEBAR GAUCHE (Responsive Drawer + Desktop Sticky) -->
            <aside id="app-sidebar" class="fixed inset-y-0 left-0 z-50 w-72 bg-gray-950 text-gray-400 flex flex-col h-full border-r border-gray-900 transform -translate-x-full transition-transform duration-300 ease-in-out lg:translate-x-0 lg:static lg:inset-auto shrink-0">
                
                <!-- 1. HAUT FIXE (Logo + Nom + Bouton Fermeture Mobile) -->
                <div class="p-5 shrink-0 flex items-center justify-between">
                    @php
                        $boutique = Auth::user()->boutique ?? null;
                        $shopName = $boutique->nom ?? 'E-Stockly';
                        $words = explode(' ', trim($shopName));
                        $initials = count($words) >= 2 ? strtoupper(substr($words[0], 0, 1) . substr($words[1], 0, 1)) : strtoupper(substr($shopName, 0, 2));
                    @endphp
                    <div class="flex items-center space-x-3 px-1 truncate">
                        @if(!empty($boutique->logo))
                            <img src="{{ asset('storage/' . $boutique->logo) }}" alt="Logo" class="w-10 h-10 rounded-xl object-cover border border-gray-800 shrink-0 shadow-md">
                        @else
                            <div class="bg-amber-500 text-gray-900 w-10 h-10 rounded-xl font-black flex items-center justify-center text-sm shrink-0 shadow-md" style="background-color: var(--primary);">
                                {{ $initials }}
                            </div>
                        @endif
                        <span class="text-white font-bold text-base tracking-wide truncate" title="{{ $shopName }}">{{ $shopName }}</span>
                    </div>

                    <!-- Bouton Croix pour fermer sur mobile -->
                    <button id="sidebar-close" class="lg:hidden p-2 text-gray-400 hover:text-white rounded-xl hover:bg-gray-900 transition">
                        <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/>
                        </svg>
                    </button>
                </div>

                <!-- 2. NAVIGATION SCROLLABLE -->
                <div class="flex-1 overflow-y-auto no-scrollbar px-5 py-2">
                    <nav class="space-y-1.5">
                        <!-- 1. Tableau de bord -->
                        <a href="{{ route('dashboard') }}" class="flex items-center gap-3.5 px-3.5 py-2.5 rounded-xl transition text-sm font-medium {{ request()->routeIs('dashboard') ? 'bg-amber-500 text-gray-900 font-semibold shadow-lg shadow-amber-500/20' : 'text-gray-400 hover:bg-gray-800 hover:text-white' }}" @if(request()->routeIs('dashboard')) style="background-color: var(--primary);" @endif>
                            <svg class="w-5 h-5 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M3 13h8V3H3v10zm0 8h8v-6H3v6zm10 0h8V11h-8v10zm0-18v6h8V3h-8z" /></svg>
                            Tableau de bord
                        </a>

                        <!-- 2. Catégories -->
                        <a href="{{ route('categories.index') }}" class="flex items-center gap-3.5 px-3.5 py-2.5 rounded-xl transition text-sm font-medium {{ request()->routeIs('categories*') ? 'bg-amber-500 text-gray-900 font-semibold shadow-lg shadow-amber-500/20' : 'text-gray-400 hover:bg-gray-800 hover:text-white' }}" @if(request()->routeIs('categories*')) style="background-color: var(--primary);" @endif>
                            <svg class="w-5 h-5 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M4 6h16M4 10h16M4 14h16M4 18h16" /></svg>
                            Catégories
                        </a>

                        <!-- 3. Produits -->
                        <a href="{{ route('products.index') }}" class="flex items-center gap-3.5 px-3.5 py-2.5 rounded-xl transition text-sm font-medium {{ request()->routeIs('products*') ? 'bg-amber-500 text-gray-900 font-semibold shadow-lg shadow-amber-500/20' : 'text-gray-400 hover:bg-gray-800 hover:text-white' }}" @if(request()->routeIs('products*')) style="background-color: var(--primary);" @endif>
                            <svg class="w-5 h-5 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4" /></svg>
                            Produits
                        </a>

                        <!-- 4. Approvisionnements -->
                        <a href="{{ route('supplies.index') }}" class="flex items-center gap-3.5 px-3.5 py-2.5 rounded-xl transition text-sm font-medium {{ request()->routeIs('supplies*') ? 'bg-amber-500 text-gray-900 font-semibold shadow-lg shadow-amber-500/20' : 'text-gray-400 hover:bg-gray-800 hover:text-white' }}" @if(request()->routeIs('supplies*')) style="background-color: var(--primary);" @endif>
                            <svg class="w-5 h-5 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4" /></svg>
                            Approvisionnements
                        </a>

                        <!-- 5. Commandes -->
                        <a href="{{ route('purchase_orders.index') }}" class="flex items-center gap-3.5 px-3.5 py-2.5 rounded-xl transition text-sm font-medium {{ request()->routeIs('purchase_orders*') ? 'bg-amber-500 text-gray-900 font-semibold shadow-lg shadow-amber-500/20' : 'text-gray-400 hover:bg-gray-800 hover:text-white' }}" @if(request()->routeIs('purchase_orders*')) style="background-color: var(--primary);" @endif>
                            <svg class="w-5 h-5 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" /></svg>
                            Commandes
                        </a>

                        <!-- 6. Ventes -->
                        <a href="{{ route('sales.index') }}" class="flex items-center gap-3.5 px-3.5 py-2.5 rounded-xl transition text-sm font-medium {{ request()->routeIs('sales*') ? 'bg-amber-500 text-gray-900 font-semibold shadow-lg shadow-amber-500/20' : 'text-gray-400 hover:bg-gray-800 hover:text-white' }}" @if(request()->routeIs('sales*')) style="background-color: var(--primary);" @endif>
                            <svg class="w-5 h-5 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z" /></svg>
                            Ventes
                        </a>

                        <!-- 7. Ajustements -->
                        <a href="{{ route('adjustments.index') }}" class="flex items-center gap-3.5 px-3.5 py-2.5 rounded-xl transition text-sm font-medium {{ request()->routeIs('adjustments*') ? 'bg-amber-500 text-gray-900 font-semibold shadow-lg shadow-amber-500/20' : 'text-gray-400 hover:bg-gray-800 hover:text-white' }}" @if(request()->routeIs('adjustments*')) style="background-color: var(--primary);" @endif>
                            <svg class="w-5 h-5 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" /></svg>
                            Ajustements
                        </a>

                        <!-- 8. Mouvements -->
                        <a href="{{ route('movements.index') }}" class="flex items-center gap-3.5 px-3.5 py-2.5 rounded-xl transition text-sm font-medium {{ request()->routeIs('movements*') ? 'bg-amber-500 text-gray-900 font-semibold shadow-lg shadow-amber-500/20' : 'text-gray-400 hover:bg-gray-800 hover:text-white' }}" @if(request()->routeIs('movements*')) style="background-color: var(--primary);" @endif>
                            <svg class="w-5 h-5 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4" /></svg>
                            Mouvements
                        </a>

                        <!-- 9. Dépenses (Masqué pour le vendeur) -->
                        @if(Auth::user()->role !== 'vendeur')
                            <a href="{{ route('expenses.index') }}" class="flex items-center gap-3.5 px-3.5 py-2.5 rounded-xl transition text-sm font-medium {{ request()->routeIs('expenses*') ? 'bg-amber-500 text-gray-900 font-semibold shadow-lg shadow-amber-500/20' : 'text-gray-400 hover:bg-gray-800 hover:text-white' }}" @if(request()->routeIs('expenses*')) style="background-color: var(--primary);" @endif>
                                <svg class="w-5 h-5 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z" /></svg>
                                Dépenses
                            </a>
                        @endif

                        <!-- 10. Historique (Masqué pour le vendeur) -->
                        @if(Auth::user()->role !== 'vendeur')
                            <a href="{{ route('stock-movements.index') }}" class="flex items-center gap-3.5 px-3.5 py-2.5 rounded-xl transition text-sm font-medium {{ request()->routeIs('stock-movements*') ? 'bg-amber-500 text-gray-900 font-semibold shadow-lg shadow-amber-500/20' : 'text-gray-400 hover:bg-gray-800 hover:text-white' }}" @if(request()->routeIs('stock-movements*')) style="background-color: var(--primary);" @endif>
                                <svg class="w-5 h-5 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                                Historique
                            </a>
                        @endif

                        <!-- 11. Utilisateurs (Visible uniquement pour l'administrateur) -->
                        @if(Auth::user()->role === 'administrateur')
                            <a href="{{ route('user.index') }}" class="flex items-center gap-3.5 px-3.5 py-2.5 rounded-xl transition text-sm font-medium {{ request()->routeIs('user*') ? 'bg-amber-500 text-gray-900 font-semibold shadow-lg shadow-amber-500/20' : 'text-gray-400 hover:bg-gray-800 hover:text-white' }}" @if(request()->routeIs('user*')) style="background-color: var(--primary);" @endif>
                                <svg class="w-5 h-5 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" /></svg>
                                Utilisateurs
                            </a>
                        @endif

                        <!-- 12. Fournisseurs (Masqué pour le vendeur) -->
                        @if(Auth::user()->role !== 'vendeur')
                            <a href="{{ route('suppliers.index') }}" class="flex items-center gap-3.5 px-3.5 py-2.5 rounded-xl transition text-sm font-medium {{ request()->routeIs('suppliers*') ? 'bg-amber-500 text-gray-900 font-semibold shadow-lg shadow-amber-500/20' : 'text-gray-400 hover:bg-gray-800 hover:text-white' }}" @if(request()->routeIs('suppliers*')) style="background-color: var(--primary);" @endif>
                                <svg class="w-5 h-5 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" /></svg>
                                Fournisseurs
                            </a>
                        @endif

                        <!-- 13. Paramètres -->
                        @if(Auth::user()->role !== 'vendeur')
                            <a href="{{ route('settings.boutique') }}" class="flex items-center gap-3.5 px-3.5 py-2.5 rounded-xl transition text-sm font-medium {{ request()->routeIs('settings*') ? 'bg-amber-500 text-gray-900 font-semibold shadow-lg shadow-amber-500/20' : 'text-gray-400 hover:bg-gray-800 hover:text-white' }}" @if(request()->routeIs('settings*')) style="background-color: var(--primary);" @endif>
                                <svg class="w-5 h-5 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M11.983 2.5a1.5 1.5 0 00-1.414 1.05l-.26 1.042a8.01 8.01 0 00-1.57.906l-.976-.462a1.5 1.5 0 00-1.815.534l-1.5 2.598a1.5 1.5 0 00.27 1.834l.79.728a7.928 7.928 0 000 1.812l-.79.728a1.5 1.5 0 00-.27 1.834l1.5 2.598a1.5 1.5 0 001.815.534l.976-.462a8.01 8.01 0 001.57.906l.26 1.042a1.5 1.5 0 001.414 1.05h3.034a1.5 1.5 0 001.414-1.05l.26-1.042a8.01 8.01 0 001.57-.906l.976.462a1.5 1.5 0 001.815-.534l1.5-2.598a1.5 1.5 0 00-.27-1.834l-.79-.728a7.928 7.928 0 000-1.812l.79-.728a1.5 1.5 0 00.27-1.834l-1.5-2.598a1.5 1.5 0 00-1.815-.534l-.976.462a8.01 8.01 0 00-1.57-.906l-.26-1.042a1.5 1.5 0 00-1.414-1.05h-3.034z" />
                                    <circle cx="12" cy="12" r="3" />
                                </svg>
                                Paramètres
                            </a>
                        @endif

                        <!-- 14. Corbeille -->
                        @if(Auth::user()->role === 'administrateur')
                            <a href="{{ route('trash.index') }}" class="flex items-center gap-3.5 px-3.5 py-2.5 rounded-xl transition text-sm font-medium {{ request()->routeIs('trash*') ? 'bg-amber-500 text-gray-900 font-semibold shadow-lg shadow-amber-500/20' : 'text-gray-400 hover:bg-gray-800 hover:text-white' }}" @if(request()->routeIs('trash*')) style="background-color: var(--primary);" @endif>
                                <svg class="w-5 h-5 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" /></svg>
                                Corbeille
                            </a>
                        @endif
                    </nav>
                </div>

                <!-- 3. BAS FIXE (Actions) -->
                <div class="p-5 shrink-0 border-t border-gray-900 bg-gray-950">
                    <div class="flex items-center bg-gray-900 rounded-xl border border-gray-800 p-1 mb-3 text-sm font-medium shadow-inner">
                        <button type="button" id="theme-light-btn" onclick="setTheme('light')" class="flex-1 flex items-center justify-center gap-2 py-1.5 rounded-lg transition-all duration-150 text-xs text-gray-400 hover:text-white">
                            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364 6.364l-.707-.707M6.343 6.343l-.707-.707m12.728 0l-.707.707M6.343 17.657l-.707.707M16 12a4 4 0 11-8 0 4 4 0 018 0z" /></svg> Clair
                        </button>
                        <button type="button" id="theme-dark-btn" onclick="setTheme('dark')" class="flex-1 flex items-center justify-center gap-2 py-1.5 rounded-lg transition-all duration-150 text-xs text-gray-400 hover:text-white">
                            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z" /></svg> Sombre
                        </button>
                    </div>
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit" class="w-full flex items-center justify-center gap-2 px-3.5 py-2 rounded-xl text-xs font-medium text-red-400 hover:bg-red-500/15 transition shadow-sm"><svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" /></svg> Déconnexion</button>
                    </form>
                </div>
            </aside>

            <!-- MAIN CONTENT -->
            <main class="flex-1 bg-gray-50 dark:bg-gray-900 rounded-none lg:rounded-l-3xl flex flex-col h-full overflow-hidden transition-colors duration-200">
                <header class="bg-white dark:bg-gray-950 border-b border-gray-100 dark:border-gray-800 px-4 sm:px-6 py-4 flex items-center justify-between shadow-sm shrink-0">
                    
                    <div class="flex items-center gap-3">
                        <!-- BOUTON HAMBURGER (Visible uniquement sur mobile / petits écrans) -->
                        <button id="hamburger-btn" class="lg:hidden p-2 rounded-xl bg-gray-100 dark:bg-gray-900 text-gray-600 dark:text-gray-300 hover:text-amber-500 transition focus:outline-none" title="Ouvrir le menu">
                            <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M4 6h16M4 12h16M4 18h16"/>
                            </svg>
                        </button>

                        <div>
                            <span class="block text-[10px] font-bold uppercase tracking-wider text-gray-400 dark:text-gray-500">@yield('header_subtitle', 'Espace de Gestion')</span>
                            <h1 class="text-base font-bold text-gray-900 dark:text-white">@yield('header_title', 'Tableau de bord')</h1>
                        </div>
                    </div>

                    <!-- DROITE DU HEADER : Scan, Notifications & Profil -->
                    <div class="flex items-center gap-3">
                        @php
                            $unreadCount = \App\Models\Notification::where('user_id', Auth::id())->whereNull('read_at')->count();
                            $outOfStockCount = \App\Models\Product::where('boutique_id', Auth::user()->boutique_id ?? null)->where('quantity', 0)->count();
                        @endphp

                        <!-- Bouton d'accès rapide au Scan/Douchette -->
                        <a href="{{ route('stock.scan.index') }}" class="flex items-center gap-2 px-3.5 py-2 rounded-xl bg-amber-500 text-gray-900 hover:bg-amber-400 transition text-xs font-bold shadow-md shadow-amber-500/10" style="background-color: var(--primary);" title="Scan rapide / Mode Douchette">
                            <svg class="w-4 h-4 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v1m6 11h2m-6 0h-2v4m0-11v3m0 0h.01M12 12h4.01M16 20h4M4 12h4m12 0h.01M5 8h2a1 1 0 001-1V5a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1zm12 0h2a1 1 0 001-1V5a1 1 0 00-1-1h-2a1 1 0 00-1 1v2a1 1 0 001 1zM5 20h2a1 1 0 001-1v-2a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1z" />
                            </svg>
                            <span class="hidden md:inline">Scan & Stock</span>
                        </a>

                        @if($outOfStockCount > 0)
                            <button id="force-audio-btn" onclick="playStockAlertSound(true)" class="hidden sm:flex items-center gap-1.5 px-3 py-1.5 rounded-xl bg-rose-500/10 border border-rose-500/30 text-rose-500 text-xs font-bold animate-pulse hover:bg-rose-500/20 transition" title="Cliquez pour rejouer l'alerte sonore">
                                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                                Alerte Stock ({{ $outOfStockCount }})
                            </button>
                        @endif

                        <!-- Icône de notification -->
                        <a href="{{ route('notifications.index') }}" class="p-2.5 rounded-xl bg-gray-50 dark:bg-gray-900 border border-gray-200 dark:border-gray-800 text-gray-400 hover:text-gray-600 dark:hover:text-white hover:bg-gray-100 dark:hover:bg-gray-800 transition shadow-sm relative" title="Notifications">
                            <svg class="w-4 h-4 {{ $unreadCount > 0 ? 'text-rose-500 animate-bounce' : '' }}" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9" />
                            </svg>

                            @if($unreadCount > 0)
                                <span class="absolute -top-1 -right-1 flex h-3.5 w-3.5">
                                    <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-rose-400 opacity-75"></span>
                                    <span class="relative inline-flex rounded-full h-3.5 w-3.5 bg-rose-500"></span>
                                </span>
                            @endif
                        </a>

                        <!-- Menu déroulant Profil -->
                        <div class="relative" x-data="{ open: false }">
                            <button @click="open = !open" class="flex items-center gap-3 py-1.5 px-3 rounded-2xl bg-gray-50 dark:bg-gray-900 border border-gray-200 dark:border-gray-800 hover:border-amber-500/50 transition shadow-sm focus:outline-none">
                                <div class="w-8 h-8 rounded-xl bg-amber-500/10 text-amber-500 font-bold flex items-center justify-center text-xs border border-amber-500/20" style="color: var(--primary); background-color: color-mix(in srgb, var(--primary) 10%, transparent); border-color: color-mix(in srgb, var(--primary) 20%, transparent);">
                                    {{ strtoupper(substr(Auth::user()->name ?? 'A', 0, 2)) }}
                                </div>
                                <div class="hidden sm:block text-left">
                                    <span class="block text-xs font-bold text-gray-900 dark:text-white leading-tight">{{ Auth::user()->name ?? 'Utilisateur' }}</span>
                                    <span class="block text-[10px] text-gray-400 leading-tight">Connecté</span>
                                </div>
                                <svg class="w-4 h-4 text-gray-400 transition-transform duration-200" :class="{ 'rotate-180': open }" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7" />
                                </svg>
                            </button>

                            <div x-show="open" @click.away="open = false" x-transition:enter="transition ease-out duration-100" x-transition:enter-start="transform opacity-0 scale-95" x-transition:enter-end="transform opacity-100 scale-100" x-transition:leave="transition ease-in duration-75" x-transition:leave-start="transform opacity-100 scale-100" x-transition:leave-end="transform opacity-0 scale-95" class="absolute right-0 mt-2 w-48 bg-white dark:bg-gray-950 border border-gray-100 dark:border-gray-800 rounded-2xl shadow-xl py-1.5 z-50" style="display: none;">
                                <a href="{{ route('profile.edit') }}" class="flex items-center gap-2.5 px-4 py-2 text-xs font-medium text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-900 transition">
                                    <svg class="w-4 h-4 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" /></svg>
                                    Mon Profil
                                </a>
                                @if(Auth::user()->role !== 'vendeur')
                                    <a href="{{ route('settings.boutique') }}" class="flex items-center gap-2.5 px-4 py-2 text-xs font-medium text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-900 transition">
                                        <svg class="w-4 h-4 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M11.983 2.5a1.5 1.5 0 00-1.414 1.05l-.26 1.042a8.01 8.01 0 00-1.57.906l-.976-.462a1.5 1.5 0 00-1.815.534l-1.5 2.598a1.5 1.5 0 00.27 1.834l.79.728a7.928 7.928 0 000 1.812l-.79.728a1.5 1.5 0 00-.27 1.834l1.5 2.598a1.5 1.5 0 001.815.534l.976-.462a8.01 8.01 0 001.57.906l.26 1.042a1.5 1.5 0 001.414 1.05h3.034a1.5 1.5 0 001.414-1.05l.26-1.042a8.01 8.01 0 001.57-.906l.976.462a1.5 1.5 0 001.815-.534l1.5-2.598a1.5 1.5 0 00-.27-1.834l-.79-.728a7.928 7.928 0 000-1.812l.79-.728a1.5 1.5 0 00.27-1.834l-1.5-2.598a1.5 1.5 0 00-1.815-.534l-.976.462a8.01 8.01 0 00-1.57-.906l-.26-1.042a1.5 1.5 0 00-1.414-1.05h-3.034z" />
                                            <circle cx="12" cy="12" r="3" />
                                        </svg>
                                        Paramètres
                                    </a>
                                @endif
                                <div class="h-px bg-gray-100 dark:bg-gray-900 my-1"></div>
                                <form method="POST" action="{{ route('logout') }}">
                                    @csrf
                                    <button type="submit" class="w-full flex items-center gap-2.5 px-4 py-2 text-xs font-medium text-rose-500 hover:bg-rose-500/10 transition">
                                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" /></svg>
                                        Déconnexion
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                </header>

                <!-- BLOC DES MESSAGES FLASH -->
                <div class="px-4 sm:px-6 pt-4 shrink-0">
                    @if(session('success'))
                        <div class="flex items-center gap-3 p-4 rounded-2xl bg-emerald-500/10 border border-emerald-500/20 text-emerald-400 text-xs font-semibold shadow-sm">
                            <svg class="w-5 h-5 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                            <span>{{ session('success') }}</span>
                        </div>
                    @endif

                    @if(session('error'))
                        <div class="flex items-center gap-3 p-4 rounded-2xl bg-rose-500/10 border border-rose-500/20 text-rose-400 text-xs font-semibold shadow-sm">
                            <svg class="w-5 h-5 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                            <span>{{ session('error') }}</span>
                        </div>
                    @endif
                </div>

                <div class="flex-1 overflow-y-auto p-4 sm:p-6 lg:p-8">
                    @yield('content')
                </div>
            </main>
        </div>
    </div>

    <!-- POPUP MODALE : Code de connexion unique -->
    @if(session('new_user_code'))
        <div id="codeModal" class="fixed inset-0 z-50 flex items-center justify-center bg-black/70 backdrop-blur-sm p-4">
            <div class="bg-gray-900 border border-gray-800 rounded-3xl shadow-2xl p-6 sm:p-8 max-w-md w-full mx-auto transform transition-all space-y-5 text-center">
                
                <div class="flex items-center justify-center w-14 h-14 mx-auto bg-amber-500/10 text-amber-500 rounded-2xl border border-amber-500/20 shadow-inner">
                    <svg class="w-7 h-7" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                    </svg>
                </div>

                <div class="space-y-2">
                    <h3 class="text-lg font-bold text-white">⚠️ TRÈS IMPORTANT</h3>
                    <p class="text-xs text-gray-400">Voici le code d'accès unique généré pour le nouvel utilisateur. Notez-le bien, il ne sera plus affiché.</p>
                </div>

                <div class="p-4 bg-gray-950 border border-gray-800 rounded-2xl font-mono text-xl font-bold tracking-widest text-amber-500" style="color: var(--primary);">
                    {{ session('new_user_code') }}
                </div>

                @if(session('new_user_id'))
                    <form method="POST" action="{{ route('user.envoyer-identifiants-email', session('new_user_id')) }}">
                        @csrf
                        <button type="submit" class="w-full py-2.5 bg-gray-800 hover:bg-gray-700 text-white font-bold text-xs rounded-xl transition border border-gray-700 flex items-center justify-center gap-2">
                            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                            </svg>
                            Envoyer par email
                        </button>
                    </form>
                @endif

                <button onclick="document.getElementById('codeModal').remove()" class="w-full py-2.5 bg-amber-500 hover:bg-amber-400 text-gray-900 font-bold text-xs rounded-xl transition shadow-lg shadow-amber-500/20" style="background-color: var(--primary);">
                    J'ai bien noté le code
                </button>
            </div>
        </div>
    @endif

    <script>
        // Fonction globale pour basculer le thème
        function setTheme(theme) {
            const html = document.documentElement;
            const lightBtn = document.getElementById('theme-light-btn');
            const darkBtn = document.getElementById('theme-dark-btn');

            if (theme === 'dark') {
                html.classList.add('dark');
                localStorage.setItem('theme', 'dark');
                
                if (lightBtn && darkBtn) {
                    lightBtn.className = "flex-1 flex items-center justify-center gap-2 py-1.5 rounded-lg transition-all duration-150 text-xs text-gray-400 hover:text-white";
                    darkBtn.className = "flex-1 flex items-center justify-center gap-2 py-1.5 rounded-lg transition-all duration-150 text-xs bg-gray-800 text-white font-semibold shadow-sm";
                }
            } else {
                html.classList.remove('dark');
                localStorage.setItem('theme', 'light');
                
                if (lightBtn && darkBtn) {
                    lightBtn.className = "flex-1 flex items-center justify-center gap-2 py-1.5 rounded-lg transition-all duration-150 text-xs bg-white text-gray-900 font-semibold shadow-sm";
                    darkBtn.className = "flex-1 flex items-center justify-center gap-2 py-1.5 rounded-lg transition-all duration-150 text-xs text-gray-400 hover:text-white";
                }
            }
        }

        // Initialisation du thème au chargement de la page selon le localStorage
        document.addEventListener('DOMContentLoaded', () => {
            const savedTheme = localStorage.getItem('theme') || 'dark';
            setTheme(savedTheme);

            // --- GESTION DE LA SIDEBAR MOBILE (menu hamburger) ---
            const sidebar = document.getElementById('app-sidebar');
            const overlay = document.getElementById('sidebar-overlay');
            const hamburgerBtn = document.getElementById('hamburger-btn');
            const closeBtn = document.getElementById('sidebar-close');

            function openSidebar() {
                sidebar.classList.remove('-translate-x-full');
                overlay.classList.remove('hidden');
            }

            function closeSidebar() {
                sidebar.classList.add('-translate-x-full');
                overlay.classList.add('hidden');
            }

            if (hamburgerBtn) hamburgerBtn.addEventListener('click', openSidebar);
            if (closeBtn) closeBtn.addEventListener('click', closeSidebar);
            if (overlay) overlay.addEventListener('click', closeSidebar);
        });
    </script>
</body>
</html>