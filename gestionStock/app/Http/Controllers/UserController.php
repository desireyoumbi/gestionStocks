<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Services\BrevoMailer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

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
            abort(403, "Vous n'avez pas l'autorisation de voir cette page.");
        }

        // 2. Filtre optionnel par rôle (si l'admin trie par vendeur ou vice-administrateur)
        if ($request->filled('role')) {
            $query->where('role', $request->role);
        }

        // 3. Recherche générale (sur le nom, le téléphone, l'email ou le code de connexion)
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'LIKE', "%{$search}%")
                    ->orWhere('phone', 'LIKE', "%{$search}%")
                    ->orWhere('email', 'LIKE', "%{$search}%")
                    ->orWhere('code_connexion', 'LIKE', "%{$search}%");
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
        // Sécurité : seuls les administrateurs peuvent créer un utilisateur
        if (Auth::user()->role !== 'administrateur') {
            abort(403, "Vous n'avez pas l'autorisation d'ajouter un utilisateur.");
        }

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

        // Validation avec unicité conditionnée par boutique_id
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'phone' => [
                'required',
                'string',
                'max:15',
                Rule::unique('users', 'phone')->where('boutique_id', $boutiqueId),
            ],
            'email' => [
                'nullable',
                'string',
                'email',
                'max:255',
                Rule::unique('users', 'email')->where('boutique_id', $boutiqueId),
            ],
            'password' => 'required|string|min:8|confirmed',
            'role' => 'required|string|in:vice_administrateur,vendeur',
        ], [
            'phone.unique' => 'Ce numéro de téléphone est déjà utilisé par un autre utilisateur dans cette boutique.',
            'email.unique' => 'Cette adresse email est déjà utilisée par un autre utilisateur dans cette boutique.',
        ]);

        $validated['boutique_id'] = $boutiqueId;
        $validated['password'] = $request->password;
        $validated['doit_changer_mdp'] = true;

        // Génération d'un code de connexion explicite et unique (ex: QUIN-4812)
        $prefix = strtoupper(Str::slug(Auth::user()->boutique->nom ?? 'STCK'));
        $prefix = substr($prefix, 0, 4);

        do {
            $codeConnexion = $prefix . '-' . rand(1000, 9999);
        } while (User::where('code_connexion', $codeConnexion)->exists());

        $validated['code_connexion'] = $codeConnexion;

       $nouvelUtilisateur = User::create($validated);

        return redirect()->route('user.index')
    ->with('succes', "Utilisateur créé avec succès. Son code de connexion unique est : {$codeConnexion}")
    ->with('new_user_code', $codeConnexion)
    ->with('new_user_id', $nouvelUtilisateur->id);

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
            'phone' => 'required|string|max:15',
            'email' => 'nullable|string|email|max:255',
            'password' => 'nullable|string|min:8|confirmed',
            'role' => 'required|string|in:administrateur,vice_administrateur,vendeur',
        ]);

        // Gestion conditionnelle du mot de passe
        if ($request->filled('password')) {
            $validate['password'] = $request->password;
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

    /**
     * Exporter la liste des utilisateurs en PDF.
     */
    public function exportPdf()
    {
        // Récupérer les utilisateurs de la boutique
        $users = User::where('boutique_id', Auth::user()->boutique_id)->get();

        // Charger la vue PDF et passer les données
        $pdf = Pdf::loadView('user.pdf', compact('users'));

        // Télécharger le fichier
        return $pdf->download('liste-utilisateurs-' . date('Y-m-d') . '.pdf');
    }

   
public function envoyerIdentifiantsEmail(User $user)
{
    // Sécurité : seul un administrateur de la même boutique peut déclencher cet envoi
    if ($user->boutique_id !== Auth::user()->boutique_id) {
        abort(404, "Utilisateur introuvable.");
    }
 
    if (Auth::user()->role !== 'administrateur') {
        abort(403, "Vous n'avez pas l'autorisation d'effectuer cette action.");
    }
 
    if (!$user->email) {
        return back()->with('error', "Impossible d'envoyer les identifiants : aucun email n'est renseigné pour cet utilisateur.");
    }
 
    $nouveauMotDePasse = \Illuminate\Support\Str::random(10);
    $user->password = $nouveauMotDePasse; // le cast 'hashed' du modèle s'occupe du hachage
    $user->doit_changer_mdp = true;
    $user->save();
 
    $contenuHtml = View::make('emails.password-reset', [
        'user' => $user,
        'nouveauMotDePasse' => $nouveauMotDePasse,
    ])->render();
 
    $envoiReussi = BrevoMailer::envoyer(
        $user->email,
        $user->name,
        'Vos identifiants de connexion - E-Stockly',
        $contenuHtml
    );
 
    if (!$envoiReussi) {
        return back()->with('error', "L\'envoi de l\'email a échoué. Réessayez plus tard ou communiquez les identifiants manuellement.");
    }
 
    return back()->with('success', "Les identifiants ont été envoyés par email à {$user->email}.");

}
}