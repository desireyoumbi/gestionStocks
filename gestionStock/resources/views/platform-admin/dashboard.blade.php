<!DOCTYPE html>
<html lang="fr" class="dark">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Platform Admin - E-Stockly</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script>tailwind.config = { darkMode: 'class' }</script>
</head>
<body class="bg-gray-950 text-gray-100 min-h-screen font-sans">
    <div class="max-w-6xl mx-auto px-4 sm:px-6 py-10 space-y-8">

        <div class="flex items-center justify-between">
            <div>
                <p class="text-xs font-bold tracking-widest text-amber-500 uppercase">E-Stockly</p>
                <h1 class="text-2xl font-black mt-1">Administration de la plateforme</h1>
            </div>
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit" class="text-xs font-medium text-red-400 hover:text-red-300">Déconnexion</button>
            </form>
        </div>

        @if(session('success'))
            <div class="bg-emerald-500/10 border border-emerald-500/30 text-emerald-400 p-4 rounded-xl text-sm">
                {{ session('success') }}
            </div>
        @endif

        {{-- Statistiques globales --}}
        <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
            <div class="bg-gray-900 border border-gray-800 rounded-2xl p-5">
                <p class="text-xs text-gray-400 uppercase font-bold tracking-wider">Total boutiques</p>
                <p class="text-3xl font-black mt-1">{{ $totalBoutiques }}</p>
            </div>
            <div class="bg-gray-900 border border-gray-800 rounded-2xl p-5">
                <p class="text-xs text-gray-400 uppercase font-bold tracking-wider">Actives</p>
                <p class="text-3xl font-black mt-1 text-emerald-400">{{ $totalActives }}</p>
            </div>
            <div class="bg-gray-900 border border-gray-800 rounded-2xl p-5">
                <p class="text-xs text-gray-400 uppercase font-bold tracking-wider">Suspendues</p>
                <p class="text-3xl font-black mt-1 text-red-400">{{ $totalSuspendues }}</p>
            </div>
        </div>

        {{-- Tableau des boutiques --}}
        <div class="bg-gray-900 border border-gray-800 rounded-2xl overflow-hidden">
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-800">
                    <thead class="bg-gray-950">
                        <tr>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-400 uppercase">Boutique</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-400 uppercase">Utilisateurs</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-400 uppercase">Créée le</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-400 uppercase">Statut</th>
                            <th class="px-4 py-3 text-right text-xs font-medium text-gray-400 uppercase">Action</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-800">
                        @forelse($boutiques as $boutique)
                            <tr class="hover:bg-gray-800/40 transition">
                                <td class="px-4 py-3 text-sm font-semibold">
                                    <a href="{{ route('platform-admin.boutiques.show', $boutique->id) }}" class="hover:text-amber-500 transition">
                                        {{ $boutique->nom }}
                                    </a>
                                </td>
                                <td class="px-4 py-3 text-sm text-gray-400">{{ $boutique->users_count }}</td>
                                <td class="px-4 py-3 text-sm text-gray-400">{{ $boutique->created_at->format('d/m/Y') }}</td>
                                <td class="px-4 py-3 text-sm">
                                    <span class="px-2.5 py-1 rounded-full text-xs font-bold {{ $boutique->statut === 'active' ? 'bg-emerald-500/10 text-emerald-400' : 'bg-red-500/10 text-red-400' }}">
                                        {{ ucfirst($boutique->statut) }}
                                    </span>
                                </td>
                                <td class="px-4 py-3 text-right">
                                    @if($boutique->statut === 'active')
                                        <form method="POST" action="{{ route('platform-admin.suspendre', $boutique->id) }}" onsubmit="return confirm('Suspendre la boutique {{ $boutique->nom }} ? Ses utilisateurs ne pourront plus se connecter.');">
                                            @csrf
                                            <button type="submit" class="px-3 py-1.5 bg-red-500/10 text-red-400 hover:bg-red-500 hover:text-white rounded-lg text-xs font-bold transition">
                                                Suspendre
                                            </button>
                                        </form>
                                    @else
                                        <form method="POST" action="{{ route('platform-admin.reactiver', $boutique->id) }}">
                                            @csrf
                                            <button type="submit" class="px-3 py-1.5 bg-emerald-500/10 text-emerald-400 hover:bg-emerald-500 hover:text-white rounded-lg text-xs font-bold transition">
                                                Réactiver
                                            </button>
                                        </form>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="px-4 py-6 text-center text-sm text-gray-500">Aucune boutique enregistrée.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</body>
</html>