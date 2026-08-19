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
            'phone' => 'required|string|max:9|unique:users,phone',
            'email' => 'nullable|string|email|unique:users,email',
            'nom_boutique' => 'required|string|max:255',
        ]);

        return DB::transaction(function () use ($request) {
            $boutique = Boutique::create([
                'nom' => $request->nom_boutique,
            ]);

            $user = User::create([
                'name' => $request->name,
                'email' => $request->email,
                'password' => Hash::make($request->password),
                'phone' => $request->phone,
                'role' => 'administrateur',
                'boutique_id' => $boutique->id,
                'doit_changer_mdp' => false,
            ]);

            Auth::login($user);

            return redirect()->route('dashboard')
                             ->with('success', 'Bienvenue ' . $user->name);
        });
    }

    public function showLogin()
    {
        return view('Auth.login');
    }

    public function login(Request $request)
    {
        $request->validate([
            'identifiant' => 'required|string',
            'password' => 'required|string',
        ]);

        $identifiant = $request->identifiant;
        $champ = filter_var($identifiant, FILTER_VALIDATE_EMAIL) ? 'email' : 'phone';

        $credentials = [
            $champ => $identifiant,
            'password' => $request->password,
        ];

        $remember = $request->boolean('remember');

        if (Auth::attempt($credentials, $remember)) {
            $request->session()->regenerate();

            return redirect()->route('dashboard')
                             ->with('success', 'Bonjour ' . Auth::user()->name);
        }

        return back()->withErrors([
            'identifiant' => 'Identifiant ou mot de passe incorrect',
        ])->onlyInput('identifiant');
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