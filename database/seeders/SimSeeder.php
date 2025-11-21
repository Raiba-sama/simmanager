<?php

namespace Database\Seeders;

use App\Models\Sim;
use Illuminate\Database\Seeder;

class SimSeeder extends Seeder
{
    public function run(): void
    {
        $operators = ['Orange', 'Moov', 'MTN'];
        $planTypes = ['Data', 'Voice', 'Data+Voice'];

        for ($i = 1; $i <= 20; $i++) {
            Sim::create([
                'iccid' => '890141032111185107' . str_pad($i, 2, '0', STR_PAD_LEFT),
                'phone_number' => '+226' . rand(70000000, 79999999),
                'status' => $i <= 15 ? 'libre' : 'attribue',
                'operator' => $operators[array_rand($operators)],
                'plan_type' => $planTypes[array_rand($planTypes)],
                'monthly_cost' => rand(5000, 15000),
            ]);
        }
    }
}

