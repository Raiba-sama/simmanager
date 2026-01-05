<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class RedirectNonAdminsFromAdmin
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Ne pas intercepter les routes de login, logout, ou autres routes publiques
        if ($request->is('login', 'logout', 'register', 'password/*', 'email/*')) {
            return $next($request);
        }

        // Si l'utilisateur est authentifié et n'est pas admin, rediriger vers le dashboard Breeze
        if (auth()->check() && !auth()->user()->isAdmin()) {
            // Si la requête est vers /admin, rediriger vers le dashboard
            if ($request->is('admin*')) {
                return redirect()->route('dashboard');
            }
        }

        return $next($request);
    }
}

