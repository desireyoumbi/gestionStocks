<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class ForcedPasswordController extends Controller
{
    public function showForm()
    {
        return view('Auth.change-password');
    }

    public function update(Request $request)
    {
        $request->validate([
            'password' => ['required', 'string', 'min:8', 'confirmed'],
        ]);

        /** @var \App\Models\User $user */
        $user = Auth::user();

        // 🛑 Vérifier si le nouveau mot de passe est identique à l'actuel
        if (Hash::check($request->password, $user->password)) {
            throw ValidationException::withMessages([
                'password' => 'Le nouveau mot de passe doit être différent de celui fourni par l\'administrateur.',
            ]);
        }

        $user->update([
            'password' => Hash::make($request->password),
            'doit_changer_mdp' => false,  // Désactive l'obligation
        ]);

        if ($request->expectsJson()) {
            return response()->json([
                'success' => true,
                'redirect' => route('dashboard')
            ]);
        }

        return redirect()->intended(route('dashboard'));
    }
}