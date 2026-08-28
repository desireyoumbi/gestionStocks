<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Inscription - E-Stockly</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <!-- Ajout d'Alpine.js pour gérer le modal interactivement -->
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
</head>
<body class="min-h-screen flex bg-slate-50" x-data="{ showModal: false }">

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
                    Gérez votre entreprise
                    <br>
                    <span class="text-orange-400">en toute simplicité</span>
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

    <!-- ================= PANNEAU DROIT (formulaire) ================= -->
    <div class="w-full lg:w-[55%] min-h-screen flex items-center justify-center px-6 py-10 sm:px-10 lg:px-16 xl:px-24">
        <div class="max-w-md w-full">

            <!-- Logo mobile uniquement -->
            <div class="flex items-center gap-2 mb-8 lg:hidden">
                <img src="{{ asset('images/logo.jpeg') }}" alt="Logo E-Stockly" class="w-9 h-9 rounded-lg object-cover object-top">
                <span class="font-bold text-slate-900 text-lg">E-Stockly</span>
            </div>

            <!-- En-tête -->
            <div class="mb-8">
                <p class="text-xs font-bold tracking-widest text-indigo-600 uppercase">E-Stockly</p>
                <h2 class="text-2xl sm:text-3xl font-bold text-slate-900 mt-2">Créez votre compte</h2>
                <p class="text-sm text-slate-500 mt-2">Inscrivez-vous pour configurer votre boutique et commencer à gérer votre activité.</p>
            </div>

            <!-- Affichage des erreurs globales de validation -->
            @if ($errors->any())
                <div class="bg-red-50 border border-red-200 p-4 rounded-lg text-sm text-red-700 mb-6">
                    <p class="font-semibold mb-1">Corrigez les erreurs suivantes :</p>
                    <ul class="list-disc list-inside space-y-0.5">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <!-- Formulaire -->
            <form class="space-y-5" action="{{ route('register') }}" method="POST" x-ref="form">
                @csrf

                <!-- Nom de la boutique -->
                <div>
                    <label for="nom_boutique" class="block text-sm font-medium text-slate-700 mb-1.5">Nom de votre boutique</label>
                    <input type="text" name="nom_boutique" id="nom_boutique" value="{{ old('nom_boutique') }}" required
                        placeholder="Ex : Boutique Centrale"
                        class="w-full px-3.5 py-2.5 border border-slate-300 rounded-lg shadow-sm placeholder-slate-400 text-sm text-slate-900 bg-white focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition">
                </div>

                <!-- Nom complet -->
                <div>
                    <label for="name" class="block text-sm font-medium text-slate-700 mb-1.5">Nom complet</label>
                    <input type="text" name="name" id="name" value="{{ old('name') }}" required
                        placeholder="Ryan Mbarga"
                        class="w-full px-3.5 py-2.5 border border-slate-300 rounded-lg shadow-sm placeholder-slate-400 text-sm text-slate-900 bg-white focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition">
                </div>

                <!-- Téléphone et Email -->
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label for="phone" class="block text-sm font-medium text-slate-700 mb-1.5">Téléphone</label>
                        <input type="text" name="phone" id="phone" value="{{ old('phone') }}" required placeholder="690 00 00 00"
                            class="w-full px-3.5 py-2.5 border border-slate-300 rounded-lg shadow-sm placeholder-slate-400 text-sm text-slate-900 bg-white focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition">
                    </div>
                    <div>
                        <label for="email" class="block text-sm font-medium text-slate-700 mb-1.5">
                            Email <span class="text-slate-400 font-normal">(facultatif)</span>
                        </label>
                        <input type="email" name="email" id="email" value="{{ old('email') }}"
                            placeholder="vous@exemple.com"
                            class="w-full px-3.5 py-2.5 border border-slate-300 rounded-lg shadow-sm placeholder-slate-400 text-sm text-slate-900 bg-white focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition">
                    </div>
                </div>

                <!-- Mot de passe -->
                <div>
                    <label for="password" class="block text-sm font-medium text-slate-700 mb-1.5">Mot de passe</label>
                    <div class="relative">
                        <input type="password" name="password" id="password" required placeholder="8 caractères minimum"
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

                <!-- Confirmation du mot de passe -->
                <div>
                    <label for="password_confirmation" class="block text-sm font-medium text-slate-700 mb-1.5">Confirmer le mot de passe</label>
                    <div class="relative">
                        <input type="password" name="password_confirmation" id="password_confirmation" required placeholder="Ressaisissez le mot de passe"
                            class="w-full px-3.5 py-2.5 pr-11 border border-slate-300 rounded-lg shadow-sm placeholder-slate-400 text-sm text-slate-900 bg-white focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition">
                        <button type="button" onclick="togglePassword('password_confirmation', this)"
                            class="absolute inset-y-0 right-0 pr-3.5 flex items-center text-slate-400 hover:text-slate-600">
                            <svg class="w-4.5 h-4.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                <path stroke-linecap="round" stroke-linejoin="round" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                            </svg>
                        </button>
                    </div>
                </div>

                <!-- Bouton de soumission déclenchant le modal si le formulaire est valide -->
                <button type="button"
                    @click="$refs.form.checkValidity() ? showModal = true : $refs.form.reportValidity()"
                    class="w-full py-3 px-4 rounded-lg shadow-sm text-sm font-semibold text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition mt-2">
                    Créer mon compte
                </button>
            </form>

            <!-- ================= MODAL DE VÉRIFICATION PÉDAGOGIQUE ================= -->
            <div x-show="showModal" style="display: none;" class="fixed inset-0 z-50 flex items-center justify-center bg-slate-900/60 backdrop-blur-sm p-4">
                <div class="bg-white rounded-2xl max-w-md w-full p-6 shadow-2xl border border-slate-100 transform transition-all">
                    
                    <!-- Icône d'alerte -->
                    <div class="w-12 h-12 rounded-xl bg-indigo-50 text-indigo-600 flex items-center justify-center mb-4">
                        <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </div>

                    <h3 class="text-lg font-bold text-slate-900">Avez-vous déjà un compte ?</h3>
                    <p class="text-sm text-slate-600 mt-2 leading-relaxed">
                        Si votre Administrateur vous a déjà créé un accès pour rejoindre une boutique existante, n'utilisez pas ce formulaire. 
                        Connectez-vous simplement avec les identifiants qui vous ont été fournis.
                    </p>

                    <!-- Actions du modal -->
                    <div class="mt-6 flex flex-col gap-2.5">
                        <button type="button" @click="$refs.form.submit()"
                            class="w-full py-2.5 px-4 rounded-xl text-sm font-semibold text-white bg-indigo-600 hover:bg-indigo-700 transition">
                            Oui, je veux créer une nouvelle boutique
                        </button>

                        <a href="{{ route('login') }}"
                            class="w-full py-2.5 px-4 rounded-xl text-sm font-semibold text-indigo-600 bg-indigo-50 hover:bg-indigo-100 text-center transition">
                            Non, j'ai déjà un compte (Se connecter)
                        </a>

                        <button type="button" @click="showModal = false"
                            class="w-full py-2 px-4 rounded-xl text-xs font-medium text-slate-500 hover:text-slate-700 transition mt-1">
                            Annuler et modifier mes informations
                        </button>
                    </div>
                </div>
            </div>

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

            <!-- Lien vers la connexion -->
            <p class="text-center text-sm text-slate-600 mt-6">
                Déjà un compte ?
                <a href="{{ route('login') }}" class="font-semibold text-indigo-600 hover:text-indigo-500">Se connecter</a>
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