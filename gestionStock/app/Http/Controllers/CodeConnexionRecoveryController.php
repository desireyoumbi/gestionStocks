<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Services\BrevoMailer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\View;

class CodeConnexionRecoveryController extends Controller
{
    /**
     * Étape 1 : formulaire de saisie du numéro de téléphone.
     */
    public function showPhoneForm()
    {
        return view('Auth.forgot-code-connexion');
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
                'phone' => "Aucun email n'est associé à vos comptes. Rapprochez-vous de l'administrateur de votre boutique pour récupérer votre code de connexion.",
            ])->onlyInput('phone');
        }

        session(['comptes_ids_verifies_code' => $comptesAvecEmail->pluck('id')->toArray()]);

        if ($comptesAvecEmail->count() === 1) {
            return $this->confirmAccount($request, $comptesAvecEmail->first());
        }

        return view('Auth.select-account-code', ['comptes' => $comptesAvecEmail]);
    }

    /**
     * Étape 3 : confirmation du compte choisi + envoi du code de connexion par email
     * via l'API HTTP de Brevo. Aucune donnée du compte n'est modifiée ici.
     */
    public function confirmAccount(Request $request, User $user)
    {
        $comptesAutorises = session('comptes_ids_verifies_code', []);

        if (!in_array($user->id, $comptesAutorises) || !$user->email) {
            abort(403, "Sélection de compte invalide.");
        }

        $contenuHtml = View::make('emails.code-connexion', [
            'user' => $user,
        ])->render();

        BrevoMailer::envoyer(
            $user->email,
            $user->name,
            'Votre code de connexion - E-Stockly',
            $contenuHtml
        );

        session()->forget('comptes_ids_verifies_code');

        return redirect()->route('login')
            ->with('success', 'Votre code de connexion vous a été envoyé par email.');
    }
}