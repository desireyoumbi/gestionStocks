<?php

namespace App\Http\Controllers;

use App\Models\Boutique;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage; // <-- N'oublie pas d'importer Storage

class BoutiqueSettingsController extends Controller
{
    // Affiche les paramètres de la boutique
    public function index()
    {
        $user = Auth::user();
        if ($user->role !== 'administrateur') {
            return redirect()->route('dashboard')->with('error', 'Accès refusé.');
        }

        $boutique = Boutique::findOrFail($user->boutique_id);
        $staff = User::where('boutique_id', $boutique->id)->get();
        
        return view('settings.boutique', compact('boutique', 'staff'));
    }

    // Mise à jour des infos générales, du logo et de la couleur
    public function update(Request $request)
    {
        $request->validate([
            'nom' => 'required|string|max:255',
            'logo' => 'nullable|image|mimes:jpeg,png,jpg,svg|max:2048', // Validation de l'image
            'primary_color' => 'nullable|string|max:7', // Validation de la couleur (ex: #f59e0b)
        ]);

        /** @var \App\Models\Boutique $boutique */
        $boutique = Boutique::findOrFail(Auth::user()->boutique_id);
        
        // Données de base à mettre à jour
        $data = [
            'nom' => $request->nom,
            'primary_color' => $request->primary_color,
        ];

        // Gestion de l'upload du logo
        if ($request->hasFile('logo')) {
            // Supprimer l'ancien logo s'il existe
            if ($boutique->logo && Storage::disk('public')->exists($boutique->logo)) {
                Storage::disk('public')->delete($boutique->logo);
            }

            // Enregistrer le nouveau logo dans storage/app/public/logos
            $path = $request->file('logo')->store('logos', 'public');
            $data['logo'] = $path;
        }

        $boutique->update($data);

        return back()->with('success', 'Paramètres de la boutique mis à jour avec succès.');
    }

    // Suppression définitive de la boutique
    public function destroy(Request $request)
    {
        $user = Auth::user();
        $boutique = Boutique::findOrFail($user->boutique_id);
        
        if ($request->confirm_text !== 'SUPPRIMER_MA_BOUTIQUE') {
            return back()->with('error', 'Veuillez taper le texte de confirmation exact.');
        }

        $request->validate([
            'password' => ['required', 'current_password'],
        ]);

        User::where('boutique_id', $boutique->id)->delete();
        $boutique->delete();

        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('register')->with('success', 'La boutique et tous les comptes associés ont été supprimés définitivement.');
    }
}