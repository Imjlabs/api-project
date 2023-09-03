<?php

namespace App\Http\Middleware;
use Closure;
use Illuminate\Http\Request;

class IsAdmin
{
    public function handle($request, Closure $next)
    {
        // Vérification si l'utilisateur est authentifié
        if (auth()->check() && auth()->user()->role === 'admin') {
            return $next($request);
        }

        return response()->json(['message' => 'Accès interdit - Vous n\'avez pas les autorisations requises'], 403);
}
}