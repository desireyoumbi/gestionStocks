@extends('layouts.app')
@section('header_title', ' Scan & Stock')
@section('header_subtitle', 'Mouvements rapides par douchette ou caméra')

@section('content')
<div class="max-w-2xl mx-auto space-y-6">

    {{-- Message d'erreur ou de succès --}}
    @if(session('error'))
        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded-2xl shadow-sm text-sm">
            {{ session('error') }}
        </div>
    @endif

    @if(session('succes'))
        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded-2xl shadow-sm text-sm">
            {{ session('succes') }}
        </div>
    @endif

    {{-- Bloc principal du Scanner --}}
    <div class="bg-white p-8 rounded-3xl shadow-md border border-gray-100 text-center space-y-4">
        <div class="w-16 h-16 bg-amber-50 text-amber-500 rounded-3xl mx-auto flex items-center justify-center shadow-inner">
            <svg class="w-8 h-8" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v1m6 11h2m-6 0h-2v4m0-11v3m0 0h.01M12 12h4.01M16 20h4M4 12h4m12 0h.01M5 8h2a1 1 0 001-1V5a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1zm12 0h2a1 1 0 001-1V5a1 1 0 00-1-1h-2a1 1 0 00-1 1v2a1 1 0 001 1zM5 20h2a1 1 0 001-1v-2a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1z" />
            </svg>
        </div>

        <div>
            <h1 class="text-xl font-bold text-gray-900">Mode Douchette / Scan Rapide</h1>
            <p class="text-xs text-gray-400 mt-1">Choisissez l'action souhaitée, puis passez le QR code (douchette ou caméra) ou entrez la référence.</p>
        </div>

        <form id="scan-form" action="{{ route('stock.scan.process') }}" method="POST" class="mt-6 space-y-4">
            @csrf

            {{-- Sélecteur d'action (Alpine.js pour gérer l'état visuel actif) --}}
            <div x-data="{ action: 'edit' }" class="grid grid-cols-3 gap-2 bg-gray-50 p-1.5 rounded-2xl border border-gray-200">

                <label @click="action = 'edit'" :class="action === 'edit' ? 'bg-amber-500 text-gray-900 font-bold shadow-sm' : 'text-gray-500 hover:text-gray-900'" class="cursor-pointer py-2 px-2 rounded-xl text-xs transition text-center flex items-center justify-center">
                    <input type="radio" name="action_type" value="edit" x-model="action" class="hidden" checked>
                    Modifier
                </label>

                <label @click="action = 'sell'" :class="action === 'sell' ? 'bg-amber-500 text-gray-900 font-bold shadow-sm' : 'text-gray-500 hover:text-gray-900'" class="cursor-pointer py-2 px-2 rounded-xl text-xs transition text-center flex items-center justify-center">
                    <input type="radio" name="action_type" value="sell" x-model="action" class="hidden">
                    Vendre
                </label>

                <label @click="action = 'supply'" :class="action === 'supply' ? 'bg-amber-500 text-gray-900 font-bold shadow-sm' : 'text-gray-500 hover:text-gray-900'" class="cursor-pointer py-2 px-2 rounded-xl text-xs transition text-center flex items-center justify-center">
                    <input type="radio" name="action_type" value="supply" x-model="action" class="hidden">
                    Approvisionner
                </label>

            </div>

            <div>
                <input type="text" name="reference" id="reference-input" autofocus autocomplete="off"
                       class="w-full bg-gray-50 border border-gray-200 rounded-2xl px-5 py-4 text-center font-mono font-bold text-lg focus:outline-none focus:ring-2 focus:ring-amber-500/20 focus:border-amber-500"
                       placeholder="Ex: cl-12">
            </div>

            {{-- Bouton pour ouvrir le scan caméra --}}
            <button type="button" id="open-camera-btn"
                    class="w-full bg-white border-2 border-dashed border-amber-300 hover:border-amber-500 text-amber-600 font-bold py-3 rounded-2xl transition text-xs flex items-center justify-center gap-2">
                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z" />
                    <path stroke-linecap="round" stroke-linejoin="round" d="M15 13a3 3 0 11-6 0 3 3 0 016 0z" />
                </svg>
                Scanner avec la caméra
            </button>

            <button type="submit" class="w-full bg-gray-900 hover:bg-gray-800 text-white font-bold py-3.5 rounded-2xl transition shadow-md text-xs">
                Valider la référence
            </button>
        </form>

        {{-- Zone caméra, cachée par défaut --}}
        <div id="qr-reader-container" class="hidden space-y-3">
            <div id="qr-reader" class="rounded-2xl overflow-hidden mx-auto max-w-sm"></div>
            <button type="button" id="close-camera-btn"
                    class="w-full bg-gray-100 hover:bg-gray-200 text-gray-700 font-bold py-2.5 rounded-2xl transition text-xs">
                Annuler le scan
            </button>
        </div>
    </div>

</div>

{{-- Bibliothèque de lecture QR côté navigateur (aucune donnée envoyée à un serveur externe, tout se passe localement) --}}
<script src="https://unpkg.com/html5-qrcode@2.3.8/html5-qrcode.min.js"></script>
<script>
    (function () {
        let html5QrCode = null;

        const container = document.getElementById('qr-reader-container');
        const openBtn = document.getElementById('open-camera-btn');
        const closeBtn = document.getElementById('close-camera-btn');
        const referenceInput = document.getElementById('reference-input');
        const scanForm = document.getElementById('scan-form');

        function stopScanner() {
            if (html5QrCode) {
                html5QrCode.stop().catch(() => {}).finally(() => {
                    html5QrCode.clear();
                    html5QrCode = null;
                });
            }
            container.classList.add('hidden');
        }

        function onScanSuccess(decodedText) {
            referenceInput.value = decodedText;
            stopScanner();
            // Soumission automatique, comme le ferait l'Entrée envoyée par une douchette
            scanForm.submit();
        }

        openBtn.addEventListener('click', function () {
            container.classList.remove('hidden');
            html5QrCode = new Html5Qrcode("qr-reader");

            html5QrCode.start(
                { facingMode: "environment" },
                { fps: 10, qrbox: 220 },
                onScanSuccess,
                function onScanFailure() {
                    // Appelé en continu tant qu'aucun QR code n'est détecté dans l'image : rien à faire ici.
                }
            ).catch(function (err) {
                alert("Impossible d'accéder à la caméra : " + err);
                container.classList.add('hidden');
            });
        });

        closeBtn.addEventListener('click', stopScanner);
    })();
</script>
@endsection