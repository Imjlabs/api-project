<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class IsAdmin
{
    public function handle(Request $request, Closure $next)
    {
        // Votre logique pour vérifier si l'utilisateur est un administrateur
        // Par exemple, vous pouvez vérifier le rôle de l'utilisateur ici

        if (auth()->user() && auth()->user()->role === 'admin') {
            return $next($request);
        }

        return response()->json(['message' => 'Accès interdit'], 403);
    }
}
