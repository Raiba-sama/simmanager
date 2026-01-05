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

        // Nettoyer complètement l'URL intended de la session
        $request->session()->forget('url.intended');
        
        // Rediriger selon le rôle de l'utilisateur
        $user = auth()->user();
        
        if ($user->isAdmin()) {
            // Admins → Filament
            return redirect()->route('filament.admin.pages.dashboard');
        }
        
        // Non-admins → Dashboard Breeze
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
