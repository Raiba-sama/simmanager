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
            'Collaborateur',
            'Matricule collaborateur',
            'Agence',
            'ICCID',
            'Téléphone',
            'Ligne concernée',
            'Plan',
            'Motif',
            'Validateur',
            'Créé par',
            'Date création',
            'Date validation',
        ];
    }

    public function map($request): array
    {
        $collaboratorName = null;
        $collaboratorMatricule = null;
        if ($request->isCreation()) {
            $collaboratorName = trim(($request->beneficiary_name ?? '') . ' ' . ($request->beneficiary_first_name ?? ''));
            $collaboratorMatricule = $request->beneficiary_matricule;
        } else {
            $collaboratorName = trim(($request->collaborator_name ?? '') . ' ' . ($request->collaborator_first_name ?? ''));
            $collaboratorMatricule = $request->collaborator_matricule;
        }
        $collaboratorName = $collaboratorName ?: 'N/A';
        $collaboratorMatricule = $collaboratorMatricule ?: 'N/A';
        $lineNumber = $request->phone_number ?? ($request->sim->phone_number ?? 'N/A');

        return [
            $request->request_number,
            ucfirst($request->request_type),
            ucfirst(str_replace('_', ' ', $request->status)),
            $request->user->full_name ?? 'N/A',
            $request->user->email ?? 'N/A',
            $request->user->matricule ?? 'N/A',
            $collaboratorName,
            $collaboratorMatricule,
            $request->collaborator_agence ?? 'N/A',
            $request->sim->iccid ?? ($request->requested_iccid ?? 'N/A'),
            $request->phone_number ?? ($request->sim->phone_number ?? 'N/A'),
            $lineNumber,
            $request->plan->formatted_name ?? 'N/A',
            $request->motif ?? 'N/A',
            $request->validator->full_name ?? 'N/A',
            $request->creator->full_name ?? 'N/A',
            $request->created_at->format('d/m/Y H:i'),
            $request->validated_at ? $request->validated_at->format('d/m/Y H:i') : ($request->admin_processed_at ? $request->admin_processed_at->format('d/m/Y H:i') : 'N/A'),
        ];
    }

}

