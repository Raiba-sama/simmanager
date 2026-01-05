<?php

namespace Database\Seeders;

use App\Models\Zone;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ZoneSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // TODO: Ajouter vos zones ici
        // Exemple de structure :
        $zones = [
            // ['code' => 'ZONE1', 'name' => 'Zone 1', 'description' => 'Description zone 1'],
            // ['code' => 'ZONE2', 'name' => 'Zone 2', 'description' => 'Description zone 2'],
        ];

        foreach ($zones as $zone) {
            Zone::updateOrCreate(
                ['code' => $zone['code']],
                $zone
            );
        }
    }
}
