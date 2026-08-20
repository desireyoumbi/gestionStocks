<?php

namespace App\Http\Controllers;

use App\Models\Boutique;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class BoutiqueSettingsController extends Controller
{
    // Affiche les paramètres de la boutique
    public function index()
    {
        $user = Auth::user();
        // Vérification de sécurité : seul l'admin peut voir cette page
        if ($user->role !== 'administrateur') {
            return redirect()->route('dashboard')->with('error', 'Accès refusé.');
        }

        $boutique = Boutique::findOrFail($user->boutique_id);
        $staff = User::where('boutique_id', $boutique->id)->get();
        
        return view('settings.boutique', compact('boutique', 'staff'));
    }

    // Mise à jour des infos générales
    public function update(Request $request)
    {
        $request->validate([
            'nom' => 'required|string|max:255',
        ]);

        /** @var \App\Models\Boutique $boutique */
        $boutique = Boutique::findOrFail(Auth::user()->boutique_id);
        
        $boutique->update([
            'nom' => $request->nom,
        ]);

        return back()->with('success', 'Nom de la boutique mis à jour avec succès.');
    }

    // Suppression définitive de la boutique
    // Suppression définitive de la boutique
    public function destroy(Request $request)
    {
        $user = Auth::user();
        $boutique = Boutique::findOrFail($user->boutique_id);
        
        // 1. Vérification du texte de confirmation
        if ($request->confirm_text !== 'SUPPRIMER_MA_BOUTIQUE') {
            return back()->with('error', 'Veuillez taper le texte de confirmation exact.');
        }

        // 2. Vérification de sécurité supplémentaire (Mot de passe de l'admin)
        $request->validate([
            'password' => ['required', 'current_password'],
        ]);

        // 3. Supprimer TOUS les utilisateurs liés à cette boutique (staff + admin)
        // pour qu'ils ne puissent plus jamais se connecter avec ce compte.
        User::where('boutique_id', $boutique->id)->delete();

        // 4. Suppression définitive de la boutique
        $boutique->delete();

        // 5. Déconnexion complète, nettoyage de session et redirection vers l'inscription
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('register')->with('success', 'La boutique et tous les comptes associés ont été supprimés définitivement.');
    }
}