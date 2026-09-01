<!DOCTYPE html>
<html lang="fr" class="dark">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $boutique->nom }} - Platform Admin</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script>tailwind.config = { darkMode: 'class' }</script>
</head>
<body class="bg-gray-950 text-gray-100 min-h-screen font-sans">
    <div class="max-w-4xl mx-auto px-4 sm:px-6 py-10 space-y-6">

        <div class="flex items-center gap-3">
            <a href="{{ route('platform-admin.dashboard') }}" class="w-9 h-9 bg-gray-900 border border-gray-800 rounded-xl flex items-center justify-center text-gray-400 hover:text-white transition">
                <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                </svg>
            </a>
            <div>
                <p class="text-xs font-bold tracking-widest text-amber-500 uppercase">Détail boutique</p>
                <h1 class="text-2xl font-black mt-0.5">{{ $boutique->nom }}</h1>
            </div>
            <span class="ml-auto px-3 py-1 rounded-full text-xs font-bold {{ $boutique->statut === 'active' ? 'bg-emerald-500/10 text-emerald-400' : 'bg-red-500/10 text-red-400' }}">
                {{ ucfirst($boutique->statut) }}
            </span>
        </div>

        @if(session('success'))
            <div class="bg-emerald-500/10 border border-emerald-500/30 text-emerald-400 p-4 rounded-xl text-sm">
                {{ session('success') }}
            </div>
        @endif
        @if(session('error'))
            <div class="bg-red-500/10 border border-red-500/30 text-red-400 p-4 rounded-xl text-sm">
                {{ session('error') }}
            </div>
        @endif

        <div class="bg-gray-900 border border-gray-800 rounded-2xl overflow-hidden">
            <div class="p-5 border-b border-gray-800">
                <h2 class="font-bold text-sm">Utilisateurs de cette boutique</h2>
            </div>
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-800">
                    <thead class="bg-gray-950">
                        <tr>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-400 uppercase">Nom</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-400 uppercase">Rôle</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-400 uppercase">Email</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-400 uppercase">Code connexion</th>
                            <th class="px-4 py-3 text-right text-xs font-medium text-gray-400 uppercase">Action</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-800">
                        @forelse($users as $user)
                            <tr class="hover:bg-gray-800/40 transition">
                                <td class="px-4 py-3 text-sm font-semibold">{{ $user->name }}</td>
                                <td class="px-4 py-3 text-sm">
                                    <span class="px-2 py-0.5 rounded-full text-[11px] font-bold
                                        {{ $user->role === 'administrateur' ? 'bg-purple-500/10 text-purple-400' : '' }}
                                        {{ $user->role === 'vice_administrateur' ? 'bg-amber-500/10 text-amber-400' : '' }}
                                        {{ $user->role === 'vendeur' ? 'bg-blue-500/10 text-blue-400' : '' }}">
                                        {{ ucfirst(str_replace('_', ' ', $user->role)) }}
                                    </span>
                                </td>
                                <td class="px-4 py-3 text-sm text-gray-400">{{ $user->email ?? 'Non renseigné' }}</td>
                                <td class="px-4 py-3 text-sm font-mono text-gray-400">{{ $user->code_connexion }}</td>
                                <td class="px-4 py-3 text-right">
                                    @if($user->email)
                                        <form method="POST" action="{{ route('platform-admin.users.reinitialiser-mot-de-passe', $user->id) }}" onsubmit="return confirm('Générer un nouveau mot de passe et l\'envoyer à {{ $user->email }} ?');">
                                            @csrf
                                            <button type="submit" class="px-3 py-1.5 bg-amber-500/10 text-amber-400 hover:bg-amber-500 hover:text-gray-950 rounded-lg text-xs font-bold transition">
                                                Réinitialiser mot de passe
                                            </button>
                                        </form>
                                    @else
                                        <span class="text-xs text-gray-600 italic">Aucun email</span>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="px-4 py-6 text-center text-sm text-gray-500">Aucun utilisateur dans cette boutique.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</body>
</html>