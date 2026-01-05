<?php

namespace Database\Seeders;

use App\Models\EquipmentType;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class EquipmentTypeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $types = [
            // Ordinateurs
            ['name' => 'Desktop', 'category' => 'computer', 'description' => 'Ordinateur de bureau'],
            ['name' => 'Laptop', 'category' => 'computer', 'description' => 'Ordinateur portable'],
            ['name' => 'Tablette', 'category' => 'computer', 'description' => 'Tablette tactile'],
            
            // Périphériques
            ['name' => 'Imprimante', 'category' => 'peripheral', 'description' => 'Imprimante'],
            ['name' => 'Écran', 'category' => 'peripheral', 'description' => 'Écran d\'ordinateur'],
            ['name' => 'Souris', 'category' => 'peripheral', 'description' => 'Souris informatique'],
            ['name' => 'Clavier', 'category' => 'peripheral', 'description' => 'Clavier informatique'],
            ['name' => 'Webcam', 'category' => 'peripheral', 'description' => 'Caméra web'],
            ['name' => 'Casque audio', 'category' => 'peripheral', 'description' => 'Casque audio'],
            
            // Réseau
            ['name' => 'Switch', 'category' => 'network', 'description' => 'Commutateur réseau'],
            ['name' => 'Router', 'category' => 'network', 'description' => 'Routeur réseau'],
            ['name' => 'Firewall', 'category' => 'network', 'description' => 'Pare-feu'],
            ['name' => 'Access Point WiFi', 'category' => 'network', 'description' => 'Point d\'accès WiFi'],
            ['name' => 'Modem', 'category' => 'network', 'description' => 'Modem'],
            
            // Accessoires
            ['name' => 'Câble USB', 'category' => 'accessory', 'description' => 'Câble USB'],
            ['name' => 'Câble HDMI', 'category' => 'accessory', 'description' => 'Câble HDMI'],
            ['name' => 'Câble Ethernet', 'category' => 'accessory', 'description' => 'Câble Ethernet'],
            ['name' => 'Adaptateur', 'category' => 'accessory', 'description' => 'Adaptateur'],
            ['name' => 'Hub USB', 'category' => 'accessory', 'description' => 'Hub USB'],
            ['name' => 'Multiprise', 'category' => 'accessory', 'description' => 'Multiprise'],
            ['name' => 'Batterie externe', 'category' => 'accessory', 'description' => 'Batterie externe'],
        ];

        foreach ($types as $type) {
            EquipmentType::updateOrCreate(
                ['name' => $type['name']],
                $type
            );
        }
    }
}
