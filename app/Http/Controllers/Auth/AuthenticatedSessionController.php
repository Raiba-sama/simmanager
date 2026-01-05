<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use App\Providers\RouteServiceProvider;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class AuthenticatedSessionController extends Controller
{
    /**
     * Display the login view.
     */
    public function create(): View
    {
        return view('auth.login');
    }

    /**
     * Handle an incoming authentication request.
     */
    public function store(LoginRequest $request): RedirectResponse
    {
        $request->authenticate();

        $request->session()->regenerate();

        // Nettoyer l'URL intended de la session pour éviter les redirections vers /admin
        $intended = $request->session()->pull('url.intended');
        
        // Rediriger les admins vers Filament, les autres vers le dashboard Breeze
        $user = auth()->user();
        if ($user->isAdmin()) {
            // Si l'URL intended était vers /admin, on la garde, sinon on redirige vers le dashboard Filament
            if ($intended && str_contains($intended, '/admin')) {
                return redirect($intended);
            }
            return redirect()->route('filament.admin.pages.dashboard');
        }
        
        // Pour les non-admins, toujours rediriger vers le dashboard Breeze
        // Même si l'URL intended était /admin, on l'ignore
        return redirect()->route('dashboard');
    }

    /**
     * Destroy an authenticated session.
     */
    public function destroy(Request $request): RedirectResponse
    {
        Auth::guard('web')->logout();

        $request->session()->invalidate();

        $request->session()->regenerateToken();

        return redirect()->route('login');
    }
}
