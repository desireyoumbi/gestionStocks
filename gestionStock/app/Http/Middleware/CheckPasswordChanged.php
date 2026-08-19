<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class CheckPasswordChanged
{
    public function handle(Request $request, Closure $next): Response
    {
        // Si l'utilisateur est connecté et que son champ doit_changer_mdp est vrai
        if (Auth::check() && Auth::user()->doit_changer_mdp) {
            
            // On l'autorise UNIQUEMENT à accéder aux routes de changement de mot de passe 
            // et à la route de déconnexion (pour éviter qu'il soit bloqué en boucle sans pouvoir sortir)
            if (!$request->routeIs('password.change') && 
                !$request->routeIs('password.update.forced') && 
                !$request->routeIs('logout')) {
                
                return redirect()->route('password.change');
            }
        }

        return $next($request);
    }
}