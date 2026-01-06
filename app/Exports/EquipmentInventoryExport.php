<?php

namespace App\Exports;

use App\Models\Equipment;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Alignment;

class EquipmentInventoryExport implements FromCollection, WithHeadings, WithMapping, WithStyles, WithTitle, WithColumnWidths
{
    protected $equipment;

    public function __construct($equipment)
    {
        $this->equipment = $equipment;
    }

    public function collection()
    {
        return $this->equipment;
    }

    public function headings(): array
    {
        return [
            'Tag / N° Inventaire',
            'Type',
            'Marque',
            'Modèle',
            'N° Série',
            'MAC Address',
            'IP Address',
            'Statut',
            'Condition',
            'Date d\'achat',
            'Prix d\'achat (XOF)',
            'Garantie jusqu\'au',
            'Assigné à',
            'Matricule',
            'Date d\'attribution',
            'Date de création',
        ];
    }

    public function map($equipment): array
    {
        $currentAssignment = $equipment->assignments()->whereNull('returned_at')->first();
        
        return [
            $equipment->asset_tag ?? '-',
            $equipment->equipmentType->name ?? '-',
            $equipment->brand ?? '-',
            $equipment->model ?? '-',
            $equipment->serial_number ?? '-',
            $equipment->mac_address ?? '-',
            $equipment->ip_address ?? '-',
            match($equipment->status) {
                'available' => 'Disponible',
                'assigned' => 'Attribué',
                'maintenance' => 'En maintenance',
                'retired' => 'Retiré',
                default => $equipment->status,
            },
            match($equipment->condition) {
                'new' => 'Neuf',
                'excellent' => 'Excellent',
                'good' => 'Bon',
                'fair' => 'Moyen',
                'poor' => 'Mauvais',
                default => $equipment->condition,
            },
            $equipment->purchase_date ? $equipment->purchase_date->format('d/m/Y') : '-',
            $equipment->purchase_price ? number_format($equipment->purchase_price, 0, ',', ' ') : '-',
            $equipment->warranty_expires_at ? $equipment->warranty_expires_at->format('d/m/Y') : '-',
            $currentAssignment?->assignedToUser?->full_name ?? ($currentAssignment?->assignedToAgency?->name ?? '-'),
            $currentAssignment?->assignedToUser?->matricule ?? '-',
            $currentAssignment?->assigned_at ? $currentAssignment->assigned_at->format('d/m/Y') : '-',
            $equipment->created_at->format('d/m/Y H:i'),
        ];
    }

    public function styles(Worksheet $sheet)
    {
        return [
            1 => [
                'font' => ['bold' => true, 'size' => 12],
                'fill' => [
                    'fillType' => Fill::FILL_SOLID,
                    'startColor' => ['rgb' => '00574A'],
                ],
                'font' => ['color' => ['rgb' => 'FFFFFF'], 'bold' => true],
                'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
            ],
        ];
    }

    public function columnWidths(): array
    {
        return [
            'A' => 20,  // Tag
            'B' => 15,  // Type
            'C' => 15,  // Marque
            'D' => 20,  // Modèle
            'E' => 20,  // N° Série
            'F' => 18,  // MAC
            'G' => 15,  // IP
            'H' => 15,  // Statut
            'I' => 12,  // Condition
            'J' => 12,  // Date achat
            'K' => 15,  // Prix
            'L' => 15,  // Garantie
            'M' => 25,  // Assigné à
            'N' => 12,  // Matricule
            'O' => 15,  // Date attribution
            'P' => 18,  // Date création
        ];
    }

    public function title(): string
    {
        return 'Inventaire Équipements';
    }
}

