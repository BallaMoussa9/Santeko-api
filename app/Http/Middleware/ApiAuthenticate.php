<?php

namespace App\Http\Middleware;

use Illuminate\Auth\Middleware\Authenticate as Middleware;

class ApiAuthenticate extends Middleware
{
    protected function redirectTo($request)
    {
        // 🚫 Si c’est une requête API, on ne redirige pas
        if ($request->expectsJson()) {
            abort(response()->json(['message' => 'Non authentifié.'], 401));
        }

        // Sinon, comportement classique (pour les routes web)
        return route('login');
    }
}
