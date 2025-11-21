<?php

namespace Database\Seeders;

use App\Models\Plan;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class PlanSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $plans = [
            ['monthly_cost' => 10800.00, 'limite_credit' => 10000.00, 'limite_data' => 0, 'name' => 'Forfait 10K - 0 Go'],
            ['monthly_cost' => 10800.00, 'limite_credit' => 25000.00, 'limite_data' => 0, 'name' => 'Forfait 25K - 0 Go'],
            ['monthly_cost' => 10800.00, 'limite_credit' => 30000.00, 'limite_data' => 0, 'name' => 'Forfait 30K - 0 Go'],
            ['monthly_cost' => 10800.00, 'limite_credit' => 30000.00, 'limite_data' => 15, 'name' => 'Forfait 30K - 15 Go'],
            ['monthly_cost' => 10800.00, 'limite_credit' => 30000.00, 'limite_data' => 4.5, 'name' => 'Forfait 30K - 4.5 Go'],
            ['monthly_cost' => 10800.00, 'limite_credit' => 40000.00, 'limite_data' => 4.5, 'name' => 'Forfait 40K - 4.5 Go'],
            ['monthly_cost' => 10800.00, 'limite_credit' => 40000.00, 'limite_data' => 2.5, 'name' => 'Forfait 40K - 2.5 Go'],
            ['monthly_cost' => 10800.00, 'limite_credit' => 40000.00, 'limite_data' => 0, 'name' => 'Forfait 40K - 0 Go'],
            ['monthly_cost' => 10800.00, 'limite_credit' => 45000.00, 'limite_data' => 2.5, 'name' => 'Forfait 45K - 2.5 Go'],
            ['monthly_cost' => 10800.00, 'limite_credit' => 50000.00, 'limite_data' => 4.5, 'name' => 'Forfait 50K - 4.5 Go'],
            ['monthly_cost' => 10800.00, 'limite_credit' => 50000.00, 'limite_data' => 0, 'name' => 'Forfait 50K - 0 Go'],
            ['monthly_cost' => 10800.00, 'limite_credit' => 70000.00, 'limite_data' => 0, 'name' => 'Forfait 70K - 0 Go'],
            ['monthly_cost' => 10800.00, 'limite_credit' => 100000.00, 'limite_data' => 15, 'name' => 'Forfait 100K - 15 Go'],
            ['monthly_cost' => 10800.00, 'limite_credit' => 100000.00, 'limite_data' => 4.5, 'name' => 'Forfait 100K - 4.5 Go'],
            ['monthly_cost' => 10800.00, 'limite_credit' => 149999.00, 'limite_data' => 15, 'name' => 'Forfait 149.999K - 15 Go'],
            ['monthly_cost' => 10800.00, 'limite_credit' => 150000.00, 'limite_data' => 50, 'name' => 'Forfait 150K - 50 Go'],
            ['monthly_cost' => 0, 'limite_credit' => 0, 'limite_data' => 4.5, 'name' => 'Forfait Data Only - 4.5 Go'],
        ];

        foreach ($plans as $planData) {
            Plan::updateOrCreate(
                [
                    'name' => $planData['name'],
                ],
                [
                    'monthly_cost' => $planData['monthly_cost'],
                    'limite_credit' => $planData['limite_credit'],
                    'limite_data' => $planData['limite_data'],
                    'description' => "Forfait avec limite crédit de " . number_format($planData['limite_credit'], 0, ',', ' ') . " XOF et " . ($planData['limite_data'] > 0 ? $planData['limite_data'] . " Go de data" : "sans data"),
                    'active' => true,
                ]
            );
        }

        $this->command->info('Forfaits standards créés avec succès !');
    }
}
