<!DOCTYPE html>
<html lang="fr" class="dark"> <!-- Par défaut en dark vu ton thème global -->
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>E-Stockly</title>
    <!-- Tailwind CSS via CDN -->
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        // Configuration de Tailwind pour supporter le mode dark via une classe
        tailwind.config = {
            darkMode: 'class',
        }
    </script>
</head>
<body class="bg-amber-100/50 dark:bg-gray-900 font-sans antialiased text-gray-800 dark:text-gray-100 transition-colors duration-200">

    <!-- CONTENEUR GLOBAL (Effet cadre) -->
    <div class="min-h-screen p-4 sm:p-6 flex justify-center items-center">
        <div class="w-full max-w-7xl bg-gray-950 rounded-3xl shadow-2xl overflow-hidden flex flex-col lg:flex-row min-h-[85vh] lg:h-[120vh]">

            <!-- SIDEBAR GAUCHE (Taille fixe et verrouillée avec shrink-0) -->
            <aside class="w-full lg:w-64 bg-gray-950 text-gray-400 flex flex-col justify-between p-5 shrink-0">
                <div>
                    <!-- Logo / Titre -->
                    <div class="flex items-center space-x-3 mb-8 px-1">
                        <div class="bg-amber-500 text-gray-900 w-9 h-9 rounded-xl font-black flex items-center justify-center text-sm">
                            ES
                        </div>
                        <span class="text-white font-bold text-lg tracking-wide">E-STOCKLY</span>
                    </div>

                    <!-- Liens de navigation -->
                    <nav class="space-y-1.5">

                        <!-- Tableau de bord -->
                        <a href="{{ route('dashboard') }}"
                           class="flex items-center gap-3 px-4 py-3 rounded-xl transition text-sm font-medium
                                  {{ request()->routeIs('dashboard')
                                        ? 'bg-amber-500 text-gray-900 font-semibold shadow-lg shadow-amber-500/20'
                                        : 'text-gray-400 hover:bg-gray-800 hover:text-white' }}">
                            <svg class="w-5 h-5 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M3 13h8V3H3v10zm0 8h8v-6H3v6zm10 0h8V11h-8v10zm0-18v6h8V3h-8z" />
                            </svg>
                            Tableau de bord
                        </a>

                        <!-- Produits -->
                        <a href="{{ route('products.index') }}"
                           class="flex items-center gap-3 px-4 py-3 rounded-xl transition text-sm font-medium
                                  {{ request()->routeIs('products*')
                                        ? 'bg-amber-500 text-gray-900 font-semibold shadow-lg shadow-amber-500/20'
                                        : 'text-gray-400 hover:bg-gray-800 hover:text-white' }}">
                            <svg class="w-5 h-5 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4" />
                            </svg>
                            Produits
                        </a>

                        <!-- Catégories -->
                        <a href="{{ route('categories.index') }}"
                           class="flex items-center gap-3 px-4 py-3 rounded-xl transition text-sm font-medium
                                  {{ request()->routeIs('categories*')
                                        ? 'bg-amber-500 text-gray-900 font-semibold shadow-lg shadow-amber-500/20'
                                        : 'text-gray-400 hover:bg-gray-800 hover:text-white' }}">
                            <svg class="w-5 h-5 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M4 6h16M4 10h16M4 14h16M4 18h16" />
                            </svg>
                            Catégories
                        </a>

                        <!-- Commandes (Purchase Orders) -->
                        <a href="{{ route('purchase_orders.index') }}"
                           class="flex items-center gap-3 px-4 py-3 rounded-xl transition text-sm font-medium
                                  {{ request()->routeIs('purchase_orders*')
                                        ? 'bg-amber-500 text-gray-900 font-semibold shadow-lg shadow-amber-500/20'
                                        : 'text-gray-400 hover:bg-gray-800 hover:text-white' }}">
                            <svg class="w-5 h-5 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                            </svg>
                            Commandes
                        </a>

                        <!-- Fournisseurs -->
                        <a href="{{ route('suppliers.index') }}"
                           class="flex items-center gap-3 px-4 py-3 rounded-xl transition text-sm font-medium
                                  {{ request()->routeIs('suppliers*')
                                        ? 'bg-amber-500 text-gray-900 font-semibold shadow-lg shadow-amber-500/20'
                                        : 'text-gray-400 hover:bg-gray-800 hover:text-white' }}">
                            <svg class="w-5 h-5 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M17 20h5v-2a4 4 0 00-3-3.87M9 20H4v-2a4 4 0 013-3.87m6-2.13a4 4 0 10-4-4 4 4 0 004 4zm6 0a4 4 0 10-4-4" />
                            </svg>
                            Fournisseurs
                        </a>

                        <!-- Section Admin -->
                        @if(Auth::check() && Auth::user()->role === 'administrateur')
                        <div class="pt-4">
                            <p class="text-[11px] font-semibold text-gray-600 uppercase tracking-wider mb-2 px-4">Admin</p>
                            <a href="{{ route('user.index') }}"
                               class="flex items-center gap-3 px-4 py-3 rounded-xl transition text-sm font-medium
                                      {{ request()->routeIs('users*') || request()->routeIs('user*')
                                            ? 'bg-amber-500 text-gray-900 font-semibold shadow-lg shadow-amber-500/20'
                                            : 'text-gray-400 hover:bg-gray-800 hover:text-white' }}">
                                <svg class="w-5 h-5 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M17 20h5v-2a4 4 0 00-3-3.87M9 20H4v-2a4 4 0 013-3.87m6-2.13a4 4 0 10-4-4 4 4 0 004 4zm-8 0a4 4 0 10-4-4 4 4 0 004 4z" />
                                </svg>
                                Utilisateurs
                            </a>
                        </div>
                        @endif
                    </nav>
                </div>

                <!-- Bas de sidebar : Profil actif, theme, Deconnexion -->
                <div class="space-y-3 pt-6 border-t border-gray-800">

                    <div class="flex items-center gap-3 bg-gray-900 p-3 rounded-2xl border border-gray-800">
                        <div class="w-9 h-9 rounded-full bg-amber-500 text-gray-900 font-bold flex items-center justify-center text-sm shrink-0">
                            {{ strtoupper(substr(Auth::user()->name ?? 'U', 0, 1)) }}
                        </div>
                        <div class="min-w-0">
                            <div class="text-xs font-bold text-white truncate">{{ Auth::user()->name ?? 'Utilisateur' }}</div>
                            <div class="text-[10px] text-green-400 flex items-center">
                                <span class="w-1.5 h-1.5 rounded-full bg-green-400 inline-block mr-1"></span> Actif
                            </div>
                        </div>
                    </div>

                    <!-- Bascule Clair / Sombre (Fonctionnelle) -->
                    <div class="flex items-center bg-gray-900 rounded-xl border border-gray-800 p-1 text-xs font-medium">
                        <button type="button" id="theme-light-btn" onclick="setTheme('light')" class="flex-1 flex items-center justify-center gap-1.5 py-1.5 rounded-lg transition-all duration-150">
                            <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364 6.364l-.707-.707M6.343 6.343l-.707-.707m12.728 0l-.707.707M6.343 17.657l-.707.707M16 12a4 4 0 11-8 0 4 4 0 018 0z" />
                            </svg>
                            Clair
                        </button>
                        <button type="button" id="theme-dark-btn" onclick="setTheme('dark')" class="flex-1 flex items-center justify-center gap-1.5 py-1.5 rounded-lg transition-all duration-150">
                            <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z" />
                            </svg>
                            Sombre
                        </button>
                    </div>

                    <!-- Bouton Déconnexion -->
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit"
                            class="w-full flex items-center justify-center gap-2 px-4 py-2.5 rounded-xl text-sm font-medium text-red-400 hover:bg-red-500/15 transition">
                            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" />
                            </svg>
                            Deconnexion
                        </button>
                    </form>
                </div>
            </aside>

            <!-- ZONE CENTRALE (Contenu dynamique des vues) -->
            <main class="flex-1 bg-gray-50 dark:bg-gray-900 rounded-l-3xl p-6 lg:p-8 overflow-y-auto text-gray-900 dark:text-gray-100 min-w-0 transition-colors duration-200">
                @yield('content')
            </main>

        </div>
    </div>

    <!-- Script de gestion du mode clair / sombre -->
    <script>
        const lightBtn = document.getElementById('theme-light-btn');
        const darkBtn = document.getElementById('theme-dark-btn');

        function setTheme(theme) {
            if (theme === 'dark') {
                document.documentElement.classList.add('dark');
                localStorage.setItem('theme', 'dark');
                
                // Style du bouton actif Sombre
                darkBtn.className = "flex-1 flex items-center justify-center gap-1.5 py-1.5 rounded-lg bg-amber-500 text-gray-900 font-semibold shadow";
                lightBtn.className = "flex-1 flex items-center justify-center gap-1.5 py-1.5 rounded-lg text-gray-400 hover:text-white";
            } else {
                document.documentElement.classList.remove('dark');
                localStorage.setItem('theme', 'light');
                
                // Style du bouton actif Clair
                lightBtn.className = "flex-1 flex items-center justify-center gap-1.5 py-1.5 rounded-lg bg-amber-500 text-gray-900 font-semibold shadow";
                darkBtn.className = "flex-1 flex items-center justify-center gap-1.5 py-1.5 rounded-lg text-gray-400 hover:text-white";
            }
        }

        // Initialisation au chargement de la page selon le localStorage
        (function() {
            const savedTheme = localStorage.getItem('theme') || 'dark'; // Sombre par défaut
            setTheme(savedTheme);
        })();
    </script>
</body>
</html>