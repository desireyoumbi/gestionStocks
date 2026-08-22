<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
 use Barryvdh\DomPDF\Facade\Pdf;

class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        // 1. Récupérer uniquement les utilisateurs de la boutique de l'admin connecté
        $query = User::where('boutique_id', Auth::user()->boutique_id);

        // Sécurité supplémentaire : seuls les administrateurs peuvent modifier un utilisateur
        if (Auth::user()->role === 'vendeur') {
            abort(403, "Vous n'avez pas l'autorisation de modifier un utilisateur.");
        }

        // 2. Filtre optionnel par rôle (si l'admin trie par vendeur ou vice-administrateur)
        if ($request->filled('role')) {
            $query->where('role', $request->role);
        }

        // 3. Recherche générale (sur le nom, le téléphone ou l'email)
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'LIKE', "%{$search}%")
                    ->orWhere('phone', 'LIKE', "%{$search}%")
                    ->orWhere('email', 'LIKE', "%{$search}%");
            });
        }

        // 4. Exécuter la requête (avec une pagination)
        $users = $query->latest()->paginate(10);

        // 5. Renvoyer la vue en y injectant la liste des utilisateurs
        return view('user.index', compact('users'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        // Attention au nom du dossier de vue (user ou users selon ton arborescence)
        return view('user.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        // Sécurité : seuls les administrateurs peuvent créer un utilisateur
        if (Auth::user()->role !== 'administrateur') {
            abort(403, "Vous n'avez pas l'autorisation de créer un utilisateur.");
        }

        $boutiqueId = Auth::user()->boutique_id;

        // Vérification préalable : le téléphone ou l'email correspond-il à un compte
        // précédemment supprimé (soft delete) dans cette même boutique ?
        $deletedByPhone = User::onlyTrashed()
            ->where('boutique_id', $boutiqueId)
            ->where('phone', $request->phone)
            ->first();

        if ($deletedByPhone) {
            return back()->withErrors([
                'phone' => "Ce numéro appartient à un ancien compte supprimé ({$deletedByPhone->name}). "
                         . "Restaurez ce compte plutôt que d'en créer un nouveau, ou utilisez un autre numéro.",
            ])->withInput();
        }

        if ($request->filled('email')) {
            $deletedByEmail = User::onlyTrashed()
                ->where('boutique_id', $boutiqueId)
                ->where('email', $request->email)
                ->first();

            if ($deletedByEmail) {
                return back()->withErrors([
                    'email' => "Cet email appartient à un ancien compte supprimé ({$deletedByEmail->name}). "
                             . "Restaurez ce compte plutôt que d'en créer un nouveau, ou utilisez un autre email.",
                ])->withInput();
            }
        }

        // Validation classique : unicité parmi les comptes ACTIFS de cette boutique
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'phone' => [
                'required',
                'string',
                'max:9',
                Rule::unique('users', 'phone')->where(
                    fn ($query) => $query->where('boutique_id', $boutiqueId)
                ),
            ],
            'email' => [
                'nullable',
                'string',
                'email',
                'max:255',
                Rule::unique('users', 'email')->where(
                    fn ($query) => $query->where('boutique_id', $boutiqueId)
                ),
            ],
            'password' => 'required|string|min:8|confirmed',
            'role' => 'required|string|in:vice_administrateur,vendeur',
        ]);

        $validated['boutique_id'] = $boutiqueId;
        $validated['password'] = Hash::make($validated['password']);
        $validated['doit_changer_mdp'] = true;

        User::create($validated);

        return redirect()->route('user.index')
            ->with('succes', 'Utilisateur créé avec succès. Il devra changer son mot de passe à sa première connexion.');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(User $user)
    {
        // SÉCURITÉ MULTI-BOUTIQUE : Renvoie une 404 pour garder l'isolation totale des données
        if ($user->boutique_id !== Auth::user()->boutique_id) {
            abort(404, "Utilisateur introuvable.");
        }

        return view('user.edit', compact('user'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, User $user)
    {
        // SÉCURITÉ MULTI-BOUTIQUE : Renvoie une 404 pour garder l'isolation totale des données
        if ($user->boutique_id !== Auth::user()->boutique_id) {
            abort(404, "Utilisateur introuvable.");
        }

        // Sécurité supplémentaire : seuls les administrateurs peuvent modifier
        if (Auth::user()->role !== 'administrateur') {
            abort(403, "Vous n'avez pas l'autorisation de modifier un utilisateur.");
        }

        $validate = $request->validate([
            'name' => 'required|string|max:255',
            'phone' => 'required|string|max:9|unique:users,phone,' . $user->id,
            'email' => 'nullable|string|email|max:255|unique:users,email,' . $user->id,
            'password' => 'nullable|string|min:8|confirmed',
            'role' => 'required|string|in:administrateur,vice_administrateur,vendeur',
        ]);

        // Gestion conditionnelle du mot de passe
        if ($request->filled('password')) {
            $validate['password'] = Hash::make($request->password);
        } else {
            unset($validate['password']);
        }

        // Mise à jour de l'utilisateur
        $user->update($validate);

        return redirect()->route('user.index')
            ->with('succes', 'Utilisateur modifié avec succès.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(User $user)
    {
        // SÉCURITÉ MULTI-BOUTIQUE : Si l'utilisateur n'appartient pas à la boutique, on renvoie une 404
        if ($user->boutique_id !== Auth::user()->boutique_id) {
            abort(404, "Utilisateur introuvable.");
        }

        // Sécurité supplémentaire : seuls les administrateurs peuvent supprimer
        if (Auth::user()->role !== 'administrateur') {
            abort(403, "Vous n'avez pas l'autorisation de supprimer cet utilisateur.");
        }

        // Empêcher un admin de se supprimer lui-même par erreur
        if ($user->id === Auth::id()) {
            return back()->with('error', "Vous ne pouvez pas supprimer votre propre compte.");
        }

        // Suppression de l'utilisateur
        $user->delete();

        return redirect()->route('user.index')
            ->with('succes', 'Utilisateur supprimé avec succès.');
    }

    /**
     * Afficher la page récapitulative des rôles et des permissions de l'application.
     */
    public function rolesMatrix()
    {
        return view('user.roles');
    }



// ... à l'intérieur de UserController

public function exportPdf()
{
    // Récupérer les utilisateurs de la boutique
    $users = User::where('boutique_id', Auth::user()->boutique_id)->get();

    // Charger la vue PDF et passer les données
    $pdf = Pdf::loadView('user.pdf', compact('users'));

    // Télécharger le fichier
    return $pdf->download('liste-utilisateurs-' . date('Y-m-d') . '.pdf');
}
}