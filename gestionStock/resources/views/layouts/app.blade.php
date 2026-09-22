
<!DOCTYPE html>

<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <!-- Appliquer le thème avant l'affichage pour éviter le flash -->
    <script>
        (function () {
            const theme = localStorage.getItem('theme') || 'dark';

            if (theme === 'dark') {
                document.documentElement.classList.add('dark');
            } else {
                document.documentElement.classList.remove('dark');
            }
        })();
    </script>

    <title>{{ Auth::user()->boutique->nom ?? 'E-Stockly' }}</title>

    <link rel="manifest" href="/manifest.json">

    <meta name="theme-color" content="#0a1a3a">

    <link rel="apple-touch-icon" href="/icons/icon-192.png">

    <meta name="apple-mobile-web-app-capable" content="yes">

    <script src="https://cdn.tailwindcss.com"></script>

    <script>
        tailwind.config = {
            darkMode: 'class',
        }
    </script>

    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>

    @php
        $boutique = Auth::user()->boutique ?? null;
        $primaryColor = $boutique->primary_color ?? '#f59e0b';
    @endphp

    <style>
        :root {
            --primary: {{ $primaryColor }};
        }

        .bg-custom-primary {
            background-color: var(--primary);
        }

        .text-custom-primary {
            color: var(--primary);
        }

        .border-custom-primary {
            border-color: var(--primary);
        }

        .no-scrollbar::-webkit-scrollbar {
            display: none;
        }

        .no-scrollbar {
            -ms-overflow-style: none;
            scrollbar-width: none;
        }

        /* Champs en mode sombre */
        html.dark input:not([type="checkbox"]):not([type="radio"]):not([type="submit"]):not([type="button"]),
        html.dark select,
        html.dark textarea {
            background-color: #111827 !important;
            color: #f9fafb !important;
            border-color: #374151 !important;
        }

        html.dark input::placeholder,
        html.dark textarea::placeholder {
            color: #9ca3af !important;
        }

        /* Champs en mode clair */
        html:not(.dark) input:not([type="checkbox"]):not([type="radio"]):not([type="submit"]):not([type="button"]),
        html:not(.dark) select,
        html:not(.dark) textarea {
            background-color: #ffffff;
            color: #111827;
            border-color: #d1d5db;
        }

        html:not(.dark) input::placeholder,
        html:not(.dark) textarea::placeholder {
            color: #9ca3af;
        }
    </style>

</head>

<body class="bg-amber-100/50 dark:bg-gray-900 font-sans antialiased text-gray-800 dark:text-gray-100 transition-colors duration-200">

<div class="min-h-screen p-2 sm:p-4 flex justify-center items-start overflow-hidden">

    <div class="w-full max-w-7xl h-[calc(100vh-1rem)] sm:h-[calc(100vh-2rem)] bg-white dark:bg-gray-950 rounded-3xl shadow-2xl overflow-hidden flex flex-col lg:flex-row relative transition-colors duration-200">

        <!-- OVERLAY MOBILE -->
        <div id="sidebar-overlay"
             class="fixed inset-0 bg-black/60 backdrop-blur-sm z-40 hidden lg:hidden transition-opacity">
        </div>

        <!-- SIDEBAR -->
        <aside id="app-sidebar"
               class="fixed inset-y-0 left-0 z-50 w-72 bg-white dark:bg-gray-950 text-gray-700 dark:text-gray-400 flex flex-col h-full border-r border-gray-200 dark:border-gray-900 transform -translate-x-full transition-all duration-300 ease-in-out lg:translate-x-0 lg:static lg:inset-auto shrink-0">

            <!-- HAUT -->
            <div class="p-5 shrink-0 flex items-center justify-between">

                @php
                    $shopName = $boutique->nom ?? 'E-Stockly';

                    $words = explode(' ', trim($shopName));

                    $initials = count($words) >= 2
                        ? strtoupper(
                            substr($words[0], 0, 1) .
                            substr($words[1], 0, 1)
                        )
                        : strtoupper(substr($shopName, 0, 2));

                    $logoUrl = null;

                    if (!empty($boutique->logo)) {
                        $logoUrl = asset(
                            'storage/' . ltrim($boutique->logo, '/')
                        );
                    }
                @endphp

                <div class="flex items-center space-x-3 px-1 truncate">

                    @if(!empty($boutique->logo)

                        <img src="{{ $logoUrl }}"
                             alt="Logo de {{ $shopName }}"
                             class="w-10 h-10 rounded-xl object-cover border border-gray-200 dark:border-gray-800 shrink-0 shadow-md">

                    @else

                        <div class="text-gray-900 w-10 h-10 rounded-xl font-black flex items-center justify-center text-sm shrink-0 shadow-md"
                             style="background-color: var(--primary);">

                            {{ $initials }}

                        </div>

                    @endif

                    <span class="text-gray-900 dark:text-white font-bold text-base tracking-wide truncate"
                          title="{{ $shopName }}">

                        {{ $shopName }}

                    </span>

                </div>

                <!-- Fermeture mobile -->
                <button id="sidebar-close"
                        class="lg:hidden p-2 text-gray-500 dark:text-gray-400 hover:text-gray-900 dark:hover:text-white rounded-xl hover:bg-gray-100 dark:hover:bg-gray-900 transition">

                    <svg class="w-6 h-6"
                         fill="none"
                         viewBox="0 0 24 24"
                         stroke="currentColor"
                         stroke-width="2">

                        <path stroke-linecap="round"
                              stroke-linejoin="round"
                              d="M6 18L18 6M6 6l12 12"/>

                    </svg>

                </button>

            </div>

            <!-- NAVIGATION -->
            <div class="flex-1 overflow-y-auto no-scrollbar px-5 py-2">

                <nav class="space-y-1.5">

                    <!-- Tableau de bord -->
                    <a href="{{ route('dashboard') }}"
                       class="flex items-center gap-3.5 px-3.5 py-2.5 rounded-xl transition text-sm font-medium {{ request()->routeIs('dashboard') ? 'bg-amber-500 text-gray-900 font-semibold shadow-lg shadow-amber-500/20' : 'text-gray-600 dark:text-gray-400 hover:bg-gray-100 dark:hover:bg-gray-800 hover:text-gray-900 dark:hover:text-white' }}"
                       @if(request()->routeIs('dashboard')) style="background-color: var(--primary);" @endif>

                        <svg class="w-5 h-5 shrink-0"
                             fill="none"
                             viewBox="0 0 24 24"
                             stroke="currentColor"
                             stroke-width="2">

                            <path stroke-linecap="round"
                                  stroke-linejoin="round"
                                  d="M3 13h8V3H3v10zm0 8h8v-6H3v6zm10 0h8V11h-8v10zm0-18v6h8V3h-8z" />

                        </svg>

                        Tableau de bord

                    </a>

                    <!-- Catégories -->
                    <a href="{{ route('categories.index') }}"
                       class="flex items-center gap-3.5 px-3.5 py-2.5 rounded-xl transition text-sm font-medium {{ request()->routeIs('categories*') ? 'bg-amber-500 text-gray-900 font-semibold shadow-lg shadow-amber-500/20' : 'text-gray-600 dark:text-gray-400 hover:bg-gray-100 dark:hover:bg-gray-800 hover:text-gray-900 dark:hover:text-white' }}"
                       @if(request()->routeIs('categories*')) style="background-color: var(--primary);" @endif>

                        <svg class="w-5 h-5 shrink-0"
                             fill="none"
                             viewBox="0 0 24 24"
                             stroke="currentColor"
                             stroke-width="2">

                            <path stroke-linecap="round"
                                  stroke-linejoin="round"
                                  d="M4 6h16M4 10h16M4 14h16M4 18h16" />

                        </svg>

                        Catégories

                    </a>

                    <!-- Produits -->
                    <a href="{{ route('products.index') }}"
                       class="flex items-center gap-3.5 px-3.5 py-2.5 rounded-xl transition text-sm font-medium {{ request()->routeIs('products*') ? 'bg-amber-500 text-gray-900 font-semibold shadow-lg shadow-amber-500/20' : 'text-gray-600 dark:text-gray-400 hover:bg-gray-100 dark:hover:bg-gray-800 hover:text-gray-900 dark:hover:text-white' }}"
                       @if(request()->routeIs('products*')) style="background-color: var(--primary);" @endif>

                        <svg class="w-5 h-5 shrink-0"
                             fill="none"
                             viewBox="0 0 24 24"
                             stroke="currentColor"
                             stroke-width="2">

                            <path stroke-linecap="round"
                                  stroke-linejoin="round"
                                  d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4" />

                        </svg>

                        Produits

                    </a>

                    <!-- Approvisionnements -->
                    <a href="{{ route('supplies.index') }}"
                       class="flex items-center gap-3.5 px-3.5 py-2.5 rounded-xl transition text-sm font-medium {{ request()->routeIs('supplies*') ? 'bg-amber-500 text-gray-900 font-semibold shadow-lg shadow-amber-500/20' : 'text-gray-600 dark:text-gray-400 hover:bg-gray-100 dark:hover:bg-gray-800 hover:text-gray-900 dark:hover:text-white' }}"
                       @if(request()->routeIs('supplies*')) style="background-color: var(--primary);" @endif>

                        <svg class="w-5 h-5 shrink-0"
                             fill="none"
                             viewBox="0 0 24 24"
                             stroke="currentColor"
                             stroke-width="2">

                            <path stroke-linecap="round"
                                  stroke-linejoin="round"
                                  d="M12 4v16m8-8H4" />

                        </svg>

                        Approvisionnements

                    </a>

                    <!-- Commandes -->
                    <a href="{{ route('purchase_orders.index') }}"
                       class="flex items-center gap-3.5 px-3.5 py-2.5 rounded-xl transition text-sm font-medium {{ request()->routeIs('purchase_orders*') ? 'bg-amber-500 text-gray-900 font-semibold shadow-lg shadow-amber-500/20' : 'text-gray-600 dark:text-gray-400 hover:bg-gray-100 dark:hover:bg-gray-800 hover:text-gray-900 dark:hover:text-white' }}"
                       @if(request()->routeIs('purchase_orders*')) style="background-color: var(--primary);" @endif>

                        <svg class="w-5 h-5 shrink-0"
                             fill="none"
                             viewBox="0 0 24 24"
                             stroke="currentColor"
                             stroke-width="2">

                            <path stroke-linecap="round"
                                  stroke-linejoin="round"
                                  d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />

                        </svg>

                        Commandes

                    </a>

                    <!-- Ventes -->
                    <a href="{{ route('sales.index') }}"
                       class="flex items-center gap-3.5 px-3.5 py-2.5 rounded-xl transition text-sm font-medium {{ request()->routeIs('sales*') ? 'bg-amber-500 text-gray-900 font-semibold shadow-lg shadow-amber-500/20' : 'text-gray-600 dark:text-gray-400 hover:bg-gray-100 dark:hover:bg-gray-800 hover:text-gray-900 dark:hover:text-white' }}"
                       @if(request()->routeIs('sales*')) style="background-color: var(--primary);" @endif>

                        <svg class="w-5 h-5 shrink-0"
                             fill="none"
                             viewBox="0 0 24 24"
                             stroke="currentColor"
                             stroke-width="2">

                            <path stroke-linecap="round"
                                  stroke-linejoin="round"
                                  d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z" />

                        </svg>

                        Ventes

                    </a>

                    <!-- Ajustements -->
                    <a href="{{ route('adjustments.index') }}"
                       class="flex items-center gap-3.5 px-3.5 py-2.5 rounded-xl transition text-sm font-medium {{ request()->routeIs('adjustments*') ? 'bg-amber-500 text-gray-900 font-semibold shadow-lg shadow-amber-500/20' : 'text-gray-600 dark:text-gray-400 hover:bg-gray-100 dark:hover:bg-gray-800 hover:text-gray-900 dark:hover:text-white' }}"
                       @if(request()->routeIs('adjustments*')) style="background-color: var(--primary);" @endif>

                        <svg class="w-5 h-5 shrink-0"
                             fill="none"
                             viewBox="0 0 24 24"
                             stroke="currentColor"
                             stroke-width="2">

                            <path stroke-linecap="round"
                                  stroke-linejoin="round"
                                  d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />

                        </svg>

                        Ajustements

                    </a>

                    <!-- Mouvements -->
                    <a href="{{ route('movements.index') }}"
                       class="flex items-center gap-3.5 px-3.5 py-2.5 rounded-xl transition text-sm font-medium {{ request()->routeIs('movements*') ? 'bg-amber-500 text-gray-900 font-semibold shadow-lg shadow-amber-500/20' : 'text-gray-600 dark:text-gray-400 hover:bg-gray-100 dark:hover:bg-gray-800 hover:text-gray-900 dark:hover:text-white' }}"
                       @if(request()->routeIs('movements*')) style="background-color: var(--primary);" @endif>

                        <svg class="w-5 h-5 shrink-0"
                             fill="none"
                             viewBox="0 0 24 24"
                             stroke="currentColor"
                             stroke-width="2">

                            <path stroke-linecap="round"
                                  stroke-linejoin="round"
                                  d="M8 7h12m0 0l-4-4m4 4l-4 4m-4 6H4m0 0l4 4m-4-4l4-4" />

                        </svg>

                        Mouvements

                    </a>

                    <!-- Dépenses -->
                    @if(Auth::user()->role !== 'vendeur')

                        <a href="{{ route('expenses.index') }}"
                           class="flex items-center gap-3.5 px-3.5 py-2.5 rounded-xl transition text-sm font-medium {{ request()->routeIs('expenses*') ? 'bg-amber-500 text-gray-900 font-semibold shadow-lg shadow-amber-500/20' : 'text-gray-600 dark:text-gray-400 hover:bg-gray-100 dark:hover:bg-gray-800 hover:text-gray-900 dark:hover:text-white' }}"
                           @if(request()->routeIs('expenses*')) style="background-color: var(--primary);" @endif>

                            <svg class="w-5 h-5 shrink-0"
                                 fill="none"
                                 viewBox="0 0 24 24"
                                 stroke="currentColor"
                                 stroke-width="2">

                                <path stroke-linecap="round"
                                      stroke-linejoin="round"
                                      d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6zm7-5a2 2 0 11-4 0 2 2 0 014 0z" />

                            </svg>

                            Dépenses

                        </a>

                    @endif

                    <!-- Historique -->
                    @if(Auth::user()->role !== 'vendeur')

                        <a href="{{ route('stock-movements.index') }}"
                           class="flex items-center gap-3.5 px-3.5 py-2.5 rounded-xl transition text-sm font-medium {{ request()->routeIs('stock-movements*') ? 'bg-amber-500 text-gray-900 font-semibold shadow-lg shadow-amber-500/20' : 'text-gray-600 dark:text-gray-400 hover:bg-gray-100 dark:hover:bg-gray-800 hover:text-gray-900 dark:hover:text-white' }}"
                           @if(request()->routeIs('stock-movements*')) style="background-color: var(--primary);" @endif>

                            <svg class="w-5 h-5 shrink-0"
                                 fill="none"
                                 viewBox="0 0 24 24"
                                 stroke="currentColor"
                                 stroke-width="2">

                                <path stroke-linecap="round"
                                      stroke-linejoin="round"
                                      d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />

                            </svg>

                            Historique

                        </a>

                    @endif

                    <!-- Utilisateurs -->
                    @if(Auth::user()->role === 'administrateur')

                        <a href="{{ route('user.index') }}"
                           class="flex items-center gap-3.5 px-3.5 py-2.5 rounded-xl transition text-sm font-medium {{ request()->routeIs('user*') ? 'bg-amber-500 text-gray-900 font-semibold shadow-lg shadow-amber-500/20' : 'text-gray-600 dark:text-gray-400 hover:bg-gray-100 dark:hover:bg-gray-800 hover:text-gray-900 dark:hover:text-white' }}"
                           @if(request()->routeIs('user*')) style="background-color: var(--primary);" @endif>

                            <svg class="w-5 h-5 shrink-0"
                                 fill="none"
                                 viewBox="0 0 24 24"
                                 stroke="currentColor"
                                 stroke-width="2">

                                <path stroke-linecap="round"
                                      stroke-linejoin="round"
                                      d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />

                            </svg>

                            Utilisateurs

                        </a>

                    @endif

                    <!-- Fournisseurs -->
                    @if(Auth::user()->role !== 'vendeur')

                        <a href="{{ route('suppliers.index') }}"
                           class="flex items-center gap-3.5 px-3.5 py-2.5 rounded-xl transition text-sm font-medium {{ request()->routeIs('suppliers*') ? 'bg-amber-500 text-gray-900 font-semibold shadow-lg shadow-amber-500/20' : 'text-gray-600 dark:text-gray-400 hover:bg-gray-100 dark:hover:bg-gray-800 hover:text-gray-900 dark:hover:text-white' }}"
                           @if(request()->routeIs('suppliers*')) style="background-color: var(--primary);" @endif>

                            <svg class="w-5 h-5 shrink-0"
                                 fill="none"
                                 viewBox="0 0 24 24"
                                 stroke="currentColor"
                                 stroke-width="2">

                                <path stroke-linecap="round"
                                      stroke-linejoin="round"
                                      d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />

                            </svg>

                            Fournisseurs

                        </a>

                    @endif

                    <!-- Paramètres -->
                    @if(Auth::user()->role !== 'vendeur')

                        <a href="{{ route('settings.boutique') }}"
                           class="flex items-center gap-3.5 px-3.5 py-2.5 rounded-xl transition text-sm font-medium {{ request()->routeIs('settings*') ? 'bg-amber-500 text-gray-900 font-semibold shadow-lg shadow-amber-500/20' : 'text-gray-600 dark:text-gray-400 hover:bg-gray-100 dark:hover:bg-gray-800 hover:text-gray-900 dark:hover:text-white' }}"
                           @if(request()->routeIs('settings*')) style="background-color: var(--primary);" @endif>

                            <svg class="w-5 h-5 shrink-0"
                                 fill="none"
                                 viewBox="0 0 24 24"
                                 stroke="currentColor"
                                 stroke-width="2">

                                <path stroke-linecap="round"
                                      stroke-linejoin="round"
                                      d="M11.983 2.5a1.5 1.5 0 00-1.414 1.05l-.26 1.042a8.01 8.01 0 00-1.57.906l-.976-.462a1.5 1.5 0 00-1.815.534l-.79.728a7.928 7.928 0 000 1.812l.79.728a1.5 1.5 0 00-.27 1.834l1.5 2.598a1.5 1.5 0 001.815.534l.976-.462a8.01 8.01 0 001.57.906l.26 1.042a1.5 1.5 0 001.414 1.05h3.034a1.5 1.5 0 001.414-1.05l.26-1.042a8.01 8.01 0 001.57-.906l.976.462a1.5 1.5 0 001.815-.534l1.5-2.598a1.5 1.5 0 00-.27-1.834l-.79-.728a7.928 7.928 0 000-1.812l.79-.728a1.5 1.5 0 00.27-1.834l-1.5-2.598a1.5 1.5 0 00-1.815-.534l-.976.462a8.01 8.01 0 00-1.57-.906l-.26-1.042a1.5 1.5 0 00-1.414-1.05h-3.034z" />

                                <circle cx="12" cy="12" r="3" />

                            </svg>

                            Paramètres

                        </a>

                    @endif

                    <!-- Corbeille -->
                    @if(in_array(Auth::user()->role, ['administrateur', 'vice_administrateur']))

                        <a href="{{ route('trash.index') }}"
                           class="flex items-center gap-3.5 px-3.5 py-2.5 rounded-xl transition text-sm font-medium {{ request()->routeIs('trash*') ? 'bg-amber-500 text-gray-900 font-semibold shadow-lg shadow-amber-500/20' : 'text-gray-600 dark:text-gray-400 hover:bg-gray-100 dark:hover:bg-gray-800 hover:text-gray-900 dark:hover:text-white' }}"
                           @if(request()->routeIs('trash*')) style="background-color: var(--primary);" @endif>

                            <svg class="w-5 h-5 shrink-0"
                                 fill="none"
                                 viewBox="0 0 24 24"
                                 stroke="currentColor"
                                 stroke-width="2">

                                <path stroke-linecap="round"
                                      stroke-linejoin="round"
                                      d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />

                            </svg>

                            Corbeille

                        </a>

                    @endif

                </nav>

            </div>

            <!-- BAS -->
            <div class="p-5 shrink-0 border-t border-gray-200 dark:border-gray-900 bg-white dark:bg-gray-950">

                <!-- INSTALLATION PWA -->
                <button id="pwa-sidebar-install-btn"
                        type="button"
                        onclick="installPwaApp()"
                        class="w-full flex items-center justify-center gap-2 px-3.5 py-2.5 mb-3 rounded-xl text-xs font-bold text-gray-900 transition shadow-md hover:opacity-90"
                        style="background-color: var(--primary);"
                        title="Installer E-Stockly">

                    <svg class="w-4 h-4 shrink-0"
                         fill="none"
                         viewBox="0 0 24 24"
                         stroke="currentColor"
                         stroke-width="2">

                        <path stroke-linecap="round"
                              stroke-linejoin="round"
                              d="M12 3v12m0 0l-4-4m4 4l4-4M5 21h14" />

                    </svg>

                    Installer l'application

                </button>

                <!-- THEME -->
                <div class="flex items-center bg-gray-100 dark:bg-gray-900 rounded-xl border border-gray-200 dark:border-gray-800 p-1 mb-3 text-sm font-medium shadow-inner">

                    <button type="button"
                            id="theme-light-btn"
                            onclick="setTheme('light')"
                            class="flex-1 flex items-center justify-center gap-2 py-1.5 rounded-lg transition-all duration-150 text-xs">

                        Clair

                    </button>

                    <button type="button"
                            id="theme-dark-btn"
                            onclick="setTheme('dark')"
                            class="flex-1 flex items-center justify-center gap-2 py-1.5 rounded-lg transition-all duration-150 text-xs">

                        Sombre

                    </button>

                </div>

                <!-- LOGOUT -->
                <form method="POST" action="{{ route('logout') }}">

                    @csrf

                    <button type="submit"
                            class="w-full flex items-center justify-center gap-2 px-3.5 py-2 rounded-xl text-xs font-medium text-red-500 dark:text-red-400 hover:bg-red-500/15 transition shadow-sm">

                        <svg class="w-4 h-4 shrink-0"
                             fill="none"
                             viewBox="0 0 24 24"
                             stroke="currentColor"
                             stroke-width="2">

                            <path stroke-linecap="round"
                                  stroke-linejoin="round"
                                  d="M15.75 9V5.25A2.25 2.25 0 0013.5 3h-6A2.25 2.25 0 005.25 5.25v13.5A2.25 2.25 0 007.5 21h6a2.25 2.25 0 002.25-2.25V15"/>

                            <path stroke-linecap="round"
                                  stroke-linejoin="round"
                                  d="M18 8.25L21.75 12 18 15.75M21.75 12H9"/>

                        </svg>

                        Déconnexion

                    </button>

                </form>

            </div>

        </aside>

        <!-- MAIN -->
        <main class="flex-1 bg-gray-50 dark:bg-gray-900 rounded-none lg:rounded-l-3xl flex flex-col h-full overflow-hidden transition-colors duration-200">

            <!-- HEADER -->
            <header class="bg-white dark:bg-gray-950 border-b border-gray-100 dark:border-gray-800 px-4 sm:px-6 py-4 flex items-center justify-between shadow-sm shrink-0 transition-colors duration-200">

                <div class="flex items-center gap-3">

                    <!-- HAMBURGER -->
                    <button id="hamburger-btn"
                            class="lg:hidden p-2 rounded-xl bg-gray-100 dark:bg-gray-900 text-gray-600 dark:text-gray-300 hover:text-amber-500 transition focus:outline-none"
                            title="Ouvrir le menu">

                        <svg class="w-6 h-6"
                             fill="none"
                             viewBox="0 0 24 24"
                             stroke="currentColor"
                             stroke-width="2">

                            <path stroke-linecap="round"
                                  stroke-linejoin="round"
                                  d="M4 6h16M4 12h16M4 18h16"/>

                        </svg>

                    </button>

                    <div>

                        <span class="block text-[10px] font-bold uppercase tracking-wider text-gray-400 dark:text-gray-500">

                            @yield('header_subtitle', 'Espace de Gestion')

                        </span>

                        <h1 class="text-base font-bold text-gray-900 dark:text-white">

                            @yield('header_title', 'Tableau de bord')

                        </h1>

                    </div>

                </div>

                <!-- DROITE HEADER -->
                <div class="flex items-center gap-3">

                    @php

                        $unreadCount = \App\Models\Notification::where('user_id', Auth::id())
                            ->whereNull('read_at')
                            ->count();

                        $outOfStockCount = \App\Models\Product::where(
                            'boutique_id',
                            Auth::user()->boutique_id ?? null
                        )
                        ->where('quantity', 0)
                        ->count();

                    @endphp

                    <!-- SCAN -->
                    <a href="{{ route('stock.scan.index') }}"
                       class="flex items-center gap-2 px-3.5 py-2 rounded-xl bg-amber-500 text-gray-900 hover:bg-amber-400 transition text-xs font-bold shadow-md shadow-amber-500/10"
                       style="background-color: var(--primary);"
                       title="Scan rapide / Mode Douchette">

                        Scan & Stock

                    </a>

                    <!-- ALERTE STOCK -->
                    @if($outOfStockCount > 0)

                        <button id="force-audio-btn"
                                onclick="playStockAlertSound(true)"
                                class="hidden sm:flex items-center gap-1.5 px-3 py-1.5 rounded-xl bg-rose-500/10 border border-rose-500/30 text-rose-500 text-xs font-bold animate-pulse hover:bg-rose-500/20 transition"
                                title="Cliquez pour rejouer l'alerte sonore">

                            Alerte Stock ({{ $outOfStockCount }})

                        </button>

                    @endif

                    <!-- NOTIFICATIONS -->
                    <a href="{{ route('notifications.index') }}"
                       class="p-2.5 rounded-xl bg-gray-50 dark:bg-gray-900 border border-gray-200 dark:border-gray-800 text-gray-400 hover:text-gray-600 dark:hover:text-white hover:bg-gray-100 dark:hover:bg-gray-800 transition shadow-sm relative"
                       title="Notifications">

                        <svg class="w-4 h-4 {{ $unreadCount > 0 ? 'text-rose-500 animate-bounce' : '' }}"
                             fill="none"
                             viewBox="0 0 24 24"
                             stroke="currentColor"
                             stroke-width="2">

                            <path stroke-linecap="round"
                                  stroke-linejoin="round"
                                  d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9" />

                        </svg>

                        @if($unreadCount > 0)

                            <span class="absolute -top-1 -right-1 flex h-3.5 w-3.5">

                                <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-rose-400 opacity-75"></span>

                                <span class="relative inline-flex rounded-full h-3.5 w-3.5 bg-rose-500"></span>

                            </span>

                        @endif

                    </a>

                    <!-- PROFIL -->
                    <div class="relative" x-data="{ open: false }">

                        <button @click="open = !open"
                                class="flex items-center gap-3 py-1.5 px-3 rounded-2xl bg-gray-50 dark:bg-gray-900 border border-gray-200 dark:border-gray-800 hover:border-amber-500/50 transition shadow-sm focus:outline-none">

                            <div class="w-8 h-8 rounded-xl bg-amber-500/10 text-amber-500 font-bold flex items-center justify-center text-xs border border-amber-500/20"
                                 style="color: var(--primary); background-color: color-mix(in srgb, var(--primary) 10%, transparent); border-color: color-mix(in srgb, var(--primary) 20%, transparent);">

                                {{ strtoupper(substr(Auth::user()->name ?? 'A', 0, 2)) }}

                            </div>

                            <div class="hidden sm:block text-left">

                                <span class="block text-xs font-bold text-gray-900 dark:text-white leading-tight">

                                    {{ Auth::user()->name ?? 'Utilisateur' }}

                                </span>

                                <span class="block text-[10px] text-gray-500 dark:text-gray-400 leading-tight">

                                    Connecté

                                </span>

                            </div>

                            <svg class="w-4 h-4 text-gray-400 transition-transform duration-200"
                                 :class="{ 'rotate-180': open }"
                                 fill="none"
                                 viewBox="0 0 24 24"
                                 stroke="currentColor"
                                 stroke-width="2">

                                <path stroke-linecap="round"
                                      stroke-linejoin="round"
                                      d="M19 9l-7 7-7-7" />

                            </svg>

                        </button>

                        <div x-show="open"
                             @click.away="open = false"
                             x-transition
                             class="absolute right-0 mt-2 w-48 bg-white dark:bg-gray-950 border border-gray-100 dark:border-gray-800 rounded-2xl shadow-xl py-1.5 z-50"
                             style="display: none;">

                            <!-- PROFIL -->
                            <a href="{{ route('profile.edit') }}"
                               class="flex items-center gap-2.5 px-4 py-2 text-xs font-medium text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-900 transition">

                                <svg class="w-4 h-4 shrink-0 text-gray-500 dark:text-gray-400"
                                     fill="none"
                                     viewBox="0 0 24 24"
                                     stroke="currentColor"
                                     stroke-width="2">

                                    <path stroke-linecap="round"
                                          stroke-linejoin="round"
                                          d="M15.75 6.75a3.75 3.75 0 11-7.5 0 3.75 3.75 0 017.5 0zM4.5 20.25a7.5 7.5 0 0115 0"/>

                                </svg>

                                Mon Profil

                            </a>

                            @if(Auth::user()->role !== 'vendeur')

                                <!-- PARAMÈTRES -->
                                <a href="{{ route('settings.boutique') }}"
                                   class="flex items-center gap-2.5 px-4 py-2 text-xs font-medium text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-900 transition">

                                    <svg class="w-4 h-4 shrink-0 text-gray-500 dark:text-gray-400"
                                         fill="none"
                                         viewBox="0 0 24 24"
                                         stroke="currentColor"
                                         stroke-width="2">

                                        <path stroke-linecap="round"
                                              stroke-linejoin="round"
                                              d="M11.983 2.5a1.5 1.5 0 00-1.414 1.05l-.26 1.042a8.01 8.01 0 00-1.57.906l-.976-.462a1.5 1.5 0 00-1.815.534l-.79.728a7.928 7.928 0 000 1.812l.79.728a1.5 1.5 0 00-.27 1.834l1.5 2.598a1.5 1.5 0 001.815.534l.976-.462a8.01 8.01 0 001.57.906l.26 1.042a1.5 1.5 0 001.414 1.05h3.034a1.5 1.5 0 001.414-1.05l.26-1.042a8.01 8.01 0 001.57-.906l.976.462a1.5 1.5 0 001.815-.534l1.5-2.598a1.5 1.5 0 00-.27-1.834l-.79-.728a7.928 7.928 0 000-1.812l.79-.728a1.5 1.5 0 00.27-1.834l-1.5-2.598a1.5 1.5 0 00-1.815-.534l-.976.462a8.01 8.01 0 00-1.57-.906l-.26-1.042a1.5 1.5 0 00-1.414-1.05h-3.034z" />

                                        <circle cx="12" cy="12" r="3" />

                                    </svg>

                                    Paramètres

                                </a>

                            @endif

                            <div class="h-px bg-gray-100 dark:bg-gray-900 my-1"></div>

                            <!-- DÉCONNEXION -->
                            <form method="POST" action="{{ route('logout') }}">

                                @csrf

                                <button type="submit"
                                        class="w-full flex items-center gap-2.5 px-4 py-2 text-xs font-medium text-rose-500 hover:bg-rose-500/10 transition">

                                    <svg class="w-4 h-4 shrink-0"
                                         fill="none"
                                         viewBox="0 0 24 24"
                                         stroke="currentColor"
                                         stroke-width="2">

                                        <path stroke-linecap="round"
                                              stroke-linejoin="round"
                                              d="M15.75 9V5.25A2.25 2.25 0 0013.5 3h-6A2.25 2.25 0 005.25 5.25v13.5A2.25 2.25 0 007.5 21h6a2.25 2.25 0 002.25-2.25V15"/>

                                        <path stroke-linecap="round"
                                              stroke-linejoin="round"
                                              d="M18 8.25L21.75 12 18 15.75M21.75 12H9"/>

                                    </svg>

                                    Déconnexion

                                </button>

                            </form>

                        </div>

                    </div>

                </div>

            </header>

            <!-- FLASH -->
            <div class="px-4 sm:px-6 pt-4 shrink-0">

                @if(session('success'))

                    <div class="flex items-center gap-3 p-4 rounded-2xl bg-emerald-500/10 border border-emerald-500/20 text-emerald-600 dark:text-emerald-400 text-xs font-semibold shadow-sm">

                        <span>{{ session('success') }}</span>

                    </div>

                @endif

                @if(session('error'))

                    <div class="flex items-center gap-3 p-4 rounded-2xl bg-rose-500/10 border border-rose-500/20 text-rose-600 dark:text-rose-400 text-xs font-semibold shadow-sm">

                        <span>{{ session('error') }}</span>

                    </div>

                @endif

            </div>

            <!-- CONTENU -->
            <div class="flex-1 overflow-y-auto p-4 sm:p-6 lg:p-8">

                @yield('content')

            </div>

        </main>

    </div>

</div>

<!-- POPUP CODE UTILISATEUR -->
@if(session('new_user_code'))

    <div id="codeModal"
         class="fixed inset-0 z-50 flex items-center justify-center bg-black/70 backdrop-blur-sm p-4">

        <div class="bg-white dark:bg-gray-900 border border-gray-200 dark:border-gray-800 rounded-3xl shadow-2xl p-6 sm:p-8 max-w-md w-full mx-auto transform transition-all space-y-5 text-center">

            <h3 class="text-lg font-bold text-gray-900 dark:text-white">

                ⚠️ TRÈS IMPORTANT

            </h3>

            <p class="text-xs text-gray-500 dark:text-gray-400">

                Voici le code d'accès unique généré pour le nouvel utilisateur.
                Notez-le bien, il ne sera plus affiché.

            </p>

            <div class="p-4 bg-gray-100 dark:bg-gray-950 border border-gray-200 dark:border-gray-800 rounded-2xl font-mono text-xl font-bold tracking-widest"
                 style="color: var(--primary);">

                {{ session('new_user_code') }}

            </div>

            @if(session('new_user_id'))

                <form method="POST"
                      action="{{ route('user.envoyer-identifiants-email', session('new_user_id')) }}">

                    @csrf

                    <button type="submit"
                            class="w-full py-2.5 bg-gray-200 dark:bg-gray-800 hover:bg-gray-300 dark:hover:bg-gray-700 text-gray-900 dark:text-white font-bold text-xs rounded-xl transition border border-gray-300 dark:border-gray-700">

                        Envoyer par email

                    </button>

                </form>

            @endif

            <button onclick="document.getElementById('codeModal').remove()"
                    class="w-full py-2.5 bg-amber-500 hover:bg-amber-400 text-gray-900 font-bold text-xs rounded-xl transition shadow-lg shadow-amber-500/20"
                    style="background-color: var(--primary);">

                J'ai bien noté le code

            </button>

        </div>

    </div>

@endif

<!-- THEME + SIDEBAR -->
<script>

    function setTheme(theme) {

        const html = document.documentElement;
        const lightBtn = document.getElementById('theme-light-btn');
        const darkBtn = document.getElementById('theme-dark-btn');

        if (theme === 'dark') {

            html.classList.add('dark');

            localStorage.setItem('theme', 'dark');

            if (lightBtn) {

                lightBtn.className =
                    "flex-1 flex items-center justify-center gap-2 py-1.5 rounded-lg transition-all duration-150 text-xs text-gray-400 hover:text-white";

            }

            if (darkBtn) {

                darkBtn.className =
                    "flex-1 flex items-center justify-center gap-2 py-1.5 rounded-lg transition-all duration-150 text-xs bg-gray-800 text-white font-semibold shadow-sm";

            }

        } else {

            html.classList.remove('dark');

            localStorage.setItem('theme', 'light');

            if (lightBtn) {

                lightBtn.className =
                    "flex-1 flex items-center justify-center gap-2 py-1.5 rounded-lg transition-all duration-150 text-xs bg-white text-gray-900 font-semibold shadow-sm";

            }

            if (darkBtn) {

                darkBtn.className =
                    "flex-1 flex items-center justify-center gap-2 py-1.5 rounded-lg transition-all duration-150 text-xs text-gray-500 hover:text-gray-900";

            }

        }

    }

    document.addEventListener('DOMContentLoaded', () => {

        const savedTheme = localStorage.getItem('theme') || 'dark';

        setTheme(savedTheme);

        const sidebar = document.getElementById('app-sidebar');
        const overlay = document.getElementById('sidebar-overlay');
        const hamburgerBtn = document.getElementById('hamburger-btn');
        const closeBtn = document.getElementById('sidebar-close');

        function openSidebar() {

            if (!sidebar || !overlay) return;

            sidebar.classList.remove('-translate-x-full');
            overlay.classList.remove('hidden');

        }

        function closeSidebar() {

            if (!sidebar || !overlay) return;

            sidebar.classList.add('-translate-x-full');
            overlay.classList.add('hidden');

        }

        if (hamburgerBtn) {
            hamburgerBtn.addEventListener('click', openSidebar);
        }

        if (closeBtn) {
            closeBtn.addEventListener('click', closeSidebar);
        }

        if (overlay) {
            overlay.addEventListener('click', closeSidebar);
        }

    });

</script>

<!-- PWA INSTALLATION -->
<script>

    let deferredPrompt = null;

    window.addEventListener('beforeinstallprompt', function (event) {

        event.preventDefault();

        deferredPrompt = event;

    });

    window.installPwaApp = async function () {

        if (!deferredPrompt) {

            console.log(
                "E-Stockly : le prompt d'installation PWA n'est pas disponible."
            );

            return;

        }

        try {

            const promptEvent = deferredPrompt;

            await promptEvent.prompt();

            const choiceResult = await promptEvent.userChoice;

            if (choiceResult.outcome === 'accepted') {

                console.log(
                    "E-Stockly : installation acceptée."
                );

            } else {

                console.log(
                    "E-Stockly : installation annulée."
                );

            }

            deferredPrompt = null;

        } catch (error) {

            console.error(
                "E-Stockly : erreur lors de l'installation PWA :",
                error
            );

        }

    };

    window.addEventListener('appinstalled', function () {

        deferredPrompt = null;

    });

</script>

<!-- SERVICE WORKER -->
<script>

    if ('serviceWorker' in navigator) {

        window.addEventListener('load', () => {

            navigator.serviceWorker.register('/sw.js')

                .then((registration) => {

                    console.log(
                        'Service Worker enregistré:',
                        registration.scope
                    );

                })

                .catch((error) => {

                    console.log(
                        'Échec de l\'enregistrement du Service Worker:',
                        error
                    );

                });

        });

    }

</script>

</body>
</html>
