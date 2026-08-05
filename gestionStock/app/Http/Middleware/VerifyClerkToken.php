<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use App\Models\User;
use Firebase\JWT\JWT;
use Firebase\JWT\JWK;

class VerifyClerkToken
{
    public function handle(Request $request, Closure $next)
    {
        // 1. Récupérer le token depuis le header Authorization
        $token = $request->bearerToken();

        if (!$token) {
            return response()->json(['error' => 'Token manquant'], 401);
        }

        try {
            // 2. Récupérer les clés publiques de Clerk (mises en cache 1h)
            $jwks = Cache::remember('clerk_jwks', 3600, function () {
                return Http::get(config('services.clerk.jwks_url'))->json();
            });

            $keys = JWK::parseKeySet($jwks);

            // 3. Décoder et vérifier la signature du token
            $decoded = JWT::decode($token, $keys);

            // 4. Retrouver l'utilisateur local grâce à son clerk_id
            $user = User::where('clerk_id', $decoded->sub)->first();

            if (!$user) {
                return response()->json(['error' => 'Utilisateur introuvable'], 404);
            }

            // 5. Rendre l'utilisateur disponible dans la requête
            $request->attributes->set('current_user', $user);

        }  catch (\Exception $e) {
    return response()->json([
        'error' => 'Token invalide ou expiré',
        'debug_message' => $e->getMessage(),
        'debug_class' => get_class($e),
    ], 401);
}

        // 6. Tout est bon, on laisse passer vers le contrôleur
        return $next($request);
    }
}
