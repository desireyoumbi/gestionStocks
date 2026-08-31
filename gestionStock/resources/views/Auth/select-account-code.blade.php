<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Choisir votre compte - E-Stockly</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="min-h-screen flex items-center justify-center bg-slate-50 px-4">
    <div class="max-w-md w-full bg-white p-8 rounded-3xl shadow-xl border border-slate-100">

        <div class="mb-8">
            <p class="text-xs font-bold tracking-widest text-indigo-600 uppercase">E-Stockly</p>
            <h2 class="text-2xl font-bold text-slate-900 mt-2">Plusieurs comptes trouvés</h2>
            <p class="text-sm text-slate-500 mt-2">Ce numéro est associé à plusieurs comptes. Sélectionnez celui dont vous voulez récupérer le code de connexion.</p>
        </div>

        <div class="space-y-3">
            @foreach($comptes as $compte)
                <form method="POST" action="{{ route('code-connexion.confirm-account', $compte->id) }}">
                    @csrf
                    <button type="submit" class="w-full text-left p-4 border border-slate-200 rounded-xl hover:border-indigo-400 hover:bg-indigo-50/50 transition">
                        <span class="font-bold text-slate-900 block">{{ $compte->boutique->nom ?? 'Boutique' }}</span>
                        <span class="text-xs text-slate-500">Rôle : {{ ucfirst(str_replace('_', ' ', $compte->role)) }}</span>
                    </button>
                </form>
            @endforeach
        </div>

        <p class="text-center text-sm text-slate-600 mt-6">
            <a href="{{ route('login') }}" class="font-semibold text-indigo-600 hover:text-indigo-500">Annuler</a>
        </p>
    </div>
</body>
</html>