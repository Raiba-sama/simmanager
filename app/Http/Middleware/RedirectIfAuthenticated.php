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
                
                $user = Auth::guard($guard)->user();
                // Rediriger les admins vers Filament, les autres vers le dashboard Breeze
                if ($user && $user->isAdmin()) {
                    return redirect()->route('filament.admin.pages.dashboard');
                }
                return redirect(RouteServiceProvider::HOME);
            }
        }

        return $next($request);
    }
}
