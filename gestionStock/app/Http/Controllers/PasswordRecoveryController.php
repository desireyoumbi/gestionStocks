<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Mail\PasswordResetMail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;

class PasswordRecoveryController extends Controller
{
    /**
     * Étape 1 : formulaire de saisie du numéro de téléphone.
     */
    public function showPhoneForm()
    {
        return view('auth.forgot-password');
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

        // On ne garde que les comptes ayant un email renseigné :
        // ce sont les seuls pour lesquels une récupération automatique est possible.
        $comptesAvecEmail = $comptes->whereNotNull('email')->values();

        if ($comptesAvecEmail->isEmpty()) {
            return back()->withErrors([
                'phone' => "Aucun email n'est associé à vos comptes. Rapprochez-vous de l'administrateur de votre boutique pour réinitialiser votre mot de passe.",
            ])->onlyInput('phone');
        }

        // On mémorise en session UNIQUEMENT les comptes déjà validés par ce numéro de téléphone.
        // Cela empêche de réinitialiser un compte en manipulant l'ID dans l'URL à l'étape suivante.
        session(['comptes_ids_verifies' => $comptesAvecEmail->pluck('id')->toArray()]);

        // S'il n'y a qu'un seul compte, on saute directement à la confirmation.
        if ($comptesAvecEmail->count() === 1) {
            return $this->confirmAccount($request, $comptesAvecEmail->first());
        }

        return view('auth.select-account', ['comptes' => $comptesAvecEmail]);
    }

    /**
     * Étape 3 : confirmation du compte choisi + envoi du nouveau mot de passe par email.
     */
    public function confirmAccount(Request $request, User $user)
    {
        $comptesAutorises = session('comptes_ids_verifies', []);

        // Sécurité : le compte ciblé doit obligatoirement faire partie de ceux
        // déjà validés par le numéro de téléphone à l'étape précédente.
        if (!in_array($user->id, $comptesAutorises) || !$user->email) {
            abort(403, "Sélection de compte invalide.");
        }

        $nouveauMotDePasse = Str::random(10);

        $user->password = $nouveauMotDePasse; // le cast 'hashed' du modèle s'occupe du hachage
        $user->doit_changer_mdp = true;
        $user->save();

        Mail::to($user->email)->send(new PasswordResetMail($user, $nouveauMotDePasse));

        session()->forget('comptes_ids_verifies');

        return redirect()->route('login')
            ->with('success', 'Un nouveau mot de passe temporaire a été envoyé à votre adresse email.');
    }
}