<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Connexion - E-Stockly</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="min-h-screen flex bg-slate-50">

    <!-- ================= PANNEAU GAUCHE (branding) ================= -->
    <div class="hidden lg:flex lg:w-[45%] min-h-screen relative overflow-hidden bg-gradient-to-br from-indigo-900 via-indigo-800 to-blue-900">
        <div class="absolute inset-0 opacity-10"
             style="background-image: linear-gradient(white 1px, transparent 1px), linear-gradient(90deg, white 1px, transparent 1px);
                    background-size: 60px 60px;"></div>

        <div class="relative z-10 flex flex-col justify-between p-10 xl:p-14 text-white w-full">

            <!-- Logo -->
            <div class="flex items-center gap-3">
                <img src="{{ asset('images/logo.png') }}" alt="Logo E-Stockly" class="w-10 h-10 rounded-xl object-cover object-top shadow-lg">
                <div class="flex flex-col">
                    <span class="font-bold text-lg tracking-tight leading-none">E-Stockly</span>
                    <span class="text-[11px] text-indigo-300 font-medium tracking-wide mt-0.5">Gestion de stock centralisée</span>
                </div>
            </div>

            <!-- Titre + description -->
            <div class="mt-10">
                <p class="text-xs font-semibold tracking-widest text-indigo-300 uppercase mb-3">
                    Plateforme de gestion intelligente
                </p>
                <h1 class="text-4xl xl:text-[2.75rem] font-extrabold leading-[1.15]">
                    Bon retour parmi nous
                    <br>
                    <span class="text-amber-400">gérez vos stocks</span>
                </h1>
                <p class="mt-4 text-indigo-200 text-base max-w-md leading-relaxed">
                    Centralisez vos stocks, vos ventes et vos approvisionnements dans un espace clair, rapide et conçu pour les équipes qui avancent.
                </p>

                <!-- Cartes fonctionnalités -->
                <div class="grid grid-cols-2 gap-4 mt-8 max-w-lg">
                    <div class="bg-white/10 backdrop-blur-sm rounded-xl p-4 border border-white/10">
                        <div class="w-8 h-8 rounded-lg bg-white/15 flex items-center justify-center mb-2.5">
                            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4" />
                            </svg>
                        </div>
                        <p class="text-sm font-semibold">Gestion des stocks</p>
                        <p class="text-xs text-indigo-300 mt-1">Suivi en temps réel</p>
                    </div>

                    <div class="bg-white/10 backdrop-blur-sm rounded-xl p-4 border border-white/10">
                        <div class="w-8 h-8 rounded-lg bg-white/15 flex items-center justify-center mb-2.5">
                            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14" />
                            </svg>
                        </div>
                        <p class="text-sm font-semibold">Analytics</p>
                        <p class="text-xs text-indigo-300 mt-1">Tableaux de bord intelligents</p>
                    </div>

                    <div class="bg-white/10 backdrop-blur-sm rounded-xl p-4 border border-white/10">
                        <div class="w-8 h-8 rounded-lg bg-white/15 flex items-center justify-center mb-2.5">
                            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2M5 21H3m4-14h4m-4 4h4m-4 4h4" />
                            </svg>
                        </div>
                        <p class="text-sm font-semibold">Multi-entrepôts</p>
                        <p class="text-xs text-indigo-300 mt-1">Gestion centralisée</p>
                    </div>

                    <div class="bg-white/10 backdrop-blur-sm rounded-xl p-4 border border-white/10">
                        <div class="w-8 h-8 rounded-lg bg-white/15 flex items-center justify-center mb-2.5">
                            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M17 20h5v-2a4 4 0 00-3-3.87M9 20H4v-2a4 4 0 013-3.87m6-2.13a4 4 0 10-4-4 4 4 0 004 4zm6 0a4 4 0 10-4-4" />
                            </svg>
                        </div>
                        <p class="text-sm font-semibold">Ventes &amp; Appro.</p>
                        <p class="text-xs text-indigo-300 mt-1">Processus optimisés</p>
                    </div>
                </div>
            </div>

            <!-- Statistiques -->
            <div class="flex gap-12 border-t border-white/10 pt-6">
                <div>
                    <p class="text-2xl font-extrabold">100%</p>
                    <p class="text-xs text-indigo-300 mt-1">Sécurisé</p>
                </div>
                <div>
                    <p class="text-2xl font-extrabold">99.9%</p>
                    <p class="text-xs text-indigo-300 mt-1">Disponibilité</p>
                </div>
                <div>
                    <p class="text-2xl font-extrabold">24/7</p>
                    <p class="text-xs text-indigo-300 mt-1">Support</p>
                </div>
            </div>
        </div>
    </div>

    <!-- ================= PANNEAU DROIT (formulaire de connexion) ================= -->
    <div class="w-full lg:w-[55%] min-h-screen flex items-center justify-center px-6 py-10 sm:px-10 lg:px-16 xl:px-24 bg-[#121620] lg:bg-white">
        <div class="max-w-md w-full bg-white lg:bg-transparent p-6 sm:p-8 rounded-3xl lg:rounded-none shadow-xl lg:shadow-none border border-slate-100 lg:border-none">

            <!-- Logo mobile uniquement -->
            <div class="flex items-center gap-2 mb-8 lg:hidden">
                <img src="{{ asset('images/logo.png') }}" alt="Logo E-Stockly" class="w-9 h-9 rounded-lg object-cover object-top">
                <span class="font-bold text-slate-900 text-lg">E-Stockly</span>
            </div>

            <!-- En-tête -->
            <div class="mb-8">
                <p class="text-xs font-bold tracking-widest text-indigo-600 uppercase">E-Stockly</p>
                <h2 class="text-2xl sm:text-3xl font-bold text-slate-900 mt-2">Connexion</h2>
                <p class="text-sm text-slate-500 mt-2">Entrez votre code unique pour accéder à votre espace.</p>
            </div>

            <!-- Affichage du message de succès (ex: après réinitialisation du mot de passe / code) -->
            @if (session('success'))
                <div class="bg-emerald-50 border border-emerald-200 p-4 rounded-lg text-sm text-emerald-700 mb-6">
                    {{ session('success') }}
                </div>
            @endif

            <!-- Affichage des erreurs globales de validation -->
            @if ($errors->any())
                <div class="bg-red-50 border border-red-200 p-4 rounded-lg text-sm text-red-700 mb-6">
                    <p class="font-semibold mb-1">Connexion impossible :</p>
                    <ul class="list-disc list-inside space-y-0.5">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <!-- Formulaire -->
            <form class="space-y-5" action="{{ route('login') }}" method="POST">
                @csrf

                <!-- Code de connexion unique -->
                <div>
                    <div class="flex items-center justify-between mb-1.5">
                        <label for="code_connexion" class="block text-sm font-medium text-slate-700">Code de connexion unique</label>
                        @if (Route::has('code-connexion.request'))
                            <a href="{{ route('code-connexion.request') }}" class="text-xs font-medium text-indigo-600 hover:text-indigo-500">Code oublié ?</a>
                        @endif
                    </div>
                    <input type="text" name="code_connexion" id="code_connexion" value="{{ old('code_connexion') }}" required
                        placeholder="Ex: K7X9P2" oninput="this.value = this.value.toUpperCase()"
                        class="w-full px-3.5 py-2.5 border border-slate-300 rounded-lg shadow-sm placeholder-slate-400 text-sm text-slate-900 bg-white focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 uppercase tracking-widest transition">
                </div>

                <!-- Mot de passe -->
                <div>
                    <div class="flex items-center justify-between mb-1.5">
                        <label for="password" class="block text-sm font-medium text-slate-700">Mot de passe</label>
                        @if (Route::has('password.request'))
                            <a href="{{ route('password.request') }}" class="text-xs font-medium text-indigo-600 hover:text-indigo-500">Mot de passe oublié ?</a>
                        @endif
                    </div>
                    <div class="relative">
                        <input type="password" name="password" id="password" required placeholder="Votre mot de passe"
                            class="w-full px-3.5 py-2.5 pr-11 border border-slate-300 rounded-lg shadow-sm placeholder-slate-400 text-sm text-slate-900 bg-white focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition">
                        <button type="button" onclick="togglePassword('password', this)"
                            class="absolute inset-y-0 right-0 pr-3.5 flex items-center text-slate-400 hover:text-slate-600">
                            <svg class="w-4.5 h-4.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                <path stroke-linecap="round" stroke-linejoin="round" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                            </svg>
                        </button>
                    </div>
                </div>

                <!-- Se souvenir de moi -->
                <label class="flex items-center gap-2 cursor-pointer w-fit">
                    <input type="checkbox" name="remember" class="rounded border-slate-300 text-indigo-600 focus:ring-indigo-500 w-4 h-4">
                    <span class="text-sm text-slate-600">Se souvenir de moi</span>
                </label>

                <!-- Bouton de soumission -->
                <button type="submit"
                    class="w-full py-3 px-4 rounded-lg shadow-sm text-sm font-semibold text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition mt-2">
                    Se connecter
                </button>
            </form>

            <!-- Badges de confiance -->
            <div class="flex items-center justify-center gap-5 text-xs text-slate-400 mt-6">
                <span class="flex items-center gap-1.5">
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" />
                    </svg>
                    SSL 256-bit
                </span>
                <span class="flex items-center gap-1.5">
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                    </svg>
                    RGPD
                </span>
            </div>

            <!-- Lien vers l'inscription -->
            <p class="text-center text-sm text-slate-600 mt-6">
                Pas encore de compte ?
                <a href="{{ route('register') }}" class="font-semibold text-indigo-600 hover:text-indigo-500">S'inscrire</a>
            </p>
        </div>
    </div>

    <script>
        function togglePassword(id, btn) {
            const input = document.getElementById(id);
            input.type = input.type === 'password' ? 'text' : 'password';
        }
    </script>

</body>
</html>