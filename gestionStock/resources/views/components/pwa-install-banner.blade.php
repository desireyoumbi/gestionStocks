{{-- Bannière d'installation PWA personnalisée --}}
<div id="pwa-install-banner"
     class="hidden fixed bottom-0 left-0 right-0 z-50 bg-gray-900 text-white px-5 py-4 flex items-center justify-between gap-4 shadow-2xl">
    <div class="flex items-center gap-3">
        <div class="w-10 h-10 bg-amber-500 rounded-xl flex items-center justify-center flex-shrink-0">
            <svg class="w-6 h-6 text-gray-900" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4" />
            </svg>
        </div>
        <div>
            <p class="font-bold text-sm">Installer e-Stockly</p>
            <p class="text-xs text-gray-400">Accès rapide depuis votre écran d'accueil</p>
        </div>
    </div>
    <div class="flex items-center gap-2 flex-shrink-0">
        <button id="pwa-install-confirm" class="bg-amber-500 hover:bg-amber-400 text-gray-900 font-bold text-xs px-4 py-2 rounded-xl transition">
            Installer
        </button>
        <button id="pwa-install-dismiss" aria-label="Fermer" class="text-gray-400 hover:text-white p-2">
            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
            </svg>
        </button>
    </div>
</div>

<script>
    (function () {
        let deferredPrompt = null;
        const banner = document.getElementById('pwa-install-banner');
        const dismissBtn = document.getElementById('pwa-install-dismiss');
        const confirmBtn = document.getElementById('pwa-install-confirm');

        const DISMISS_KEY = 'pwa_install_dismissed_until';

        function isDismissedRecently() {
            const until = localStorage.getItem(DISMISS_KEY);
            return until && Date.now() < parseInt(until, 10);
        }

        // Vérification EN DIRECT uniquement — pas de drapeau permanent,
        // qui resterait bloqué à tort après une désinstallation.
        function isRunningAsInstalledApp() {
            return window.matchMedia('(display-mode: standalone)').matches
                || window.navigator.standalone === true; // iOS Safari
        }

        window.addEventListener('beforeinstallprompt', function (event) {
            event.preventDefault();
            deferredPrompt = event;

            if (!isRunningAsInstalledApp() && !isDismissedRecently()) {
                banner.classList.remove('hidden');
            }
        });

        async function triggerInstall() {
            banner.classList.add('hidden');
            if (!deferredPrompt) {
                alert("L'installation n'est pas disponible pour le moment sur ce navigateur.");
                return;
            }
            deferredPrompt.prompt();
            const { outcome } = await deferredPrompt.userChoice;
            deferredPrompt = null;
        }

        confirmBtn.addEventListener('click', triggerInstall);

        dismissBtn.addEventListener('click', function () {
            banner.classList.add('hidden');
            const septJoursEnMs = 7 * 24 * 60 * 60 * 1000;
            localStorage.setItem(DISMISS_KEY, Date.now() + septJoursEnMs);
        });

        window.addEventListener('appinstalled', function () {
            banner.classList.add('hidden');
            deferredPrompt = null;
        });

        window.installPwaApp = triggerInstall;
    })();
</script>