<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Bienvenue - E-Stockly</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        /* Fondu d'entrée de la page */
        body {
            opacity: 0;
            animation: fadeIn 0.6s ease-out forwards;
        }
        @keyframes fadeIn {
            to { opacity: 1; }
        }

        /* Fondu de sortie, déclenché en JS avant la redirection */
        body.fade-out {
            animation: fadeOut 0.5s ease-in forwards;
        }
        @keyframes fadeOut {
            to { opacity: 0; }
        }

        /* Le bouton "Passer" apparaît en douceur après un court délai, plutôt que d'être là brutalement dès le chargement */
        .skip-link {
            opacity: 0;
            animation: revealSkip 0.6s ease-out 1s forwards;
        }
        @keyframes revealSkip {
            to { opacity: 1; }
        }
    </style>
</head>
<body class="bg-[#e2e2e2] h-screen w-screen flex flex-col items-center justify-center overflow-hidden m-0">

    <div class="relative w-full max-w-3xl px-4 flex flex-col items-center">
        <video id="introVideo" class="w-full h-auto object-contain" autoplay muted playsinline>
            <source src="{{ asset('videos/intro.mp4') }}" type="video/mp4">
            Votre navigateur ne prend pas en charge la lecture de vidéos.
        </video>

        <!-- Bouton "Passer" discret, apparaît après un court délai -->
        <div class="skip-link mt-6 text-center">
            <a href="{{ route('home') }}" id="skipLink" class="text-xs sm:text-sm text-slate-400 hover:text-slate-700 font-medium tracking-wide transition">
                Passer l'introduction &rarr;
            </a>
        </div>
    </div>

    <script>
        const video = document.getElementById('introVideo');
        const skipLink = document.getElementById('skipLink');
        const destination = "{{ route('home') }}";

        function goToDestination(e) {
            if (e) e.preventDefault();
            document.body.classList.add('fade-out');
            // On attend la fin de l'animation de fondu avant de vraiment changer de page
            setTimeout(function () {
                window.location.href = destination;
            }, 480);
        }

        // Redirection automatique en fondu dès que la vidéo se termine
        video.addEventListener('ended', function () {
            goToDestination();
        });

        // Le lien "Passer" utilise aussi le fondu plutôt qu'un saut brutal
        skipLink.addEventListener('click', goToDestination);
    </script>

</body>
</html>