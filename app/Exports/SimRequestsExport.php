<?php

namespace App\Exports;

use App\Models\SimRequest;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class SimRequestsExport implements FromCollection, WithHeadings, WithMapping
{
    protected $requests;

    public function __construct($requests)
    {
        $this->requests = $requests;
    }

    public function collection()
    {
        return $this->requests;
    }

    public function headings(): array
    {
        return [
            'N° Demande',
            'Type',
            'Statut',
            'Demandeur',
            'Email',
            'Matricule',
            'ICCID',
            'Téléphone',
            'Plan',
            'Motif',
            'Validateur',
            'Date création',
            'Date validation',
        ];
    }

    public function map($request): array
    {
        return [
            $request->request_number,
            ucfirst($request->request_type),
            ucfirst(str_replace('_', ' ', $request->status)),
            $request->user->full_name ?? 'N/A',
            $request->user->email ?? 'N/A',
            $request->user->matricule ?? 'N/A',
            $request->sim->iccid ?? ($request->requested_iccid ?? 'N/A'),
            $request->phone_number ?? ($request->sim->phone_number ?? 'N/A'),
            $request->plan->formatted_name ?? 'N/A',
            $request->motif ?? 'N/A',
            $request->validator->full_name ?? 'N/A',
            $request->created_at->format('d/m/Y H:i'),
            $request->validator_validated_at ? $request->validator_validated_at->format('d/m/Y H:i') : 'N/A',
        ];
    }

}

