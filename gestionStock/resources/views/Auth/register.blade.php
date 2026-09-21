
<!DOCTYPE html>

<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Inscription - E-Stockly</title>

    <script src="https://cdn.tailwindcss.com"></script>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
</head>

<body
    class="min-h-screen lg:h-screen lg:overflow-hidden bg-slate-50"
    x-data="{
        step: {{ $errors->hasAny(['nom_boutique', 'ville', 'quartier', 'indication']) ? 2 : 1 }},
        showModal: false,

        ville: @js(old('ville', '')),
        quartier: @js(old('quartier', '')),

        quartiers: {
            'Douala': [
                'Akwa',
                'Akwa Nord',
                'Bali',
                'Bépanda',
                'Bonabéri',
                'Bonamoussadi',
                'Bonanjo',
                'Bonapriso',
                'Deïdo',
                'Ndongbong',
                'Village'
            ],

            'Yaoundé': [
                'Bastos',
                'Biyem-Assi',
                'Cité Verte',
                'Emana',
                'Essos',
                'Etoudi',
                'Kondengui',
                'Mendong',
                'Mokolo',
                'Mvan',
                'Mvog-Ada',
                'Mvog-Mbi',
                'Nkolbisson',
                'Ngoa-Ekélé',
                'Ngousso',
                'Nlongkak',
                'Odza'
            ],

            'Bafoussam': [
                'Bamendzi',
                'Banengo',
                'Djeleng',
                'Djemoun',
                'Famla',
                'Gouache',
                'Haoussa',
                'Kamkop',
                'Kouogouo',
                'Ndiandam',
                'Nylon',
                'Tamdja',
                'Toket',
                'Tougang',
                'Tyo'
            ]
        },

        changerVille() {
            this.quartier = '';
        }
    }"
>

<div class="min-h-screen lg:h-screen lg:flex">


    <!-- ========================================================= -->
    <!-- PANNEAU GAUCHE -->
    <!-- ========================================================= -->

    <div class="hidden lg:flex lg:w-[45%] lg:h-screen relative overflow-hidden bg-gradient-to-br from-indigo-900 via-indigo-800 to-blue-900">

        <div
            class="absolute inset-0 opacity-10"
            style="
                background-image:
                    linear-gradient(white 1px, transparent 1px),
                    linear-gradient(90deg, white 1px, transparent 1px);
                background-size: 60px 60px;
            "
        ></div>


        <div class="relative z-10 flex flex-col justify-between p-10 xl:p-14 text-white w-full">


            <!-- Logo -->

            <div class="flex items-center gap-3">

                <img
                    src="{{ asset('images/logo.png') }}"
                    alt="Logo E-Stockly"
                    class="w-10 h-10 rounded-xl object-cover object-top shadow-lg"
                >

                <div class="flex flex-col">

                    <span class="font-bold text-lg tracking-tight leading-none">
                        E-Stockly
                    </span>

                    <span class="text-[11px] text-indigo-300 font-medium tracking-wide mt-0.5">
                        Gestion de stock centralisée
                    </span>

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

                    <span class="text-orange-400">
                        en toute simplicité
                    </span>

                </h1>


                <p class="mt-4 text-indigo-200 text-base max-w-md leading-relaxed">
                    Centralisez vos stocks, vos ventes et vos approvisionnements dans un espace clair,
                    rapide et conçu pour les équipes qui avancent.
                </p>


                <!-- Cartes fonctionnalités -->

                <div class="grid grid-cols-2 gap-4 mt-8 max-w-lg">


                    <!-- Gestion des stocks -->

                    <div class="bg-white/10 backdrop-blur-sm rounded-xl p-4 border border-white/10">

                        <div class="w-8 h-8 rounded-lg bg-white/15 flex items-center justify-center mb-2.5">

                            <svg
                                class="w-4 h-4"
                                fill="none"
                                viewBox="0 0 24 24"
                                stroke="currentColor"
                                stroke-width="2"
                            >

                                <path
                                    stroke-linecap="round"
                                    stroke-linejoin="round"
                                    d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"
                                />

                            </svg>

                        </div>

                        <p class="text-sm font-semibold">
                            Gestion des stocks
                        </p>

                        <p class="text-xs text-indigo-300 mt-1">
                            Suivi en temps réel
                        </p>

                    </div>


                    <!-- Analytics -->

                    <div class="bg-white/10 backdrop-blur-sm rounded-xl p-4 border border-white/10">

                        <div class="w-8 h-8 rounded-lg bg-white/15 flex items-center justify-center mb-2.5">

                            <svg
                                class="w-4 h-4"
                                fill="none"
                                viewBox="0 0 24 24"
                                stroke="currentColor"
                                stroke-width="2"
                            >

                                <path
                                    stroke-linecap="round"
                                    stroke-linejoin="round"
                                    d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2V9a2 2 0 012-2h2a2 2 0 012 2v10m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14"
                                />

                            </svg>

                        </div>

                        <p class="text-sm font-semibold">
                            Analytics
                        </p>

                        <p class="text-xs text-indigo-300 mt-1">
                            Tableaux de bord intelligents
                        </p>

                    </div>


                    <!-- Multi-entrepôts -->

                    <div class="bg-white/10 backdrop-blur-sm rounded-xl p-4 border border-white/10">

                        <div class="w-8 h-8 rounded-lg bg-white/15 flex items-center justify-center mb-2.5">

                            <svg
                                class="w-4 h-4"
                                fill="none"
                                viewBox="0 0 24 24"
                                stroke="currentColor"
                                stroke-width="2"
                            >

                                <path
                                    stroke-linecap="round"
                                    stroke-linejoin="round"
                                    d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2M5 21H3m4-14h4m-4 4h4m-4 4h4"
                                />

                            </svg>

                        </div>

                        <p class="text-sm font-semibold">
                            Multi-entrepôts
                        </p>

                        <p class="text-xs text-indigo-300 mt-1">
                            Gestion centralisée
                        </p>

                    </div>


                    <!-- Ventes & Approvisionnement -->

                    <div class="bg-white/10 backdrop-blur-sm rounded-xl p-4 border border-white/10">

                        <div class="w-8 h-8 rounded-lg bg-white/15 flex items-center justify-center mb-2.5">

                            <svg
                                class="w-4 h-4"
                                fill="none"
                                viewBox="0 0 24 24"
                                stroke="currentColor"
                                stroke-width="2"
                            >

                                <path
                                    stroke-linecap="round"
                                    stroke-linejoin="round"
                                    d="M17 20h5v-2a4 4 0 00-3-3.87M9 20H4v-2a4 4 0 013-3.87m6-2.13a4 4 0 10-4-4 4 4 0 004 4zm6 0a4 4 0 10-4-4"
                                />

                            </svg>

                        </div>

                        <p class="text-sm font-semibold">
                            Ventes &amp; Appro.
                        </p>

                        <p class="text-xs text-indigo-300 mt-1">
                            Processus optimisés
                        </p>

                    </div>

                </div>

            </div>


            <!-- Statistiques -->

            <div class="flex gap-12 border-t border-white/10 pt-6">

                <div>

                    <p class="text-2xl font-extrabold">
                        100%
                    </p>

                    <p class="text-xs text-indigo-300 mt-1">
                        Sécurisé
                    </p>

                </div>


                <div>

                    <p class="text-2xl font-extrabold">
                        99.9%
                    </p>

                    <p class="text-xs text-indigo-300 mt-1">
                        Disponibilité
                    </p>

                </div>


                <div>

                    <p class="text-2xl font-extrabold">
                        24/7
                    </p>

                    <p class="text-xs text-indigo-300 mt-1">
                        Support
                    </p>

                </div>

            </div>

        </div>

    </div>


    <!-- ========================================================= -->
    <!-- PANNEAU DROIT -->
    <!-- ========================================================= -->

    <div
        class="w-full lg:w-[55%] lg:h-screen lg:overflow-hidden bg-[#121620] lg:bg-white flex items-start justify-center px-6 py-10 sm:px-10 lg:px-16 xl:px-24"
    >

        <div
            class="max-w-md w-full bg-white lg:bg-transparent p-6 sm:p-8 rounded-3xl lg:rounded-none shadow-xl lg:shadow-none border border-slate-100 lg:border-none lg:h-full lg:flex lg:flex-col"
        >


            <!-- Logo mobile -->

            <div class="flex items-center gap-2 mb-8 lg:hidden shrink-0">

                <img
                    src="{{ asset('images/logo.png') }}"
                    alt="Logo E-Stockly"
                    class="w-9 h-9 rounded-lg object-cover object-top"
                >

                <span class="font-bold text-slate-900 text-lg">
                    E-Stockly
                </span>

            </div>


            <!-- ===================================================== -->
            <!-- EN-TÊTE FIXE -->
            <!-- ===================================================== -->

            <div class="mb-7 shrink-0">

                <!-- Retour à l'accueil -->

                <a
                    href="{{ url('/') }}"
                    class="inline-flex items-center gap-2 text-sm font-medium text-slate-500 hover:text-indigo-600 transition mb-5"
                >
                    <span class="text-base">
                        ←
                    </span>

                    <span>
                        Retour à l’accueil
                    </span>
                </a>


                <p class="text-xs font-bold tracking-widest text-indigo-600 uppercase">
                    E-Stockly
                </p>


                <h2 class="text-2xl sm:text-3xl font-bold text-slate-900 mt-2">
                    Créez votre compte
                </h2>


                <p class="text-sm text-slate-500 mt-2">
                    Quelques informations suffisent pour commencer à utiliser E-Stockly.
                </p>

            </div>


            <!-- ===================================================== -->
            <!-- PROGRESSION FIXE -->
            <!-- ===================================================== -->

            <div class="mb-8 shrink-0">

                <div class="flex items-center justify-between text-xs font-medium">


                    <!-- Étape 1 -->

                    <div
                        class="flex items-center gap-2"
                        :class="step >= 1 ? 'text-indigo-600' : 'text-slate-400'"
                    >

                        <div
                            class="w-7 h-7 rounded-full flex items-center justify-center font-bold"
                            :class="step >= 1 ? 'bg-indigo-600 text-white' : 'bg-slate-100 text-slate-400'"
                        >
                            1
                        </div>

                        <span>
                            Identité
                        </span>

                    </div>


                    <!-- Barre -->

                    <div class="flex-1 mx-3 h-1 bg-slate-100 rounded-full overflow-hidden">

                        <div
                            class="h-full bg-indigo-600 rounded-full transition-all duration-300"
                            :style="step === 1 ? 'width: 0%' : 'width: 100%'"
                        ></div>

                    </div>


                    <!-- Étape 2 -->

                    <div
                        class="flex items-center gap-2"
                        :class="step === 2 ? 'text-indigo-600' : 'text-slate-400'"
                    >

                        <div
                            class="w-7 h-7 rounded-full flex items-center justify-center font-bold"
                            :class="step === 2 ? 'bg-indigo-600 text-white' : 'bg-slate-100 text-slate-400'"
                        >
                            2
                        </div>

                        <span>
                            Boutique
                        </span>

                    </div>

                </div>


                <p class="text-xs text-slate-400 mt-2 text-center">

                    <span x-show="step === 1">
                        Étape 1 sur 2 — Vos informations personnelles
                    </span>

                    <span x-show="step === 2">
                        Étape 2 sur 2 — Les informations de votre boutique
                    </span>

                </p>

            </div>


            <!-- ===================================================== -->
            <!-- ZONE QUI SCROLLE : ERREURS + FORMULAIRE + LIENS -->
            <!-- ===================================================== -->

            <div
                class="lg:flex-1 lg:min-h-0 lg:overflow-y-auto lg:pr-2"
                x-ref="scrollZone"
            >

                <!-- ================================================= -->
                <!-- ERREURS -->
                <!-- ================================================= -->

                @if ($errors->any())

                    <div class="bg-red-50 border border-red-200 p-4 rounded-lg text-sm text-red-700 mb-6">

                        <p class="font-semibold mb-1">
                            Corrigez les erreurs suivantes :
                        </p>


                        <ul class="list-disc list-inside space-y-0.5">

                            @foreach ($errors->all() as $error)

                                <li>
                                    {{ $error }}
                                </li>

                            @endforeach

                        </ul>

                    </div>

                @endif


                <!-- ================================================= -->
                <!-- FORMULAIRE -->
                <!-- ================================================= -->

                <form
                    class="space-y-5"
                    action="{{ route('register') }}"
                    method="POST"
                    x-ref="form"
                >

                    @csrf


                    <!-- ================================================= -->
                    <!-- ÉTAPE 1 : IDENTITÉ -->
                    <!-- ================================================= -->

                    <div
                        x-show="step === 1"
                        x-transition
                        class="space-y-5"
                    >

                        <div>

                            <h3 class="text-lg font-bold text-slate-900">
                                Vos informations personnelles
                            </h3>

                            <p class="text-sm text-slate-500 mt-1">
                                Ces informations serviront à créer votre compte administrateur.
                            </p>

                        </div>


                        <!-- Nom complet -->

                        <div>

                            <label
                                for="name"
                                class="block text-sm font-medium text-slate-700 mb-1.5"
                            >
                                Nom complet
                            </label>

                            <input
                                type="text"
                                name="name"
                                id="name"
                                value="{{ old('name') }}"
                                required
                                placeholder="Ex : Ryan Mbarga"
                                class="w-full px-3.5 py-2.5 border border-slate-300 rounded-lg shadow-sm placeholder-slate-400 text-sm text-slate-900 bg-white focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition"
                            >

                        </div>


                        <!-- Email -->

                        <div>

                            <label
                                for="email"
                                class="block text-sm font-medium text-slate-700 mb-1.5"
                            >
                                Adresse email
                            </label>

                            <input
                                type="email"
                                name="email"
                                id="email"
                                value="{{ old('email') }}"
                                required
                                placeholder="vous@exemple.com"
                                class="w-full px-3.5 py-2.5 border border-slate-300 rounded-lg shadow-sm placeholder-slate-400 text-sm text-slate-900 bg-white focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition"
                            >

                        </div>


                        <!-- Téléphone -->

                        <div>

                            <label
                                for="phone"
                                class="block text-sm font-medium text-slate-700 mb-1.5"
                            >
                                Téléphone
                            </label>


                            <div class="flex">

                                <div class="flex items-center gap-2 px-3.5 border border-r-0 border-slate-300 rounded-l-lg bg-slate-50 text-sm text-slate-700">

                                    <span>
                                        🇨🇲
                                    </span>

                                    <span>
                                        +237
                                    </span>

                                </div>


                                <input
                                    type="text"
                                    name="phone"
                                    id="phone"
                                    value="{{ old('phone') }}"
                                    required
                                    maxlength="9"
                                    minlength="9"
                                    inputmode="numeric"
                                    pattern="[0-9]{9}"
                                    placeholder="698123456"
                                    class="w-full px-3.5 py-2.5 border border-slate-300 rounded-r-lg shadow-sm placeholder-slate-400 text-sm text-slate-900 bg-white focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition"
                                >

                            </div>


                            <p class="text-xs text-slate-400 mt-1.5">
                                Entrez les 9 chiffres de votre numéro camerounais.
                            </p>

                        </div>


                        <!-- Mot de passe -->

                        <div>

                            <label
                                for="password"
                                class="block text-sm font-medium text-slate-700 mb-1.5"
                            >
                                Mot de passe
                            </label>


                            <div class="relative">

                                <input
                                    type="password"
                                    name="password"
                                    id="password"
                                    required
                                    minlength="8"
                                    placeholder="8 caractères minimum"
                                    class="w-full px-3.5 py-2.5 pr-11 border border-slate-300 rounded-lg shadow-sm placeholder-slate-400 text-sm text-slate-900 bg-white focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition"
                                >


                                <button
                                    type="button"
                                    onclick="togglePassword('password', this)"
                                    aria-label="Afficher le mot de passe"
                                    title="Afficher le mot de passe"
                                    class="absolute inset-y-0 right-0 px-3.5 flex items-center text-slate-400 hover:text-indigo-600 transition"
                                >

                                    <svg
                                        class="w-5 h-5 eye-open"
                                        fill="none"
                                        viewBox="0 0 24 24"
                                        stroke="currentColor"
                                        stroke-width="2"
                                    >

                                        <path
                                            stroke-linecap="round"
                                            stroke-linejoin="round"
                                            d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"
                                        />

                                        <path
                                            stroke-linecap="round"
                                            stroke-linejoin="round"
                                            d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"
                                        />

                                    </svg>


                                    <svg
                                        class="w-5 h-5 eye-closed hidden"
                                        fill="none"
                                        viewBox="0 0 24 24"
                                        stroke="currentColor"
                                        stroke-width="2"
                                    >

                                        <path
                                            stroke-linecap="round"
                                            stroke-linejoin="round"
                                            d="M3 3l18 18"
                                        />

                                        <path
                                            stroke-linecap="round"
                                            stroke-linejoin="round"
                                            d="M10.584 10.587a2 2 0 002.829 2.829"
                                        />

                                        <path
                                            stroke-linecap="round"
                                            stroke-linejoin="round"
                                            d="M9.88 5.09A10.94 10.94 0 0112 5c4.478 0 8.268 2.943 9.542 7a11.05 11.05 0 01-4.043 5.21M6.228 6.228A11.05 11.05 0 002.458 12c.746 2.376 2.38 4.364 4.377 5.583A10.94 10.94 0 0112 19c1.06 0 2.08-.15 3.04-.43"
                                        />

                                    </svg>

                                </button>

                            </div>

                        </div>


                        <!-- Confirmation -->

                        <div>

                            <label
                                for="password_confirmation"
                                class="block text-sm font-medium text-slate-700 mb-1.5"
                            >
                                Confirmer le mot de passe
                            </label>


                            <div class="relative">

                                <input
                                    type="password"
                                    name="password_confirmation"
                                    id="password_confirmation"
                                    required
                                    placeholder="Ressaisissez le mot de passe"
                                    class="w-full px-3.5 py-2.5 pr-11 border border-slate-300 rounded-lg shadow-sm placeholder-slate-400 text-sm text-slate-900 bg-white focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition"
                                >


                                <button
                                    type="button"
                                    onclick="togglePassword('password_confirmation', this)"
                                    aria-label="Afficher la confirmation du mot de passe"
                                    title="Afficher la confirmation du mot de passe"
                                    class="absolute inset-y-0 right-0 px-3.5 flex items-center text-slate-400 hover:text-indigo-600 transition"
                                >

                                    <svg
                                        class="w-5 h-5 eye-open"
                                        fill="none"
                                        viewBox="0 0 24 24"
                                        stroke="currentColor"
                                        stroke-width="2"
                                    >

                                        <path
                                            stroke-linecap="round"
                                            stroke-linejoin="round"
                                            d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"
                                        />

                                        <path
                                            stroke-linecap="round"
                                            stroke-linejoin="round"
                                            d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"
                                        />

                                    </svg>


                                    <svg
                                        class="w-5 h-5 eye-closed hidden"
                                        fill="none"
                                        viewBox="0 0 24 24"
                                        stroke="currentColor"
                                        stroke-width="2"
                                    >

                                        <path
                                            stroke-linecap="round"
                                            stroke-linejoin="round"
                                            d="M3 3l18 18"
                                        />

                                        <path
                                            stroke-linecap="round"
                                            stroke-linejoin="round"
                                            d="M10.584 10.587a2 2 0 002.829 2.829"
                                        />

                                        <path
                                            stroke-linecap="round"
                                            stroke-linejoin="round"
                                            d="M9.88 5.09A10.94 10.94 0 0112 5c4.478 0 8.268 2.943 9.542 7a11.05 11.05 0 01-4.043 5.21M6.228 6.228A11.05 11.05 0 002.458 12c.746 2.376 2.38 4.364 4.377 5.583A10.94 10.94 0 0112 19c1.06 0 2.08-.15 3.04-.43"
                                        />

                                    </svg>

                                </button>

                            </div>

                        </div>


                        <!-- Continuer -->

                        <button
                            type="button"
                            @click="
                                const fields = [
                                    document.getElementById('name'),
                                    document.getElementById('email'),
                                    document.getElementById('phone'),
                                    document.getElementById('password'),
                                    document.getElementById('password_confirmation')
                                ];

                                const valid = fields.every(field => field.checkValidity());

                                if (valid) {
                                    step = 2;
                                } else {
                                    const firstInvalid = fields.find(field => !field.checkValidity());

                                    if (firstInvalid) {
                                        firstInvalid.reportValidity();
                                    }
                                }
                            "
                            class="w-full py-3 px-4 rounded-lg shadow-sm text-sm font-semibold text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition mt-2"
                        >

                            Continuer

                            <span class="ml-1">
                                →
                            </span>

                        </button>

                    </div>


                    <!-- ================================================= -->
                    <!-- ÉTAPE 2 : BOUTIQUE -->
                    <!-- ================================================= -->

                    <div
                        x-show="step === 2"
                        x-transition
                        class="space-y-5"
                    >

                        <div>

                            <h3 class="text-lg font-bold text-slate-900">
                                Votre boutique
                            </h3>

                            <p class="text-sm text-slate-500 mt-1">
                                Indiquez-nous où se trouve votre boutique.
                            </p>

                        </div>


                        <!-- Nom boutique -->

                        <div>

                            <label
                                for="nom_boutique"
                                class="block text-sm font-medium text-slate-700 mb-1.5"
                            >
                                Nom de votre boutique
                            </label>


                            <input
                                type="text"
                                name="nom_boutique"
                                id="nom_boutique"
                                value="{{ old('nom_boutique') }}"
                                required
                                placeholder="Ex : Boutique Centrale"
                                class="w-full px-3.5 py-2.5 border border-slate-300 rounded-lg shadow-sm placeholder-slate-400 text-sm text-slate-900 bg-white focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition"
                            >

                        </div>


                        <!-- Ville -->

                        <div>

                            <label
                                for="ville"
                                class="block text-sm font-medium text-slate-700 mb-1.5"
                            >
                                Ville
                            </label>


                            <select
                                name="ville"
                                id="ville"
                                x-model="ville"
                                @change="changerVille()"
                                required
                                class="w-full px-3.5 py-2.5 border border-slate-300 rounded-lg shadow-sm text-sm text-slate-900 bg-white focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition"
                            >

                                <option value="">
                                    Sélectionnez votre ville
                                </option>

                                <option value="Douala">
                                    Douala
                                </option>

                                <option value="Yaoundé">
                                    Yaoundé
                                </option>

                                <option value="Bafoussam">
                                    Bafoussam
                                </option>

                            </select>


                            <p class="text-xs text-slate-400 mt-1.5">
                                Pour le moment, E-Stockly est disponible dans ces trois villes.
                            </p>

                        </div>


                        <!-- Quartier -->

                        <div>

                            <label
                                for="quartier"
                                class="block text-sm font-medium text-slate-700 mb-1.5"
                            >
                                Quartier
                            </label>


                            <select
                                name="quartier"
                                id="quartier"
                                x-model="quartier"
                                required
                                :disabled="!ville"
                                class="w-full px-3.5 py-2.5 border border-slate-300 rounded-lg shadow-sm text-sm text-slate-900 bg-white focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition disabled:bg-slate-100 disabled:text-slate-400"
                            >

                                <option
                                    value=""
                                    x-text="ville ? 'Sélectionnez votre quartier' : 'Sélectionnez d’abord une ville'"
                                ></option>


                                <template
                                    x-for="nomQuartier in (quartiers[ville] || [])"
                                    :key="nomQuartier"
                                >

                                    <option
                                        :value="nomQuartier"
                                        x-text="nomQuartier"
                                    ></option>

                                </template>

                            </select>


                            <p class="text-xs text-slate-400 mt-1.5">
                                Les quartiers proposés dépendent de la ville sélectionnée.
                            </p>

                        </div>


                        <!-- Indication -->

                        <div>

                            <label
                                for="indication"
                                class="block text-sm font-medium text-slate-700 mb-1.5"
                            >
                                Indication
                            </label>


                            <input
                                type="text"
                                name="indication"
                                id="indication"
                                value="{{ old('indication') }}"
                                required
                                placeholder="Ex : À côté de la pharmacie..."
                                class="w-full px-3.5 py-2.5 border border-slate-300 rounded-lg shadow-sm placeholder-slate-400 text-sm text-slate-900 bg-white focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition"
                            >


                            <p class="text-xs text-slate-400 mt-1.5">
                                Une indication permettant de retrouver facilement votre boutique.
                            </p>

                        </div>


                        <!-- Boutons -->

                        <div class="flex gap-3 pt-2">

                            <button
                                type="button"
                                @click="step = 1"
                                class="w-1/3 py-3 px-4 rounded-lg text-sm font-semibold text-slate-700 bg-slate-100 hover:bg-slate-200 transition"
                            >
                                ← Retour
                            </button>


                            <button
                                type="button"
                                @click="
                                    const fields = [
                                        document.getElementById('nom_boutique'),
                                        document.getElementById('ville'),
                                        document.getElementById('quartier'),
                                        document.getElementById('indication')
                                    ];

                                    const valid = fields.every(field => field.checkValidity());

                                    if (valid) {
                                        showModal = true;
                                    } else {
                                        const firstInvalid = fields.find(field => !field.checkValidity());

                                        if (firstInvalid) {
                                            firstInvalid.reportValidity();
                                        }
                                    }
                                "
                                class="flex-1 py-3 px-4 rounded-lg shadow-sm text-sm font-semibold text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition"
                            >
                                Créer ma boutique
                            </button>

                        </div>

                    </div>

                </form>


                <!-- ================================================= -->
                <!-- LIENS : EUX AUSSI DANS LA ZONE SCROLLABLE -->
                <!-- ================================================= -->

                <div class="flex items-center justify-center gap-3 text-xs text-slate-500 mt-8 pt-2">

                    <a
                        href="{{ route('terms') }}"
                        class="hover:text-indigo-600 transition"
                    >
                        Conditions d'utilisation
                    </a>

                    <span>·</span>

                    <a
                        href="{{ route('privacy') }}"
                        class="hover:text-indigo-600 transition"
                    >
                        Politique de confidentialité
                    </a>

                </div>


                <!-- ================================================= -->
                <!-- CONNEXION : ELLE AUSSI DANS LA ZONE SCROLLABLE -->
                <!-- ================================================= -->

                <p class="text-center text-sm text-slate-600 mt-6 pb-6">

                    Déjà un compte ?

                    <a
                        href="{{ route('login') }}"
                        class="font-semibold text-indigo-600 hover:text-indigo-500"
                    >
                        Se connecter
                    </a>

                </p>

            </div>


            <!-- ===================================================== -->
            <!-- MODAL -->
            <!-- ===================================================== -->

            <div
                x-show="showModal"
                style="display: none;"
                class="fixed inset-0 z-50 flex items-center justify-center bg-slate-900/60 backdrop-blur-sm p-4"
            >

                <div class="bg-white rounded-2xl max-w-md w-full p-6 shadow-2xl border border-slate-100">


                    <div class="w-12 h-12 rounded-xl bg-indigo-50 text-indigo-600 flex items-center justify-center mb-4">

                        <svg
                            class="w-6 h-6"
                            fill="none"
                            viewBox="0 0 24 24"
                            stroke="currentColor"
                            stroke-width="2"
                        >

                            <path
                                stroke-linecap="round"
                                stroke-linejoin="round"
                                d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"
                            />

                        </svg>

                    </div>


                    <h3 class="text-lg font-bold text-slate-900">
                        Avez-vous déjà un compte ?
                    </h3>


                    <p class="text-sm text-slate-600 mt-2 leading-relaxed">
                        Si votre Administrateur vous a déjà créé un accès pour rejoindre une boutique existante,
                        n'utilisez pas ce formulaire. Connectez-vous simplement avec les identifiants qui vous ont été fournis.
                    </p>


                    <div class="mt-6 flex flex-col gap-2.5">


                        <button
                            type="button"
                            @click="$refs.form.submit()"
                            class="w-full py-2.5 px-4 rounded-xl text-sm font-semibold text-white bg-indigo-600 hover:bg-indigo-700 transition"
                        >
                            Oui, je veux créer une nouvelle boutique
                        </button>


                        <a
                            href="{{ route('login') }}"
                            class="w-full py-2.5 px-4 rounded-xl text-sm font-semibold text-indigo-600 bg-indigo-50 hover:bg-indigo-100 text-center transition"
                        >
                            Non, j'ai déjà un compte (Se connecter)
                        </a>


                        <button
                            type="button"
                            @click="showModal = false"
                            class="w-full py-2 px-4 rounded-xl text-xs font-medium text-slate-500 hover:text-slate-700 transition mt-1"
                        >
                            Annuler et modifier mes informations
                        </button>

                    </div>

                </div>

            </div>

        </div>

    </div>

</div>


<!-- ========================================================= -->
<!-- JAVASCRIPT -->
<!-- ========================================================= -->

<script>

    function togglePassword(id, btn) {

        const input = document.getElementById(id);

        const eyeOpen = btn.querySelector('.eye-open');

        const eyeClosed = btn.querySelector('.eye-closed');


        if (input.type === 'password') {

            input.type = 'text';

            eyeOpen.classList.add('hidden');

            eyeClosed.classList.remove('hidden');

            btn.setAttribute(
                'aria-label',
                'Masquer le mot de passe'
            );

            btn.setAttribute(
                'title',
                'Masquer le mot de passe'
            );

        } else {

            input.type = 'password';

            eyeClosed.classList.add('hidden');

            eyeOpen.classList.remove('hidden');

            btn.setAttribute(
                'aria-label',
                'Afficher le mot de passe'
            );

            btn.setAttribute(
                'title',
                'Afficher le mot de passe'
            );

        }

    }


    /*
     * ============================================================
     * DÉFILEMENT AUTOMATIQUE INTELLIGENT
     * ============================================================
     *
     * Le formulaire reste entièrement scrollable avec les liens
     * du bas. Sur grand écran, lorsqu'un champ devient trop proche
     * du bas de la zone visible, on fait monter doucement le contenu.
     *
     * Le déplacement est volontairement léger : l'utilisateur garde
     * le contrôle du scroll et découvre simplement qu'il y a une suite.
     */

    document.addEventListener('DOMContentLoaded', () => {

        const scrollZone = document.querySelector('[x-ref="scrollZone"]');

        if (!scrollZone) {
            return;
        }

        const isDesktop = () => window.matchMedia('(min-width: 1024px)').matches;

        const fields = scrollZone.querySelectorAll(
            'input:not([type="hidden"]), select, textarea'
        );

        const alreadyMovedFor = new WeakSet();

        function gentlyRevealField(field) {

            if (!isDesktop()) {
                return;
            }

            const zoneRect = scrollZone.getBoundingClientRect();
            const fieldRect = field.getBoundingClientRect();

            const bottomMargin = 90;
            const topMargin = 30;

            if (fieldRect.bottom > zoneRect.bottom - bottomMargin) {

                const amount = Math.min(
                    fieldRect.bottom - (zoneRect.bottom - bottomMargin),
                    120
                );

                scrollZone.scrollBy({
                    top: amount,
                    behavior: 'smooth'
                });

                return;
            }

            if (fieldRect.top < zoneRect.top + topMargin) {

                const amount = Math.min(
                    fieldRect.top - (zoneRect.top + topMargin),
                    120
                );

                scrollZone.scrollBy({
                    top: amount,
                    behavior: 'smooth'
                });
            }
        }

        fields.forEach(field => {

            field.addEventListener('focus', () => {

                window.setTimeout(() => {
                    gentlyRevealField(field);
                }, 80);

            });

            field.addEventListener('input', () => {

                if (
                    field.value.trim() !== '' &&
                    !alreadyMovedFor.has(field)
                ) {

                    alreadyMovedFor.add(field);

                    window.setTimeout(() => {
                        gentlyRevealField(field);
                    }, 80);
                }

            });

        });


        window.addEventListener('resize', () => {

            if (!isDesktop()) {
                scrollZone.scrollTo({
                    top: 0,
                    behavior: 'auto'
                });
            }

        });

    });

</script>


</body>
</html>

