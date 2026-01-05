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
        // Nettoyer la session pour éviter les redirections indésirables
        session()->forget('url.intended');
        
        return view('auth.login');
    }

    /**
     * Handle an incoming authentication request.
     */
    public function store(LoginRequest $request): RedirectResponse
    {
        $request->authenticate();

        $request->session()->regenerate();

        // Nettoyer complètement toutes les données de session liées aux redirections
        $request->session()->forget('url.intended');
        $request->session()->forget('_previous');
        $request->session()->forget('_flash');
        
        // Rediriger selon le rôle de l'utilisateur
        $user = auth()->user();
        
        // Forcer la redirection vers /dashboard pour les non-admins
        if (!$user->isAdmin()) {
            // Utiliser redirect()->intended() avec fallback vers /dashboard
            // Cela évite les problèmes de redirection automatique
            return redirect()->intended('/dashboard');
        }
        
        // Admins → Filament
        return redirect()->intended('/admin');
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
