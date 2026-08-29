<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>e-Stoky — Gérez votre stock, maîtrisez votre activité</title>

    <script src="https://cdn.tailwindcss.com"></script>

    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        stoky: {
                            yellow: '#FFAA00',
                            orange: '#F59E0B',
                            navy: '#050B18',
                            dark: '#0B1220',
                            soft: '#FFF8E8'
                        }
                    },

                    boxShadow: {
                        glow: '0 25px 80px rgba(255,170,0,.20)',
                        dashboard: '0 35px 90px rgba(5,11,24,.28)'
                    }
                }
            }
        }
    </script>

    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>

    <style>
        html {
            scroll-behavior: smooth;
        }

        body {
            font-family: Inter, ui-sans-serif, system-ui, -apple-system,
                         BlinkMacSystemFont, "Segoe UI", sans-serif;
        }

        .hero-grid {
            background-image:
                linear-gradient(rgba(5, 11, 24, .045) 1px, transparent 1px),
                linear-gradient(90deg, rgba(5, 11, 24, .045) 1px, transparent 1px);
            background-size: 42px 42px;
        }

        .orange-gradient {
            background: linear-gradient(
                135deg,
                #FFAA00 0%,
                #F59E0B 55%,
                #F97316 100%
            );
        }

        .dark-gradient {
            background:
                radial-gradient(
                    circle at 80% 20%,
                    rgba(255,170,0,.16),
                    transparent 28%
                ),
                radial-gradient(
                    circle at 15% 80%,
                    rgba(249,115,22,.10),
                    transparent 25%
                ),
                #050B18;
        }

        .dashboard-frame {
            transform:
                perspective(1800px)
                rotateY(-4deg)
                rotateX(2deg);
            transform-origin: center;
        }

        .phone-mockup {
            animation: phoneFloat 5s ease-in-out infinite;
        }

        @keyframes phoneFloat {
            0%, 100% {
                transform: translateY(0) rotate(2deg);
            }

            50% {
                transform: translateY(-8px) rotate(2deg);
            }
        }

        @media (prefers-reduced-motion: reduce) {
            .phone-mockup {
                animation: none;
            }
        }

        @media (max-width: 1024px) {
            .dashboard-frame {
                transform: none;
            }
        }

        /*
         * Mode desktop compact :
         * à 100% de zoom navigateur sur un écran ~1280–1440px,
         * on réduit légèrement les espacements et les grandes tailles
         * pour retrouver une présentation plus équilibrée.
         */
        @media (min-width: 1025px) and (max-width: 1450px) {

            header .max-w-7xl {
                max-width: 1180px;
            }

            header .h-20 {
                height: 4rem;
            }

            header nav {
                gap: 1.25rem;
                font-size: 0.75rem;
            }

            header .px-5.py-2\.5 {
                padding-left: 0.85rem;
                padding-right: 0.85rem;
            }

            header .px-5.py-3 {
                padding-left: 1rem;
                padding-right: 1rem;
            }

            #accueil {
                padding-top: 6.5rem;
                padding-bottom: 4.5rem;
            }

            #accueil .grid {
                gap: 2.5rem;
            }

            #accueil h1 {
                font-size: 3.25rem;
                line-height: 1.05;
            }

            #accueil p {
                font-size: 0.95rem;
                line-height: 1.7;
            }

            #solution,
            #fonctionnement,
            #pourquoi,
            #faq {
                padding-top: 5rem;
                padding-bottom: 5rem;
            }

            #solution h2,
            #fonctionnement h2,
            #pourquoi h2,
            #faq h2 {
                font-size: 2.5rem;
            }

            .dashboard-frame {
                transform:
                    perspective(1800px)
                    rotateY(-3deg)
                    rotateX(1.5deg)
                    scale(.92);
                transform-origin: center center;
            }

            .dashboard-frame .phone-mockup {
                transform-origin: bottom right;
            }

            #solution .mt-14,
            #fonctionnement .mt-16,
            #pourquoi .mt-14 {
                margin-top: 2.5rem;
            }

            footer .py-14 {
                padding-top: 2.5rem;
                padding-bottom: 2.5rem;
            }
        }
    </style>
</head>


<body class="bg-white text-slate-900 antialiased">


<!-- ========================================================= -->
<!-- NAVBAR -->
<!-- ========================================================= -->

<header
    x-data="{ mobileMenu: false }"
    class="fixed top-0 left-0 right-0 z-50
           bg-white/90 backdrop-blur-xl
           border-b border-slate-200/70"
>

    <div class="max-w-7xl mx-auto px-5 sm:px-6 lg:px-8">

        <div class="h-20 flex items-center justify-between">


            <!-- LOGO + NOM DE L'APPLICATION -->

            <a
                href="#accueil"
                class="flex items-center gap-3 group"
            >

                <!-- SYMBOLE DU LOGO -->

                <div
                    class="w-11 h-11
                           sm:w-12 sm:h-12
                           rounded-2xl
                           overflow-hidden
                           bg-white
                           flex items-start justify-center
                           shadow-sm
                           group-hover:scale-105
                           transition duration-200"
                >

                    <img
                        src="{{ asset('images/logo.png') }}"
                        alt="Logo e-Stockly"
                        class="w-full h-auto block"
                        style="
                            transform: scale(1.55);
                            transform-origin: top center;
                        "
                    >

                </div>


                <!-- NOM DE L'APPLICATION -->

                <div>

                    <div
                        class="text-xl
                               sm:text-2xl
                               font-black
                               tracking-tight
                               text-slate-950"
                    >
                        e-<span class="text-orange-500">Stoky</span>
                    </div>

                    <div
                        class="hidden sm:block
                               text-[8px]
                               uppercase
                               tracking-[.18em]
                               text-slate-400
                               font-bold"
                    >
                        Gestion de stock
                    </div>

                </div>

            </a>


            <!-- NAVIGATION DESKTOP -->

            <nav class="hidden lg:flex items-center gap-8 text-sm font-semibold">

                <a
                    href="#accueil"
                    class="text-slate-900 hover:text-orange-500 transition"
                >
                    Accueil
                </a>

                <a
                    href="#solution"
                    class="text-slate-600 hover:text-orange-500 transition"
                >
                    Fonctionnalités
                </a>

                <a
                    href="#fonctionnement"
                    class="text-slate-600 hover:text-orange-500 transition"
                >
                    Comment ça marche
                </a>

                <a
                    href="#pourquoi"
                    class="text-slate-600 hover:text-orange-500 transition"
                >
                    Pourquoi e-Stockly
                </a>

                <a
                    href="#faq"
                    class="text-slate-600 hover:text-orange-500 transition"
                >
                    FAQ
                </a>

            </nav>


            <!-- ACTIONS -->

            <div class="hidden lg:flex items-center gap-3">

                <a
                    href="{{ route('login') }}"
                    class="px-5 py-2.5 rounded-xl font-bold
                           text-slate-700
                           hover:bg-slate-100 transition"
                >
                    Se connecter
                </a>

                <a
                    href="{{ route('register') }}"
                    class="px-5 py-3 rounded-xl font-bold
                           bg-slate-950 text-white
                           hover:bg-slate-800
                           shadow-lg transition"
                >
                    Créer ma boutique
                </a>

            </div>


            <!-- BOUTON MOBILE -->

            <button
                @click="mobileMenu = !mobileMenu"
                class="lg:hidden w-11 h-11 rounded-xl
                       bg-slate-100
                       flex items-center justify-center"
            >

                <svg
                    class="w-6 h-6"
                    fill="none"
                    stroke="currentColor"
                    viewBox="0 0 24 24"
                >

                    <path
                        x-show="!mobileMenu"
                        stroke-linecap="round"
                        stroke-linejoin="round"
                        stroke-width="2"
                        d="M4 6h16M4 12h16M4 18h16"
                    />

                    <path
                        x-show="mobileMenu"
                        stroke-linecap="round"
                        stroke-linejoin="round"
                        stroke-width="2"
                        d="M6 18L18 6M6 6l12 12"
                    />

                </svg>

            </button>

        </div>

    </div>


    <!-- MENU MOBILE -->

    <div
        x-show="mobileMenu"
        x-transition
        class="lg:hidden bg-white
               border-t border-slate-200
               px-5 py-6"
    >

        <div class="flex flex-col gap-5">

            <a
                href="#accueil"
                @click="mobileMenu = false"
                class="font-semibold"
            >
                Accueil
            </a>

            <a
                href="#solution"
                @click="mobileMenu = false"
                class="font-semibold"
            >
                Fonctionnalités
            </a>

            <a
                href="#fonctionnement"
                @click="mobileMenu = false"
                class="font-semibold"
            >
                Comment ça marche
            </a>

            <a
                href="#pourquoi"
                @click="mobileMenu = false"
                class="font-semibold"
            >
                Pourquoi e-Stockly
            </a>

            <a
                href="#faq"
                @click="mobileMenu = false"
                class="font-semibold"
            >
                FAQ
            </a>


            <div
                class="border-t border-slate-200
                       pt-5 flex flex-col gap-3"
            >

                <a
                    href="{{ route('login') }}"
                    class="text-center py-3 font-bold"
                >
                    Se connecter
                </a>

                <a
                    href="{{ route('register') }}"
                    class="text-center py-3 rounded-xl
                           bg-orange-400
                           font-bold text-slate-950"
                >
                    Créer ma boutique
                </a>

            </div>

        </div>

    </div>

</header>



<main>


<!-- ========================================================= -->
<!-- HERO -->
<!-- ========================================================= -->

<section
    id="accueil"
    class="relative pt-32 pb-24
           overflow-hidden hero-grid"
>


    <!-- DÉCOR -->

    <div
        class="absolute -top-32 -right-32
               w-96 h-96 rounded-full
               bg-orange-300/20 blur-3xl"
    >
    </div>

    <div
        class="absolute bottom-0 -left-40
               w-96 h-96 rounded-full
               bg-yellow-300/20 blur-3xl"
    >
    </div>


    <div class="max-w-7xl mx-auto px-5 sm:px-6 lg:px-8">

        <div
            class="grid lg:grid-cols-12
                   gap-14 items-center"
        >


            <!-- ================================================= -->
            <!-- TEXTE -->
            <!-- ================================================= -->

            <div class="lg:col-span-5">

                <div
                    class="inline-flex items-center gap-2
                           px-4 py-2 rounded-full
                           bg-orange-50
                           border border-orange-200
                           text-orange-700
                           text-xs font-black
                           uppercase tracking-wide"
                >

                    <span
                        class="w-2 h-2 rounded-full
                               bg-orange-500"
                    >
                    </span>

                    Une gestion plus simple

                </div>


                <h1
                    class="mt-7 text-5xl sm:text-6xl
                           lg:text-[64px]
                           font-black tracking-tight
                           leading-[1.04]
                           text-slate-950"
                >

                    Gérez votre stock.

                    <br>

                    <span class="text-orange-500">
                        Maîtrisez votre activité.
                    </span>

                </h1>


                <p
                    class="mt-7 text-lg
                           leading-8
                           text-slate-600
                           max-w-xl"
                >
                    e-Stockly vous permet de centraliser
                    vos produits, vos ventes, vos mouvements,
                    vos dépenses et votre activité dans
                    un seul espace de gestion.
                </p>


                <!-- ACTIONS -->

                <div
                    class="mt-9
                           flex flex-col sm:flex-row
                           gap-4"
                >

                    <a
                        href="#solution"
                        class="inline-flex
                               items-center justify-center
                               gap-2
                               px-7 py-4
                               rounded-2xl
                               bg-slate-950
                               text-white
                               font-bold
                               shadow-xl
                               hover:bg-slate-800
                               hover:-translate-y-1
                               transition"
                    >

                        Découvrir e-Stockly

                        <svg
                            class="w-5 h-5"
                            fill="none"
                            stroke="currentColor"
                            viewBox="0 0 24 24"
                        >

                            <path
                                stroke-linecap="round"
                                stroke-linejoin="round"
                                stroke-width="2"
                                d="M14 5l7 7m0 0l-7 7m7-7H3"
                            />

                        </svg>

                    </a>


                    <a
                        href="{{ route('login') }}"
                        class="inline-flex
                               items-center justify-center
                               px-7 py-4
                               rounded-2xl
                               border border-slate-300
                               bg-white
                               text-slate-800
                               font-bold
                               hover:border-orange-400
                               hover:text-orange-600
                               transition"
                    >
                        Se connecter
                    </a>

                </div>


                <!-- CONFIANCE -->

                <div
                    class="mt-8
                           flex flex-wrap
                           gap-x-6 gap-y-3
                           text-sm
                           font-semibold
                           text-slate-500"
                >

                    <span class="flex items-center gap-2">
                        <span class="text-orange-500">✓</span>
                        Simple
                    </span>

                    <span class="flex items-center gap-2">
                        <span class="text-orange-500">✓</span>
                        Centralisé
                    </span>

                    <span class="flex items-center gap-2">
                        <span class="text-orange-500">✓</span>
                        Traçable
                    </span>

                </div>

            </div>



            <!-- ================================================= -->
            <!-- VISUEL DASHBOARD + TELEPHONE -->
            <!-- ================================================= -->

            <div class="lg:col-span-7 relative">


                <!-- HALO -->

                <div
                    class="absolute inset-8
                           bg-orange-400/20
                           blur-3xl
                           rounded-full"
                >
                </div>


                <!-- INTERFACES -->

                <div
                    class="relative
                           dashboard-frame
                           pb-10 sm:pb-0"
                >


                    <!-- ========================================= -->
                    <!-- GRAND APERÇU DESKTOP -->
                    <!-- ========================================= -->

                    <div
                        class="rounded-[28px]
                               bg-slate-950
                               p-2
                               shadow-dashboard
                               border border-slate-800"
                    >

                        <!-- BARRE NAVIGATEUR -->

                        <div
                            class="h-10
                                   rounded-t-[20px]
                                   bg-slate-900
                                   flex items-center
                                   gap-2 px-4"
                        >

                            <span
                                class="w-3 h-3 rounded-full
                                       bg-red-400/70"
                            ></span>

                            <span
                                class="w-3 h-3 rounded-full
                                       bg-yellow-400/70"
                            ></span>

                            <span
                                class="w-3 h-3 rounded-full
                                       bg-green-400/70"
                            ></span>

                            <div
                                class="ml-5 flex-1
                                       h-5 rounded-md
                                       bg-slate-800/80"
                            >
                            </div>

                        </div>


                        <!-- VRAI DASHBOARD -->

                        <div
                            class="overflow-hidden
                                   rounded-b-[20px]
                                   bg-white"
                        >

                            <img
                                src="{{ asset('images/dashboard-preview.png') }}"
                                alt="Aperçu du tableau de bord e-Stockly"
                                class="w-full h-auto block"
                            >

                        </div>

                    </div>



                    <!-- ========================================= -->
                    <!-- TÉLÉPHONE -->
                    <!-- ========================================= -->

                    {{-- <div
                        class="phone-mockup
                               absolute
                               -bottom-8
                               right-0
                               sm:-right-5
                               lg:-right-10

                               w-[150px]
                               sm:w-[175px]
                               lg:w-[190px]

                               rounded-[30px]
                               sm:rounded-[34px]

                               bg-slate-950
                               p-[6px]

                               border-[3px]
                               border-slate-700

                               shadow-[0_30px_70px_rgba(5,11,24,.38)]

                               z-20"
                    >


                        <!-- ENCOCHE -->

                        <div
                            class="absolute
                                   top-2
                                   left-1/2
                                   -translate-x-1/2
                                   w-14 h-4
                                   rounded-full
                                   bg-slate-950
                                   z-30"
                        >
                        </div>


                        <!-- ÉCRAN -->

                        <div
                            class="rounded-[25px]
                                   sm:rounded-[29px]
                                   overflow-hidden
                                   bg-white
                                   relative"
                        >

                            <img
                                src="{{ asset('images/dashboard-preview.png') }}"
                                alt="Aperçu d'e-Stockly sur téléphone"
                                class="w-full
                                       h-[300px]
                                       sm:h-[360px]
                                       lg:h-[390px]
                                       object-cover
                                       object-left-top
                                       block"
                            >

                        </div>


                        <!-- BARRE DU BAS -->

                        <div
                            class="absolute
                                   bottom-1.5
                                   left-1/2
                                   -translate-x-1/2
                                   w-12 h-1
                                   rounded-full
                                   bg-white/40"
                        >
                        </div>

                    </div> --}}



                    <!-- BADGE -->

                    <div
                        class="absolute
                               -bottom-6
                               -left-6
                               bg-white
                               rounded-2xl
                               shadow-xl
                               border border-slate-100
                               px-5 py-4
                               hidden sm:block"
                    >

                        <div
                            class="flex items-center gap-3"
                        >

                            <div
                                class="w-10 h-10
                                       rounded-xl
                                       bg-orange-100
                                       flex items-center
                                       justify-center"
                            >
                                <span
                                    class="text-orange-600 text-lg"
                                >
                                    ✓
                                </span>
                            </div>


                            <div>

                                <p
                                    class="text-xs
                                           text-slate-500"
                                >
                                    Votre activité
                                </p>

                                <p
                                    class="font-black
                                           text-slate-900"
                                >
                                    sous contrôle
                                </p>

                            </div>

                        </div>

                    </div>

                </div>

            </div>

        </div>

    </div>

</section>



<!-- ========================================================= -->
<!-- LE PROBLÈME -->
<!-- ========================================================= -->

<section class="py-24 bg-white">

    <div
        class="max-w-7xl mx-auto
               px-5 sm:px-6 lg:px-8"
    >

        <div class="max-w-3xl">

            <span
                class="text-orange-500
                       font-black
                       text-xs
                       uppercase
                       tracking-[.18em]"
            >
                Le problème
            </span>


            <h2
                class="mt-4
                       text-4xl sm:text-5xl
                       font-black
                       tracking-tight
                       text-slate-950"
            >
                La gestion de votre activité
                ne devrait pas être compliquée.
            </h2>


            <p
                class="mt-5
                       text-lg
                       text-slate-600
                       leading-8"
            >
                Quand les informations sont dispersées,
                il devient difficile de savoir ce qui se passe
                réellement dans votre activité.
            </p>

        </div>



        <div
            class="mt-14
                   grid md:grid-cols-2
                   lg:grid-cols-4
                   gap-5"
        >


            <!-- PROBLÈME 1 -->

            <div
                class="p-7
                       rounded-3xl
                       bg-slate-50
                       border border-slate-200"
            >

                <div
                    class="w-12 h-12
                           rounded-2xl
                           bg-red-50
                           text-red-500
                           flex items-center
                           justify-center
                           text-xl"
                >
                    !
                </div>


                <h3 class="mt-6 font-black text-lg">
                    Ruptures inattendues
                </h3>


                <p
                    class="mt-3
                           text-sm
                           text-slate-600
                           leading-6"
                >
                    Vous découvrez parfois qu'un produit est
                    presque épuisé alors qu'il est déjà trop tard.
                </p>

            </div>



            <!-- PROBLÈME 2 -->

            <div
                class="p-7
                       rounded-3xl
                       bg-slate-50
                       border border-slate-200"
            >

                <div
                    class="w-12 h-12
                           rounded-2xl
                           bg-orange-50
                           text-orange-500
                           flex items-center
                           justify-center
                           text-xl"
                >
                    ↔
                </div>


                <h3 class="mt-6 font-black text-lg">
                    Mouvements difficiles à suivre
                </h3>


                <p
                    class="mt-3
                           text-sm
                           text-slate-600
                           leading-6"
                >
                    Entrées, sorties et ajustements deviennent
                    difficiles à contrôler.
                </p>

            </div>



            <!-- PROBLÈME 3 -->

            <div
                class="p-7
                       rounded-3xl
                       bg-slate-50
                       border border-slate-200"
            >

                <div
                    class="w-12 h-12
                           rounded-2xl
                           bg-yellow-50
                           text-yellow-600
                           flex items-center
                           justify-center
                           text-xl"
                >
                    ₣
                </div>


                <h3 class="mt-6 font-black text-lg">
                    Visibilité financière limitée
                </h3>


                <p
                    class="mt-3
                           text-sm
                           text-slate-600
                           leading-6"
                >
                    Il est difficile d'avoir une vision claire
                    des ventes, dépenses et résultats.
                </p>

            </div>



            <!-- PROBLÈME 4 -->

            <div
                class="p-7
                       rounded-3xl
                       bg-slate-50
                       border border-slate-200"
            >

                <div
                    class="w-12 h-12
                           rounded-2xl
                           bg-slate-100
                           text-slate-700
                           flex items-center
                           justify-center
                           text-xl"
                >
                    👥
                </div>


                <h3 class="mt-6 font-black text-lg">
                    Manque de contrôle
                </h3>


                <p
                    class="mt-3
                           text-sm
                           text-slate-600
                           leading-6"
                >
                    Vous avez besoin de savoir qui fait quoi
                    dans votre espace de gestion.
                </p>

            </div>

        </div>



        <div
            class="mt-10
                   p-7
                   rounded-3xl
                   bg-orange-50
                   border border-orange-100"
        >

            <p class="font-bold text-slate-800">

                e-Stockly rassemble ces informations dans
                un même espace afin de vous donner une vision
                claire de votre activité.

            </p>

        </div>

    </div>

</section>



<!-- ========================================================= -->
<!-- LA SOLUTION -->
<!-- ========================================================= -->

<section
    id="solution"
    class="py-24
           dark-gradient
           text-white"
>

    <div
        class="max-w-7xl mx-auto
               px-5 sm:px-6 lg:px-8"
    >

        <div
            class="text-center
                   max-w-3xl
                   mx-auto"
        >

            <span
                class="text-orange-400
                       font-black
                       text-xs
                       uppercase
                       tracking-[.18em]"
            >
                La solution
            </span>


            <h2
                class="mt-4
                       text-4xl sm:text-5xl
                       font-black
                       tracking-tight"
            >
                Tout ce dont vous avez besoin
                pour gérer votre activité.
            </h2>


            <p
                class="mt-5
                       text-slate-400
                       text-lg
                       leading-8"
            >
                Une plateforme pensée pour centraliser
                les opérations essentielles de votre activité.
            </p>

        </div>



        @php

            $features = [

                [
                    'icon' => '📦',
                    'title' => 'Gérer vos produits',
                    'text' => 'Ajoutez, organisez et suivez vos produits facilement.'
                ],

                [
                    'icon' => '↔',
                    'title' => 'Contrôler vos mouvements',
                    'text' => 'Suivez les entrées, sorties et ajustements de stock.'
                ],

                [
                    'icon' => '🛒',
                    'title' => 'Suivre vos ventes',
                    'text' => 'Enregistrez vos ventes et visualisez vos performances.'
                ],

                [
                    'icon' => '💰',
                    'title' => 'Suivre votre caisse',
                    'text' => 'Gardez une vision claire de vos opérations financières.'
                ],

                [
                    'icon' => '👥',
                    'title' => 'Gérer votre équipe',
                    'text' => 'Organisez les utilisateurs et leurs droits d’accès.'
                ],

                [
                    'icon' => '🔔',
                    'title' => 'Recevoir des alertes',
                    'text' => 'Soyez informé des stocks faibles ou des ruptures.'
                ],

            ];

        @endphp



        <div
            class="mt-14
                   grid md:grid-cols-2
                   lg:grid-cols-3
                   gap-5"
        >

            @foreach($features as $feature)

                <div
                    class="group
                           p-7
                           rounded-3xl
                           bg-white/[.055]
                           border border-white/10
                           hover:border-orange-400/50
                           hover:bg-white/[.08]
                           transition duration-300"
                >

                    <div
                        class="w-12 h-12
                               rounded-2xl
                               bg-orange-400/10
                               flex items-center
                               justify-center
                               text-xl
                               group-hover:bg-orange-400
                               group-hover:scale-105
                               transition"
                    >
                        {{ $feature['icon'] }}
                    </div>


                    <h3
                        class="mt-6
                               text-lg
                               font-black"
                    >
                        {{ $feature['title'] }}
                    </h3>


                    <p
                        class="mt-3
                               text-sm
                               text-slate-400
                               leading-6"
                    >
                        {{ $feature['text'] }}
                    </p>

                </div>

            @endforeach

        </div>

    </div>

</section>



<!-- ========================================================= -->
<!-- COMMENT ÇA MARCHE -->
<!-- ========================================================= -->

<section
    id="fonctionnement"
    class="py-24
           bg-[#FFF9ED]"
>

    <div
        class="max-w-7xl mx-auto
               px-5 sm:px-6 lg:px-8"
    >

        <div class="text-center">

            <span
                class="text-orange-600
                       font-black
                       text-xs
                       uppercase
                       tracking-[.18em]"
            >
                Simple et progressif
            </span>


            <h2
                class="mt-4
                       text-4xl sm:text-5xl
                       font-black
                       tracking-tight"
            >
                Comment fonctionne e-Stockly ?
            </h2>

        </div>



        <div
            class="mt-16
                   grid sm:grid-cols-2
                   lg:grid-cols-4
                   gap-6"
        >


            <!-- 01 -->

            <div
                class="relative
                       bg-white
                       rounded-3xl
                       border border-orange-100
                       p-8
                       shadow-sm"
            >

                <span
                    class="absolute
                           -top-4
                           left-7
                           w-9 h-9
                           rounded-xl
                           bg-orange-400
                           text-slate-950
                           font-black
                           flex items-center
                           justify-center"
                >
                    01
                </span>


                <div class="pt-3 text-3xl">
                    🏪
                </div>


                <h3
                    class="mt-6
                           font-black
                           text-xl"
                >
                    Créez votre boutique
                </h3>


                <p
                    class="mt-3
                           text-sm
                           text-slate-600
                           leading-6"
                >
                    Créez votre espace et renseignez
                    les informations essentielles de votre activité.
                </p>

            </div>



            <!-- 02 -->

            <div
                class="relative
                       bg-white
                       rounded-3xl
                       border border-orange-100
                       p-8
                       shadow-sm"
            >

                <span
                    class="absolute
                           -top-4
                           left-7
                           w-9 h-9
                           rounded-xl
                           bg-orange-400
                           text-slate-950
                           font-black
                           flex items-center
                           justify-center"
                >
                    02
                </span>


                <div class="pt-3 text-3xl">
                    ⚙️
                </div>


                <h3
                    class="mt-6
                           font-black
                           text-xl"
                >
                    Configurez votre activité
                </h3>


                <p
                    class="mt-3
                           text-sm
                           text-slate-600
                           leading-6"
                >
                    Ajoutez vos produits, catégories,
                    fournisseurs et utilisateurs.
                </p>

            </div>



            <!-- 03 -->

            <div
                class="relative
                       bg-white
                       rounded-3xl
                       border border-orange-100
                       p-8
                       shadow-sm"
            >

                <span
                    class="absolute
                           -top-4
                           left-7
                           w-9 h-9
                           rounded-xl
                           bg-orange-400
                           text-slate-950
                           font-black
                           flex items-center
                           justify-center"
                >
                    03
                </span>


                <div class="pt-3 text-3xl">
                    📊
                </div>


                <h3
                    class="mt-6
                           font-black
                           text-xl"
                >
                    Gérez votre quotidien
                </h3>


                <p
                    class="mt-3
                           text-sm
                           text-slate-600
                           leading-6"
                >
                    Gérez vos ventes, mouvements,
                    commandes et dépenses.
                </p>

            </div>



            <!-- 04 -->

            <div
                class="relative
                       bg-white
                       rounded-3xl
                       border border-orange-100
                       p-8
                       shadow-sm"
            >

                <span
                    class="absolute
                           -top-4
                           left-7
                           w-9 h-9
                           rounded-xl
                           bg-orange-400
                           text-slate-950
                           font-black
                           flex items-center
                           justify-center"
                >
                    04
                </span>


                <div class="pt-3 text-3xl">
                    🚀
                </div>


                <h3
                    class="mt-6
                           font-black
                           text-xl"
                >
                    Pilotez votre activité
                </h3>


                <p
                    class="mt-3
                           text-sm
                           text-slate-600
                           leading-6"
                >
                    Consultez vos données et prenez
                    de meilleures décisions.
                </p>

            </div>

        </div>

    </div>

</section>



<!-- ========================================================= -->
<!-- DÉCOUVERTE DE L'APPLICATION -->
<!-- ========================================================= -->

<section
    class="py-24
           bg-slate-950
           text-white"
>

    <div
        x-data="{ active: 'dashboard' }"
        class="max-w-7xl mx-auto
               px-5 sm:px-6 lg:px-8"
    >

        <div
            class="text-center
                   max-w-3xl
                   mx-auto"
        >

            <span
                class="text-orange-400
                       font-black
                       text-xs
                       uppercase
                       tracking-[.18em]"
            >
                Découvrez l'application
            </span>


            <h2
                class="mt-4
                       text-4xl sm:text-5xl
                       font-black
                       tracking-tight"
            >
                Votre activité.
                <span class="text-orange-400">
                    Un seul espace.
                </span>
            </h2>


            <p
                class="mt-5
                       text-slate-400
                       text-lg"
            >
                Découvrez les principales interfaces
                d'e-Stockly avant même de créer votre boutique.
            </p>

        </div>



        <!-- ONGLETS -->

        <div
            class="mt-12
                   flex flex-wrap
                   justify-center
                   gap-2"
        >

            <button
                @click="active = 'dashboard'"
                :class="active === 'dashboard'
                    ? 'bg-orange-400 text-slate-950'
                    : 'bg-white/5 text-slate-300'"
                class="px-5 py-3
                       rounded-xl
                       font-bold
                       text-sm
                       transition"
            >
                Tableau de bord
            </button>


            <button
                @click="active = 'products'"
                :class="active === 'products'
                    ? 'bg-orange-400 text-slate-950'
                    : 'bg-white/5 text-slate-300'"
                class="px-5 py-3
                       rounded-xl
                       font-bold
                       text-sm
                       transition"
            >
                Produits
            </button>


            <button
                @click="active = 'sales'"
                :class="active === 'sales'
                    ? 'bg-orange-400 text-slate-950'
                    : 'bg-white/5 text-slate-300'"
                class="px-5 py-3
                       rounded-xl
                       font-bold
                       text-sm
                       transition"
            >
                Ventes
            </button>


            <button
                @click="active = 'reports'"
                :class="active === 'reports'
                    ? 'bg-orange-400 text-slate-950'
                    : 'bg-white/5 text-slate-300'"
                class="px-5 py-3
                       rounded-xl
                       font-bold
                       text-sm
                       transition"
            >
                Rapports
            </button>

        </div>



        <!-- APERÇU -->

        <div class="mt-8 max-w-5xl mx-auto">

            <div
                class="rounded-[30px]
                       bg-slate-900
                       border border-white/10
                       p-2
                       shadow-2xl"
            >

                <div
                    class="rounded-[24px]
                           overflow-hidden
                           bg-white"
                >


                    <!-- DASHBOARD -->

                    <div x-show="active === 'dashboard'">

                        <img
                            src="{{ asset('images/dashboard-preview.png') }}"
                            alt="Tableau de bord e-Stockly"
                            class="w-full block"
                        >

                    </div>



                    <!-- PRODUITS -->

                    <div
                        x-show="active === 'products'"
                        class="bg-slate-50"
                    >

                        <img
                            src="{{ asset('images/products-preview.png') }}"
                            alt="Gestion des produits e-Stockly"
                            class="w-full h-auto block"
                        >

                    </div>



                    <!-- VENTES -->

                    <div
                        x-show="active === 'sales'"
                        class="bg-slate-50"
                    >

                        <img
                            src="{{ asset('images/sales-preview.png') }}"
                            alt="Suivi des ventes e-Stockly"
                            class="w-full h-auto block"
                        >

                    </div>



                    <!-- RAPPORTS -->

                    <div
                        x-show="active === 'reports'"
                        class="bg-slate-50"
                    >

                        <img
                            src="{{ asset('images/reports-preview.png') }}"
                            alt="Rapports et analyses e-Stockly"
                            class="w-full h-auto block"
                        >

                    </div>

                        </div>

                    </div>

                </div>

            </div>

        </div>

    </div>

</section>



<!-- ========================================================= -->
<!-- POUR QUI ? -->
<!-- ========================================================= -->

<section
    id="pourquoi"
    class="py-24
           bg-white"
>

    <div
        class="max-w-7xl mx-auto
               px-5 sm:px-6 lg:px-8"
    >

        <div class="max-w-3xl">

            <span
                class="text-orange-500
                       font-black
                       text-xs
                       uppercase
                       tracking-[.18em]"
            >
                Pour qui ?
            </span>


            <h2
                class="mt-4
                       text-4xl sm:text-5xl
                       font-black
                       tracking-tight"
            >
                Une solution pensée
                pour votre réalité.
            </h2>

        </div>



        <div
            class="mt-14
                   grid md:grid-cols-2
                   gap-6"
        >


            <!-- COMMERÇANTS -->

            <div
                class="p-8
                       rounded-3xl
                       border border-slate-200
                       hover:border-orange-300
                       hover:shadow-xl
                       transition"
            >

                <div class="text-4xl">
                    🏪
                </div>


                <h3
                    class="mt-6
                           text-2xl
                           font-black"
                >
                    Commerçants
                </h3>


                <p
                    class="mt-3
                           text-slate-600
                           leading-7"
                >
                    Gardez le contrôle de vos produits,
                    ventes, dépenses et stocks.
                </p>

            </div>



            <!-- ENTREPRISES -->

            <div
                class="p-8
                       rounded-3xl
                       border border-slate-200
                       hover:border-orange-300
                       hover:shadow-xl
                       transition"
            >

                <div class="text-4xl">
                    🏢
                </div>


                <h3
                    class="mt-6
                           text-2xl
                           font-black"
                >
                    Entreprises
                </h3>


                <p
                    class="mt-3
                           text-slate-600
                           leading-7"
                >
                    Centralisez vos opérations et donnez
                    aux membres de votre équipe les accès appropriés.
                </p>

            </div>



            <!-- GESTIONNAIRES -->

            <div
                class="p-8
                       rounded-3xl
                       border border-slate-200
                       hover:border-orange-300
                       hover:shadow-xl
                       transition"
            >

                <div class="text-4xl">
                    📦
                </div>


                <h3
                    class="mt-6
                           text-2xl
                           font-black"
                >
                    Gestionnaires de stock
                </h3>


                <p
                    class="mt-3
                           text-slate-600
                           leading-7"
                >
                    Suivez les entrées, sorties, ajustements
                    et alertes de votre stock.
                </p>

            </div>



            <!-- RESPONSABLES -->

            <div
                class="p-8
                       rounded-3xl
                       border border-slate-200
                       hover:border-orange-300
                       hover:shadow-xl
                       transition"
            >

                <div class="text-4xl">
                    👤
                </div>


                <h3
                    class="mt-6
                           text-2xl
                           font-black"
                >
                    Responsables
                </h3>


                <p
                    class="mt-3
                           text-slate-600
                           leading-7"
                >
                    Disposez d'une vision globale pour
                    suivre l'activité et prendre vos décisions.
                </p>

            </div>

        </div>

    </div>

</section>



<!-- ========================================================= -->
<!-- POURQUOI e-Stockly -->
<!-- ========================================================= -->

<section
    class="py-24
           bg-[#FFF9ED]"
>

    <div
        class="max-w-7xl mx-auto
               px-5 sm:px-6 lg:px-8"
    >

        <div
            class="text-center
                   max-w-3xl
                   mx-auto"
        >

            <span
                class="text-orange-600
                       font-black
                       text-xs
                       uppercase
                       tracking-[.18em]"
            >
                Pourquoi e-Stockly ?
            </span>


            <h2
                class="mt-4
                       text-4xl sm:text-5xl
                       font-black
                       tracking-tight"
            >
                Plus qu'un outil de stock.
            </h2>


            <p
                class="mt-5
                       text-lg
                       text-slate-600"
            >
                Une plateforme pour avoir une vision plus claire
                de votre activité.
            </p>

        </div>



        <div
            class="mt-14
                   grid md:grid-cols-2
                   lg:grid-cols-4
                   gap-5"
        >


            <div
                class="bg-white
                       rounded-3xl
                       p-7
                       border border-orange-100"
            >

                <div class="text-orange-500 text-3xl">
                    ⚡
                </div>

                <h3 class="mt-5 font-black text-lg">
                    Simple
                </h3>

                <p
                    class="mt-3
                           text-sm
                           text-slate-600
                           leading-6"
                >
                    Une interface pensée pour aller rapidement
                    à l'essentiel.
                </p>

            </div>



            <div
                class="bg-white
                       rounded-3xl
                       p-7
                       border border-orange-100"
            >

                <div class="text-orange-500 text-3xl">
                    🧩
                </div>

                <h3 class="mt-5 font-black text-lg">
                    Centralisé
                </h3>

                <p
                    class="mt-3
                           text-sm
                           text-slate-600
                           leading-6"
                >
                    Vos principales informations sont regroupées
                    dans un même espace.
                </p>

            </div>



            <div
                class="bg-white
                       rounded-3xl
                       p-7
                       border border-orange-100"
            >

                <div class="text-orange-500 text-3xl">
                    🔎
                </div>

                <h3 class="mt-5 font-black text-lg">
                    Traçable
                </h3>

                <p
                    class="mt-3
                           text-sm
                           text-slate-600
                           leading-6"
                >
                    Suivez les opérations importantes
                    réalisées dans votre activité.
                </p>

            </div>



            <div
                class="bg-white
                       rounded-3xl
                       p-7
                       border border-orange-100"
            >

                <div class="text-orange-500 text-3xl">
                    📱
                </div>

                <h3 class="mt-5 font-black text-lg">
                    Accessible
                </h3>

                <p
                    class="mt-3
                           text-sm
                           text-slate-600
                           leading-6"
                >
                    Retrouvez votre espace de gestion
                    depuis vos appareils compatibles.
                </p>

            </div>

        </div>

    </div>

</section>



<!-- ========================================================= -->
<!-- SÉCURITÉ -->
<!-- ========================================================= -->

<section
    class="py-24
           bg-white"
>

    <div
        class="max-w-6xl mx-auto
               px-5 sm:px-6 lg:px-8"
    >

        <div
            class="rounded-[32px]
                   bg-slate-950
                   overflow-hidden
                   relative"
        >

            <div
                class="absolute top-0 right-0
                       w-96 h-96
                       bg-orange-400/10
                       blur-3xl
                       rounded-full"
            >
            </div>


            <div
                class="relative
                       p-10 sm:p-14
                       grid lg:grid-cols-2
                       gap-12
                       items-center"
            >

                <div>

                    <span
                        class="text-orange-400
                               font-black
                               text-xs
                               uppercase
                               tracking-[.18em]"
                    >
                        Confiance
                    </span>


                    <h2
                        class="mt-4
                               text-4xl
                               font-black
                               text-white"
                    >
                        Vos données.
                        Votre activité.
                        Votre contrôle.
                    </h2>


                    <p
                        class="mt-5
                               text-slate-400
                               leading-7"
                    >
                        e-Stockly intègre une gestion des utilisateurs,
                        des rôles et des accès afin de mieux contrôler
                        votre espace.
                    </p>

                </div>



                <div
                    class="grid sm:grid-cols-2
                           gap-4"
                >


                    <div
                        class="p-5
                               rounded-2xl
                               bg-white/5
                               border border-white/10"
                    >

                        <div class="text-orange-400 text-2xl">
                            🔐
                        </div>

                        <h3
                            class="mt-3
                                   text-white
                                   font-bold"
                        >
                            Accès contrôlés
                        </h3>

                        <p
                            class="mt-2
                                   text-xs
                                   text-slate-400"
                        >
                            Gérez les accès des utilisateurs.
                        </p>

                    </div>



                    <div
                        class="p-5
                               rounded-2xl
                               bg-white/5
                               border border-white/10"
                    >

                        <div class="text-orange-400 text-2xl">
                            👥
                        </div>

                        <h3
                            class="mt-3
                                   text-white
                                   font-bold"
                        >
                            Utilisateurs
                        </h3>

                        <p
                            class="mt-2
                                   text-xs
                                   text-slate-400"
                        >
                            Organisez votre équipe.
                        </p>

                    </div>



                    <div
                        class="p-5
                               rounded-2xl
                               bg-white/5
                               border border-white/10"
                    >

                        <div class="text-orange-400 text-2xl">
                            📝
                        </div>

                        <h3
                            class="mt-3
                                   text-white
                                   font-bold"
                        >
                            Traçabilité
                        </h3>

                        <p
                            class="mt-2
                                   text-xs
                                   text-slate-400"
                        >
                            Gardez une vision des opérations.
                        </p>

                    </div>



                    <div
                        class="p-5
                               rounded-2xl
                               bg-white/5
                               border border-white/10"
                    >

                        <div class="text-orange-400 text-2xl">
                            🛡️
                        </div>

                        <h3
                            class="mt-3
                                   text-white
                                   font-bold"
                        >
                            Contrôle
                        </h3>

                        <p
                            class="mt-2
                                   text-xs
                                   text-slate-400"
                        >
                            Votre espace reste organisé.
                        </p>

                    </div>

                </div>

            </div>

        </div>

    </div>

</section>



<!-- ========================================================= -->
<!-- FAQ -->
<!-- ========================================================= -->

<section
    id="faq"
    class="py-24
           bg-slate-50"
>

    <div
        x-data="{ open: null }"
        class="max-w-4xl mx-auto
               px-5 sm:px-6 lg:px-8"
    >

        <div class="text-center">

            <span
                class="text-orange-500
                       font-black
                       text-xs
                       uppercase
                       tracking-[.18em]"
            >
                Questions fréquentes
            </span>


            <h2
                class="mt-4
                       text-4xl sm:text-5xl
                       font-black
                       tracking-tight"
            >
                Vous avez des questions ?
            </h2>

        </div>



        @php

            $questions = [

                [
                    'q' => 'Qu’est-ce qu’e-Stockly ?',
                    'a' => 'e-Stockly est une plateforme de gestion qui permet de centraliser les informations liées à votre stock et à votre activité.'
                ],

                [
                    'q' => 'Que signifie créer une boutique ?',
                    'a' => 'Créer une boutique signifie créer votre propre espace de gestion e-Stoky dans lequel vous pourrez configurer votre activité.'
                ],

                [
                    'q' => 'Puis-je avoir plusieurs utilisateurs ?',
                    'a' => 'Oui, l’espace de gestion est conçu pour permettre la gestion des utilisateurs et de leurs accès.'
                ],

                [
                    'q' => 'Puis-je accéder à mon compte après l’avoir créé ?',
                    'a' => 'Oui. Une fois votre espace créé, vous pourrez utiliser votre compte pour vous connecter à votre espace de gestion.'
                ],

                [
                    'q' => 'Dois-je être informaticien pour utiliser e-Stockly ?',
                    'a' => 'Non. L’objectif d’e-Stockly est justement de proposer une gestion accessible et simple à utiliser.'
                ],

            ];

        @endphp



        <div class="mt-12 space-y-3">

            @foreach($questions as $index => $question)

                <div
                    class="bg-white
                           rounded-2xl
                           border border-slate-200
                           overflow-hidden"
                >

                    <button
                        @click="open === {{ $index }}
                            ? open = null
                            : open = {{ $index }}"
                        class="w-full
                               px-6 py-5
                               flex items-center
                               justify-between
                               text-left
                               font-bold"
                    >

                        <span>
                            {{ $question['q'] }}
                        </span>


                        <svg
                            class="w-5 h-5
                                   text-orange-500
                                   transition-transform"
                            :class="open === {{ $index }}
                                ? 'rotate-180'
                                : ''"
                            fill="none"
                            stroke="currentColor"
                            viewBox="0 0 24 24"
                        >

                            <path
                                stroke-linecap="round"
                                stroke-linejoin="round"
                                stroke-width="2"
                                d="M19 9l-7 7-7-7"
                            />

                        </svg>

                    </button>


                    <div
                        x-show="open === {{ $index }}"
                        x-collapse
                        class="px-6 pb-6
                               text-sm
                               text-slate-600
                               leading-7"
                    >
                        {{ $question['a'] }}
                    </div>

                </div>

            @endforeach

        </div>

    </div>

</section>



<!-- ========================================================= -->
<!-- CTA -->
<!-- ========================================================= -->

<section
    class="py-24
           bg-white"
>

    <div
        class="max-w-6xl mx-auto
               px-5 sm:px-6 lg:px-8"
    >

        <div
            class="relative
                   overflow-hidden
                   rounded-[36px]
                   orange-gradient
                   p-10 sm:p-16"
        >

            <div
                class="absolute
                       -right-20
                       -top-20
                       w-72 h-72
                       rounded-full
                       bg-white/10"
            >
            </div>


            <div
                class="absolute
                       -left-20
                       -bottom-24
                       w-72 h-72
                       rounded-full
                       bg-white/10"
            >
            </div>


            <div
                class="relative
                       text-center
                       max-w-3xl
                       mx-auto"
            >

                <span
                    class="inline-flex
                           px-4 py-2
                           rounded-full
                           bg-slate-950/10
                           text-slate-950
                           text-xs
                           font-black
                           uppercase
                           tracking-wide"
                >
                    Votre espace vous attend
                </span>


                <h2
                    class="mt-6
                           text-4xl sm:text-5xl
                           font-black
                           text-slate-950"
                >
                    Prêt à créer votre boutique ?
                </h2>


                <p
                    class="mt-5
                           text-slate-900/75
                           text-lg
                           leading-8"
                >
                    Créez votre espace e-Stockly,
                    configurez votre activité et commencez
                    à mieux gérer votre quotidien.
                </p>


                <div
                    class="mt-9
                           flex flex-col sm:flex-row
                           justify-center
                           gap-4"
                >

                    <a
                        href="{{ route('register') }}"
                        class="px-8 py-4
                               rounded-2xl
                               bg-slate-950
                               text-white
                               font-black
                               shadow-xl
                               hover:bg-slate-800
                               hover:-translate-y-1
                               transition"
                    >
                        Créer ma boutique
                    </a>


                    <a
                        href="{{ route('login') }}"
                        class="px-8 py-4
                               rounded-2xl
                               bg-white/80
                               text-slate-950
                               font-black
                               hover:bg-white
                               transition"
                    >
                        J'ai déjà un compte
                    </a>

                </div>

            </div>

        </div>

    </div>

</section>


</main>



<!-- ========================================================= -->
<!-- FOOTER -->
<!-- ========================================================= -->

<footer
    class="bg-slate-950
           text-white"
>

    <div
        class="max-w-7xl mx-auto
               px-5 sm:px-6 lg:px-8
               py-14"
    >

        <div
            class="grid md:grid-cols-4
                   gap-10"
        >


            <!-- MARQUE -->

            <div class="md:col-span-2">

                <div
                    class="inline-flex items-center
                           bg-white
                           rounded-2xl
                           px-2 py-1"
                >

                    <img
                        src="{{ asset('images/logo.png') }}"
                        alt="e-Stockly — Gestion de stock centralisée"
                        class="h-16
                               w-auto
                               object-contain"
                    >

                </div>


                <p
                    class="mt-5
                           max-w-md
                           text-sm
                           text-slate-400
                           leading-6"
                >
                    Une solution de gestion pensée pour vous
                    aider à garder le contrôle de votre stock
                    et de votre activité.
                </p>

            </div>



            <!-- NAVIGATION -->

            <div>

                <h3 class="font-black">
                    Navigation
                </h3>


                <div
                    class="mt-5
                           space-y-3
                           text-sm
                           text-slate-400"
                >

                    <a
                        href="#accueil"
                        class="block
                               hover:text-orange-400
                               transition"
                    >
                        Accueil
                    </a>

                    <a
                        href="#solution"
                        class="block
                               hover:text-orange-400
                               transition"
                    >
                        Fonctionnalités
                    </a>

                    <a
                        href="#fonctionnement"
                        class="block
                               hover:text-orange-400
                               transition"
                    >
                        Comment ça marche
                    </a>

                    <a
                        href="#faq"
                        class="block
                               hover:text-orange-400
                               transition"
                    >
                        FAQ
                    </a>

                </div>

            </div>



            <!-- COMPTE -->

            <div>

                <h3 class="font-black">
                    Votre compte
                </h3>


                <div
                    class="mt-5
                           space-y-3
                           text-sm
                           text-slate-400"
                >

                    <a
                        href="{{ route('login') }}"
                        class="block
                               hover:text-orange-400
                               transition"
                    >
                        Se connecter
                    </a>


                    <a
                        href="{{ route('register') }}"
                        class="block
                               hover:text-orange-400
                               transition"
                    >
                        Créer ma boutique
                    </a>

                </div>

            </div>

        </div>



        <!-- COPYRIGHT -->

        <div
            class="mt-12
                   pt-7
                   border-t border-white/10
                   flex flex-col
                   sm:flex-row
                   justify-between
                   gap-4
                   text-xs
                   text-slate-500"
        >

            <p>
                © {{ date('Y') }}
                e-Stockly.
                Tous droits réservés.
            </p>


            <div class="flex gap-5">

                <a
                    href="#"
                    class="hover:text-white transition"
                >
                    Conditions
                </a>

                <a
                    href="#"
                    class="hover:text-white transition"
                >
                    Confidentialité
                </a>

            </div>

        </div>

    </div>

</footer>


</body>
</html>