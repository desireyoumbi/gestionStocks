<?php

namespace App\Http\Controllers;

use App\Models\Boutique;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Str;

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
            'phone' => 'required|string|max:15',
            'email' => 'required|string|email|max:255',
            'nom_boutique' => 'required|string|max:255',
        ]);

        return DB::transaction(function () use ($request) {
            $boutique = Boutique::create([
                'nom' => $request->nom_boutique,
            ]);

            $prefix = strtoupper(Str::slug($request->nom_boutique));
            $prefix = substr($prefix, 0, 4);

            do {
                $codeConnexion = $prefix . '-' . rand(1000, 9999);
            } while (User::where('code_connexion', $codeConnexion)->exists());

            $user = User::create([
                'name' => $request->name,
                'email' => $request->email,
                'password' => $request->password,
                'phone' => $request->phone,
                'role' => 'administrateur',
                'boutique_id' => $boutique->id,
                'code_connexion' => $codeConnexion,
                'doit_changer_mdp' => false,
            ]);

            Auth::login($user);

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

        // Normalisation du code de connexion
        $codeConnexion = strtoupper(trim($request->code_connexion));

        /*
        |--------------------------------------------------------------------------
        | Rate limiting
        |--------------------------------------------------------------------------
        */

        $ip = $request->ip();

        // Clé spécifique au compte + adresse IP
        $accountKey = 'login-account:' . Str::lower($codeConnexion) . '|' . $ip;

        // Clé globale pour l'adresse IP
        $ipKey = 'login-ip:' . $ip;

        /*
        |--------------------------------------------------------------------------
        | Vérification de la limite compte + IP
        |--------------------------------------------------------------------------
        */

        if (RateLimiter::tooManyAttempts($accountKey, 5)) {
            $seconds = RateLimiter::availableIn($accountKey);

            return back()
                ->withErrors([
                    'code_connexion' => "Trop de tentatives. Réessayez dans {$seconds} secondes.",
                ])
                ->onlyInput('code_connexion');
        }

        /*
        |--------------------------------------------------------------------------
        | Vérification de la limite globale par IP
        |--------------------------------------------------------------------------
        */

        if (RateLimiter::tooManyAttempts($ipKey, 30)) {
            $seconds = RateLimiter::availableIn($ipKey);

            return back()
                ->withErrors([
                    'code_connexion' => "Trop de tentatives de connexion depuis cette adresse. Réessayez dans {$seconds} secondes.",
                ])
                ->onlyInput('code_connexion');
        }

        $credentials = [
            'code_connexion' => $codeConnexion,
            'password' => $request->password,
        ];

        $remember = $request->boolean('remember');

        /*
        |--------------------------------------------------------------------------
        | 1. Connexion Platform Admin
        |--------------------------------------------------------------------------
        */

        if (Auth::guard('platform_admin')->attempt($credentials, $remember)) {

            // Connexion réussie : remise à zéro des compteurs
            RateLimiter::clear($accountKey);
            RateLimiter::clear($ipKey);

            $request->session()->regenerate();

            return redirect()->route('platform-admin.dashboard')
                ->with(
                    'success',
                    'Bienvenue, ' . Auth::guard('platform_admin')->user()->name
                );
        }

        /*
        |--------------------------------------------------------------------------
        | 2. Connexion utilisateur classique
        |--------------------------------------------------------------------------
        */

        if (Auth::attempt($credentials, $remember)) {

            $user = Auth::user();

            // Connexion réussie : remise à zéro des compteurs
            RateLimiter::clear($accountKey);
            RateLimiter::clear($ipKey);

            $request->session()->regenerate();

            /*
            |--------------------------------------------------------------------------
            | Vérification de la boutique suspendue
            |--------------------------------------------------------------------------
            */

            if ($user->boutique && $user->boutique->statut === 'suspendue') {

                Auth::logout();

                $request->session()->invalidate();
                $request->session()->regenerateToken();

                return back()->withErrors([
                    'code_connexion' =>
                        'Ce compte est actuellement suspendu. Contactez le support : 678177749 (WhatsApp) ou youmbidesire413@gmail.com.',
                ])->onlyInput('code_connexion');
            }

            return redirect()->route('dashboard')
                ->with('success', 'Bonjour ' . $user->name);
        }

        /*
        |--------------------------------------------------------------------------
        | Échec de connexion
        |--------------------------------------------------------------------------
        */

        // On incrémente les deux compteurs uniquement lorsque
        // les identifiants sont incorrects.
        RateLimiter::hit($accountKey, 60);
        RateLimiter::hit($ipKey, 300);

        return back()
            ->withErrors([
                'code_connexion' => 'Code de connexion ou mot de passe incorrect',
            ])
            ->onlyInput('code_connexion');
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