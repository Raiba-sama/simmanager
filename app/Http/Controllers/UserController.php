<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('role:admin')->except(['profile', 'updateProfile']);
    }

    public function profile()
    {
        return view('users.profile', ['user' => auth()->user()]);
    }

    public function updateProfile(Request $request)
    {
        $user = auth()->user();
        
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'first_name' => 'nullable|string|max:255',
            'fonction' => 'nullable|string|max:255',
            'lieu_affectation' => 'nullable|string|max:255',
            'zone_affectation' => 'nullable|string|max:255',
            'direction' => 'nullable|string|max:255',
            'numero_flotte' => 'nullable|string|max:255',
            'email' => 'required|email|unique:users,email,' . $user->id,
            'avatar_url' => 'nullable|url|max:500',
        ]);

        $user->update($validated);
        logModelAction($user, 'update_profile');

        return back()->with('success', 'Profil mis à jour avec succès.');
    }
}

