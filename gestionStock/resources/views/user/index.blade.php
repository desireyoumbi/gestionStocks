@extends('layouts.app')
@section('header_title', 'Espace utilisateurs')
@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6 space-y-6">
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
        <h1 class="text-2xl font-bold text-gray-900">Gestion des Utilisateurs</h1>
        <div class="flex items-center gap-3">
            {{-- Bouton d'export PDF --}}
            <a href="{{ route('user.export.pdf') }}" class="bg-red-600 text-white px-4 py-2 rounded-md hover:bg-red-700 transition flex items-center gap-2 shadow-sm text-sm">
                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z" />
                </svg>
                Exporter PDF
            </a>

            {{-- Bouton Ajouter --}}
            <a href="{{ route('user.create') }}" class="bg-blue-600 text-white px-4 py-2 rounded-md hover:bg-blue-700 transition shadow-sm text-sm">
                + Ajouter un utilisateur
            </a>
        </div>
    </div>

    {{-- Messages Flash --}}
    @if(session('succes'))
        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative text-sm" role="alert">
            <span class="block sm:inline">{{ session('succes') }}</span>
        </div>
    @endif
    @if(session('success'))
        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative text-sm" role="alert">
            <span class="block sm:inline">{{ session('success') }}</span>
        </div>
    @endif
    @if(session('error'))
        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative text-sm" role="alert">
            <span class="block sm:inline">{{ session('error') }}</span>
        </div>
    @endif

    {{-- SECTION : Matrice explicative des Rôles et Permissions --}}
    <div class="grid grid-cols-1 md:grid-cols-3 gap-4 bg-white p-5 rounded-lg shadow-sm border border-gray-100">
        <!-- Administrateur -->
        <div class="space-y-1.5 p-3 rounded-md bg-purple-50/50 border border-purple-100">
            <span class="inline-block px-2 py-0.5 bg-purple-100 text-purple-800 font-bold text-[10px] uppercase rounded-full">Administrateur</span>
            <h3 class="font-bold text-gray-900 text-xs">Accès Total</h3>
            <p class="text-[11px] text-gray-600">Gestion complète de la boutique, des produits, des paramètres et création/modification des rôles utilisateurs.</p>
        </div>

        <!-- Vice-Administrateur -->
        <div class="space-y-1.5 p-3 rounded-md bg-amber-50/50 border border-amber-100">
            <span class="inline-block px-2 py-0.5 bg-amber-100 text-amber-800 font-bold text-[10px] uppercase rounded-full">Vice-Administrateur</span>
            <h3 class="font-bold text-gray-900 text-xs">Gestion & Supervision</h3>
            <p class="text-[11px] text-gray-600">Ajout et modification de produits, gestion du stock. Pas de droit de suppression définitive.</p>
        </div>

        <!-- Vendeur -->
        <div class="space-y-1.5 p-3 rounded-md bg-blue-50/50 border border-blue-100">
            <span class="inline-block px-2 py-0.5 bg-blue-100 text-blue-800 font-bold text-[10px] uppercase rounded-full">Vendeur</span>
            <h3 class="font-bold text-gray-900 text-xs">Catalogue & Ventes</h3>
            <p class="text-[11px] text-gray-600">Consultation du catalogue (prix d'achat masqué) et enregistrement des ventes et mouvements de stock.</p>
        </div>
    </div>

    {{-- Barre de recherche instantanée et filtres --}}
    <div class="bg-white p-4 rounded-lg shadow flex flex-col sm:flex-row gap-4">
        <div class="flex-1">
            <input type="text" id="liveSearch" placeholder="Recherche instantanée (nom, téléphone, email, code)..." class="w-full border-gray-300 rounded-md shadow-sm focus:border-blue-500 focus:ring-blue-500 text-sm">
        </div>
        <div>
            <form method="GET" action="{{ route('user.index') }}" id="filterForm">
                <select name="role" onchange="this.form.submit()" class="w-full border-gray-300 rounded-md shadow-sm focus:border-blue-500 focus:ring-blue-500 text-sm">
                    <option value="">Tous les rôles</option>
                    <option value="administrateur" {{ request('role') == 'administrateur' ? 'selected' : '' }}>Administrateur</option>
                    <option value="vice_administrateur" {{ request('role') == 'vice_administrateur' ? 'selected' : '' }}>Vice-Administrateur</option>
                    <option value="vendeur" {{ request('role') == 'vendeur' ? 'selected' : '' }}>Vendeur</option>
                </select>
            </form>
        </div>
        <div>
            <a href="{{ route('user.index') }}" class="bg-gray-200 text-gray-700 px-4 py-2 rounded-md hover:bg-gray-300 flex items-center justify-center h-full transition text-sm">Réinitialiser</a>
        </div>
    </div>

    {{-- Tableau des utilisateurs avec scroll horizontal responsive --}}
    <div class="bg-white shadow rounded-lg overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200" id="usersTable">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Nom</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Code Connexion</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Téléphone</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Email</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Rôle</th>
                        <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($users as $user)
                        <tr class="user-row hover:bg-gray-50/50 transition">
                            <td class="px-4 py-4 whitespace-nowrap text-sm font-medium text-gray-900 user-name">{{ $user->name }}</td>
                            <td class="px-4 py-4 whitespace-nowrap text-sm font-mono font-bold text-blue-600 user-code">
                                {{ $user->code_connexion ?? 'N/A' }}
                            </td>
                            <td class="px-4 py-4 whitespace-nowrap text-sm text-gray-500 user-phone">{{ $user->phone }}</td>
                            <td class="px-4 py-4 whitespace-nowrap text-sm text-gray-500 user-email">{{ $user->email ?? 'Non renseigné' }}</td>
                            <td class="px-4 py-4 whitespace-nowrap text-sm text-gray-500">
                                <span class="px-2.5 py-1 inline-flex text-xs leading-5 font-semibold rounded-full 
                                    @if($user->role == 'administrateur') bg-purple-100 text-purple-800 
                                    @elseif($user->role == 'vice_administrateur') bg-amber-100 text-amber-800 
                                    @else bg-blue-100 text-blue-800 @endif">
                                    {{ ucfirst(str_replace('_', ' ', $user->role)) }}
                                </span>
                            </td>
                            <td class="px-4 py-4 whitespace-nowrap text-right text-sm font-medium">
                                @if($user->id !== Auth::id())
                                    <div class="flex items-center justify-end gap-2">
                                        {{-- Bouton Envoyer les identifiants par email --}}
                                        @if($user->email)
                                            <form action="{{ route('user.envoyer-identifiants-email', $user->id) }}" method="POST" class="inline-block" onsubmit="return confirm('Générer un nouveau mot de passe et l\'envoyer à {{ $user->email }} ?');">
                                                @csrf
                                                <button type="submit"
                                                        class="p-2 bg-emerald-50 text-emerald-600 hover:bg-emerald-600 hover:text-white rounded-lg transition-all duration-200 shadow-sm"
                                                        title="Envoyer les identifiants par email">
                                                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                                        <path stroke-linecap="round" stroke-linejoin="round" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                                                    </svg>
                                                </button>
                                            </form>
                                        @else
                                            <span class="p-2 bg-gray-50 text-gray-300 rounded-lg cursor-not-allowed" title="Aucun email renseigné pour cet utilisateur">
                                                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                                    <path stroke-linecap="round" stroke-linejoin="round" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                                                </svg>
                                            </span>
                                        @endif

                                        {{-- Bouton Modifier --}}
                                        <a href="{{ route('user.edit', $user->id) }}" 
                                           class="p-2 bg-indigo-50 text-indigo-600 hover:bg-indigo-600 hover:text-white rounded-lg transition-all duration-200 shadow-sm"
                                           title="Modifier l'utilisateur">
                                            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                            </svg>
                                        </a>

                                        {{-- Bouton Supprimer --}}
                                        <form action="{{ route('user.destroy', $user->id)}}" method="POST" class="inline-block" onsubmit="return confirm('Êtes-vous sûr de vouloir supprimer cet utilisateur ?');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" 
                                                    class="p-2 bg-red-50 text-red-600 hover:bg-red-600 hover:text-white rounded-lg transition-all duration-200 shadow-sm"
                                                    title="Supprimer l'utilisateur">
                                                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                                    <path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                                </svg>
                                            </button>
                                        </form>
                                    </div>
                                @else
                                    <span class="text-gray-400 italic text-xs">Votre compte</span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-4 py-4 text-center text-sm text-gray-500">Aucun utilisateur trouvé.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    {{-- Pagination --}}
    <div class="mt-4">
        {{ $users->links() }}
    </div>
</div>

{{-- Script pour la recherche instantanée (Live Search) --}}
<script>
    document.getElementById('liveSearch').addEventListener('input', function(e) {
        let term = e.target.value.toLowerCase();
        let rows = document.querySelectorAll('.user-row');

        rows.forEach(row => {
            let name = row.querySelector('.user-name').textContent.toLowerCase();
            let code = row.querySelector('.user-code').textContent.toLowerCase();
            let phone = row.querySelector('.user-phone').textContent.toLowerCase();
            let email = row.querySelector('.user-email').textContent.toLowerCase();

            if (name.includes(term) || code.includes(term) || phone.includes(term) || email.includes(term)) {
                row.style.display = '';
            } else {
                row.style.display = 'none';
            }
        });
    });
</script>
@endsection