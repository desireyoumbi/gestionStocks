<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use App\Models\Movement;

class ProfileController extends Controller
{
    public function edit()
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();

        // Calcul du temps de service (ancienneté)
        $ancienneté = $user->created_at->diffForHumans(null, true);

        // Statistiques selon le rôle (scopées proprement à la boutique ou à l'utilisateur)
        $stats = [];
        if ($user->role === 'vendeur') {
            $stats['total_movements'] = Movement::where('user_id', $user->id)
                ->where('boutique_id', $user->boutique_id)
                ->count();
        } else {
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
            'telephone' => ['nullable', 'string', 'max:20', 'regex:/^[0-9\+\-\s\(\)]+$/'],
            'current_password' => ['nullable', 'required_with:password', 'current_password'],
            'password' => ['nullable', 'string', 'min:8', 'confirmed'],
            'avatar' => ['nullable', 'image', 'mimes:jpeg,png,jpg,webp', 'max:2048'],
        ];

        $messages = [
            'telephone.regex' => 'Le format du numéro de téléphone est invalide.',
            'current_password.current_password' => 'Le mot de passe actuel est incorrect.',
            'password.confirmed' => 'La confirmation du nouveau mot de passe ne correspond pas.',
        ];

        $validated = $request->validate($rules, $messages);

        // L'email reste modifiable uniquement par l'administrateur
        if ($user->role === 'administrateur') {
            $rules['email'] = ['required', 'string', 'email', 'max:255', 'unique:users,email,' . $user->id];
        }

        $validated = $request->validate($rules);

        $user->name = $validated['name'];
        $user->telephone = $validated['telephone'] ?? null;

        // Mise à jour de l'email seulement si admin
        if ($user->role === 'administrateur' && isset($validated['email'])) {
            $user->email = $validated['email'];
        }

        // Changement de mot de passe
        if ($request->filled('password')) {
            $user->password = Hash::make($validated['password']);
        }

        // Gestion de l'avatar si présent dans ton formulaire
        if ($request->hasFile('avatar')) {
            if ($user->avatar && Storage::disk('public')->exists($user->avatar)) {
                Storage::disk('public')->delete($user->avatar);
            }
            $user->avatar = $request->file('avatar')->store('avatars', 'public');
        }

        $user->save();

        return back()->with('success', 'Profil mis à jour avec succès !');
    }
}
