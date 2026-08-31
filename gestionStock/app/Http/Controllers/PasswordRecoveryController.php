<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Services\BrevoMailer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Str;

class PasswordRecoveryController extends Controller
{
    /**
     * Étape 1 : formulaire de saisie du numéro de téléphone.
     */
    public function showPhoneForm()
    {
        return view('Auth.forgot-password');
    }

    /**
     * Étape 2 : recherche des comptes liés à ce numéro de téléphone.
     */
    public function findAccounts(Request $request)
    {
        $request->validate([
            'phone' => 'required|string|max:15',
        ]);

        $comptes = User::where('phone', $request->phone)->with('boutique')->get();

        if ($comptes->isEmpty()) {
            return back()->withErrors([
                'phone' => 'Aucun compte trouvé avec ce numéro de téléphone.',
            ])->onlyInput('phone');
        }

        $comptesAvecEmail = $comptes->whereNotNull('email')->values();

        if ($comptesAvecEmail->isEmpty()) {
            return back()->withErrors([
                'phone' => "Aucun email n'est associé à vos comptes. Rapprochez-vous de l'administrateur de votre boutique pour réinitialiser votre mot de passe.",
            ])->onlyInput('phone');
        }

        session(['comptes_ids_verifies' => $comptesAvecEmail->pluck('id')->toArray()]);

        if ($comptesAvecEmail->count() === 1) {
            return $this->confirmAccount($request, $comptesAvecEmail->first());
        }

        return view('Auth.select-account', ['comptes' => $comptesAvecEmail]);
    }

    /**
     * Étape 3 : confirmation du compte choisi + envoi du nouveau mot de passe par email
     * via l'API HTTP de Brevo (contourne le blocage SMTP de Render).
     */
    public function confirmAccount(Request $request, User $user)
    {
        $comptesAutorises = session('comptes_ids_verifies', []);

        if (!in_array($user->id, $comptesAutorises) || !$user->email) {
            abort(403, "Sélection de compte invalide.");
        }

        $nouveauMotDePasse = Str::random(10);

        $user->password = $nouveauMotDePasse; // le cast 'hashed' du modèle s'occupe du hachage
        $user->doit_changer_mdp = true;
        $user->save();

        $contenuHtml = View::make('emails.password-reset', [
            'user' => $user,
            'nouveauMotDePasse' => $nouveauMotDePasse,
        ])->render();

        BrevoMailer::envoyer(
            $user->email,
            $user->name,
            'Réinitialisation de votre mot de passe - E-Stockly',
            $contenuHtml
        );

        session()->forget('comptes_ids_verifies');

        return redirect()->route('login')
            ->with('success', 'Un nouveau mot de passe temporaire a été envoyé à votre adresse email.');
    }
}