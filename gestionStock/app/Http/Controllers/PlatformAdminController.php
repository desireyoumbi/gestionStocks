<?php

namespace App\Http\Controllers;

use App\Models\Boutique;
use App\Models\User;
use App\Services\BrevoMailer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Str;

class PlatformAdminController extends Controller
{
    public function dashboard()
    {
        $boutiques = Boutique::withCount('users')->orderByDesc('created_at')->get();

        return view('platform-admin.dashboard', [
            'boutiques' => $boutiques,
            'totalBoutiques' => $boutiques->count(),
            'totalActives' => $boutiques->where('statut', 'active')->count(),
            'totalSuspendues' => $boutiques->where('statut', 'suspendue')->count(),
        ]);
    }

    public function suspendre(Boutique $boutique)
    {
        $boutique->update(['statut' => 'suspendue']);

        return back()->with('success', "Boutique « {$boutique->nom} » suspendue.");
    }

    public function reactiver(Boutique $boutique)
    {
        $boutique->update(['statut' => 'active']);

        return back()->with('success', "Boutique « {$boutique->nom} » réactivée.");
    }

    /**
     * Affiche le détail d'une boutique : ses utilisateurs et leurs rôles.
     */
    public function show(Boutique $boutique)
    {
        $users = User::where('boutique_id', $boutique->id)->orderByDesc('role')->get();

        return view('platform-admin.boutique-show', [
            'boutique' => $boutique,
            'users' => $users,
        ]);
    }

    /**
     * Réinitialise le mot de passe d'un utilisateur, quel que soit son rôle,
     * y compris un administrateur qui n'a personne au-dessus de lui dans sa boutique.
     */
    public function reinitialiserMotDePasse(User $user)
    {
        if (!$user->email) {
            return back()->with('error', "Impossible d'envoyer un nouveau mot de passe : aucun email n'est renseigné pour {$user->name}.");
        }

        $nouveauMotDePasse = Str::random(10);
        $user->password = $nouveauMotDePasse;
        $user->doit_changer_mdp = true;
        $user->save();

        $contenuHtml = View::make('emails.password-reset', [
            'user' => $user,
            'nouveauMotDePasse' => $nouveauMotDePasse,
        ])->render();

        $envoiReussi = BrevoMailer::envoyer(
            $user->email,
            $user->name,
            'Réinitialisation de votre mot de passe - E-Stockly',
            $contenuHtml
        );

        if (!$envoiReussi) {
            return back()->with('error', "L'envoi de l'email a échoué pour {$user->name}.");
        }

        return back()->with('success', "Nouveau mot de passe envoyé à {$user->name} ({$user->email}).");
    }
}