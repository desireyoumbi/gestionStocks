<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Code de connexion oublié - E-Stockly</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="min-h-screen flex items-center justify-center bg-[#121620] px-4">
    <div class="max-w-md w-full bg-white p-6 sm:p-8 rounded-3xl shadow-xl border border-slate-100">

        <div class="mb-8">
            <p class="text-xs font-bold tracking-widest text-indigo-600 uppercase">E-Stockly</p>
            <h2 class="text-2xl font-bold text-slate-900 mt-2">Code de connexion oublié</h2>
            <p class="text-sm text-slate-500 mt-2">Entrez le numéro de téléphone associé à votre compte.</p>
        </div>

        @if ($errors->any())
            <div class="bg-red-50 border border-red-200 p-4 rounded-lg text-sm text-red-700 mb-6">
                @foreach ($errors->all() as $error)
                    <p>{{ $error }}</p>
                @endforeach
            </div>
        @endif

        <form method="POST" action="{{ route('code-connexion.find-accounts') }}" class="space-y-5">
            @csrf

            <div>
                <label for="phone" class="block text-sm font-medium text-slate-700 mb-1.5">Numéro de téléphone</label>
                <input type="text" name="phone" id="phone" value="{{ old('phone') }}" required maxlength="15"
                    placeholder="Ex: 678177749"
                    class="w-full px-3.5 py-2.5 border border-slate-300 rounded-lg shadow-sm text-sm text-slate-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition">
            </div>

            <button type="submit"
                class="w-full py-3 px-4 rounded-lg text-sm font-semibold text-white bg-indigo-600 hover:bg-indigo-700 transition">
                Continuer
            </button>
        </form>

        <p class="text-center text-sm text-slate-600 mt-6">
            <a href="{{ route('login') }}" class="font-semibold text-indigo-600 hover:text-indigo-500">Retour à la connexion</a>
        </p>
    </div>
</body>
</html>