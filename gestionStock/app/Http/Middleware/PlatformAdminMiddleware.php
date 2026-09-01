<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PlatformAdminMiddleware
{
    public function handle(Request $request, Closure $next)
    {
        if (!Auth::guard('platform_admin')->check()) {
            abort(403, "Accès réservé à l'administration de la plateforme.");
        }

        return $next($request);
    }
}