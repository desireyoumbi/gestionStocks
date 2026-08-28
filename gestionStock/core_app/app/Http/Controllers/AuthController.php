<?php

namespace App\Http\Controllers;

use App\Models\Boutique;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    public function showRegister()
    {
        return view('Auth.register');
    }

    public function register(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'password' => 'required|string|min:8|confirmed',
            'phone' => 'required|string|max:60|',
            'email' => 'nullable|string|email|',
            'nom_boutique' => 'required|string|max:255',
        ]);

        return DB::transaction(function () use ($request) {
            $boutique = Boutique::create([
                'nom' => $request->nom_boutique,
            ]);

            $prefix = strtoupper(\Illuminate\Support\Str::slug($request->nom_boutique));
            $prefix = substr($prefix, 0, 4);

            do {
                $codeConnexion = $prefix . '-' . rand(1000, 9999);
            } while (\App\Models\User::where('code_connexion', $codeConnexion)->exists());

            $user = User::create([
                'name' => $request->name,
                'email' => $request->email,
                'password' => Hash::make($request->password),
                'phone' => $request->phone,
                'role' => 'administrateur',
                'boutique_id' => $boutique->id,
                'code_connexion' => $codeConnexion,
                'doit_changer_mdp' => false,
            ]);

            Auth::login($user);

            // On stocke le code dans une session flash dédiée pour la popup
            return redirect()->route('dashboard')
                             ->with('new_user_code', $codeConnexion);
        });
    }
    public function showLogin()
    {
        return view('Auth.login');
    }

   public function login(Request $request)
    {
        $request->validate([
            'code_connexion' => 'required|string',
            'password' => 'required|string',
        ]);

        $credentials = [
            'code_connexion' => $request->code_connexion,
            'password' => $request->password,
        ];

        $remember = $request->boolean('remember');

        if (Auth::attempt($credentials, $remember)) {
            $request->session()->regenerate();

            return redirect()->route('dashboard')
                             ->with('success', 'Bonjour ' . Auth::user()->name);
        }

        return back()->withErrors([
            'code_connexion' => 'Code de connexion ou mot de passe incorrect',
        ])->onlyInput('code_connexion');
    }

    public function logout(Request $request)
    {
        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login')
                         ->with('success', 'À bientôt');
    }
}