<?php

namespace App\Exports;

use App\Models\Sim;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class SimsExport implements FromCollection, WithHeadings, WithMapping
{
    protected $sims;

    public function __construct($sims)
    {
        $this->sims = $sims;
    }

    public function collection()
    {
        return $this->sims;
    }

    public function headings(): array
    {
        return [
            'ICCID',
            'Numéro téléphone',
            'Opérateur',
            'Type de plan',
            'Coût mensuel',
            'Statut',
            'Assignée à',
            'Matricule',
            'Date attribution',
            'Date création',
        ];
    }

    public function map($sim): array
    {
        return [
            $sim->iccid,
            $sim->phone_number ?? 'N/A',
            $sim->operator ?? 'N/A',
            $sim->plan_type ?? 'N/A',
            $sim->monthly_cost ? number_format($sim->monthly_cost, 0, ',', ' ') . ' ariary' : 'N/A',
            ucfirst($sim->status),
            $sim->assignedUser->full_name ?? 'Non attribuée',
            $sim->assignedUser->matricule ?? 'N/A',
            $sim->assigned_at ? \Carbon\Carbon::parse($sim->assigned_at)->format('d/m/Y') : 'N/A',
            $sim->created_at->format('d/m/Y H:i'),
        ];
    }

}

