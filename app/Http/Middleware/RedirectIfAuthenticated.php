<?php

namespace App\Http\Middleware;

use App\Providers\RouteServiceProvider;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class RedirectIfAuthenticated
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next, string ...$guards): Response
    {
        $guards = empty($guards) ? [null] : $guards;

        foreach ($guards as $guard) {
            if (Auth::guard($guard)->check()) {
                // Ne pas rediriger si on est déjà sur une route publique ou de login
                if ($request->is('login', 'logout', 'register', 'password/*', 'email/*', 'verify-email/*')) {
                    return $next($request);
                }
                
                // Rediriger tous les utilisateurs authentifiés vers le dashboard
                // Les admins peuvent ensuite accéder à /admin s'ils le souhaitent
                return redirect(RouteServiceProvider::HOME);
            }
        }

        return $next($request);
    }
}
