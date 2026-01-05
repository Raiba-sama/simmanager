<?php

namespace Database\Seeders;

use App\Models\Agency;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class AgencySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // TODO: Ajouter vos agences ici
        // Exemple de structure :
        $agencies = [
            // [
            //     'code' => 'AG001',
            //     'name' => 'Agence Antananarivo',
            //     'address' => 'Adresse de l\'agence',
            //     'phone' => '+261 XX XX XXX XX',
            //     'email' => 'agence@example.com',
            //     'zone_id' => 1, // ID de la zone (à ajuster)
            // ],
        ];

        foreach ($agencies as $agency) {
            Agency::updateOrCreate(
                ['code' => $agency['code']],
                $agency
            );
        }
    }
}
