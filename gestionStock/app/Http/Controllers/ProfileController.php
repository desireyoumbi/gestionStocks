<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Models\Movement;

class ProfileController extends Controller
{
    public function edit()
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();

        // Calcul du temps de service (ancienneté)
        $ancienneté = $user->created_at->diffForHumans(null, true);

        // Statistiques selon le rôle
        $stats = [];
        if ($user->role === 'vendeur') {
            $stats['total_movements'] = Movement::where('user_id', $user->id)->count();
        } else {
            // Pour l'admin/vice-admin, on peut afficher les mouvements de toute la boutique
            $stats['total_movements'] = Movement::where('boutique_id', $user->boutique_id)->count();
        }

        return view('profile.edit', compact('user', 'ancienneté', 'stats'));
    }

    public function update(Request $request)
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();

        $rules = [
            'name' => ['required', 'string', 'max:255'],
            'current_password' => ['nullable', 'required_with:password', 'current_password'],
            'password' => ['nullable', 'string', 'min:8', 'confirmed'],
        ];

        // Si c'est un administrateur, on peut l'autoriser à modifier son email si besoin, 
        // ou bloquer l'email pour tout le monde. Restreignons l'email uniquement à l'admin :
        if ($user->role === 'administrateur') {
            $rules['email'] = ['required', 'string', 'email', 'max:255', 'unique:users,email,' . $user->id];
        }

        $request->validate($rules);

        $user->name = $request->name;

        // Mise à jour de l'email seulement si admin
        if ($user->role === 'administrateur' && $request->filled('email')) {
            $user->email = $request->email;
        }

        // Changement de mot de passe
        if ($request->filled('password')) {
            $user->password = Hash::make($request->password);
        }

        $user->save();

        return back()->with('success', 'Profil mis à jour avec succès !');
    }
}