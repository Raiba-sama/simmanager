<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class PreventAdminRedirect
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Ne pas intercepter les routes de login, logout, ou autres routes publiques
        if ($request->is('login', 'logout', 'register', 'password/*', 'email/*', 'verify-email/*')) {
            return $next($request);
        }
        
        // Si l'utilisateur est authentifié et n'est pas admin, et qu'il essaie d'accéder à /admin
        if (auth()->check() && !auth()->user()->isAdmin()) {
            // Si la requête est vers /admin ou ses sous-routes, rediriger vers /dashboard
            if ($request->is('admin*') && !$request->is('admin/login')) {
                return redirect()->to(url('/dashboard'));
            }
        }

        return $next($request);
    }
}

