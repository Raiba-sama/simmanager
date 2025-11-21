<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        // Admin
        User::create([
            'matricule' => 'ADMIN001',
            'name' => 'Admin',
            'first_name' => 'Système',
            'fonction' => 'Administrateur',
            'email' => 'admin@simmanager.local',
            'password' => Hash::make('password'),
            'role' => 'admin',
            'active' => true,
        ]);

        // Validator
        User::create([
            'matricule' => 'VAL001',
            'name' => 'Validateur',
            'first_name' => 'Test',
            'fonction' => 'Validateur',
            'email' => 'validator@simmanager.local',
            'password' => Hash::make('password'),
            'role' => 'validator',
            'active' => true,
        ]);

        // User
        User::create([
            'matricule' => 'USER001',
            'name' => 'Utilisateur',
            'first_name' => 'Test',
            'fonction' => 'Employé',
            'email' => 'user@simmanager.local',
            'password' => Hash::make('password'),
            'role' => 'user',
            'active' => true,
        ]);
    }
}

