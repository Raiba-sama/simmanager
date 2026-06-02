<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Bordereau de Transmission - {{ $simRequest->request_number }}</title>
    <style>
        @page {
            size: A4;
            margin: 15mm;
        }
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        body {
            font-family: Arial, sans-serif;
            font-size: 10pt;
            color: #000;
            background: #e8e8e8;
            margin: 0;
            padding: 15px;
        }
        .container {
            background: white;
            padding: 20px 25px;
            width: 210mm;
            min-height: 277mm;
            margin: 0 auto;
            box-shadow: 0 2px 8px rgba(0,0,0,0.1);
        }
        @media print {
            body {
                background: white;
                padding: 0;
            }
            .container {
                box-shadow: none;
                margin: 0;
                padding: 15mm;
                width: 100%;
                min-height: 100vh;
            }
            .no-print {
                display: none;
            }
        }
        .print-button {
            position: fixed;
            top: 20px;
            right: 20px;
            background: #555;
            color: white;
            padding: 10px 20px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            font-size: 13px;
            font-weight: 600;
            z-index: 1000;
            box-shadow: 0 2px 8px rgba(0,0,0,0.2);
        }
        .print-button:hover {
            background: #333;
        }
        .header {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            margin-bottom: 20px;
            padding-bottom: 10px;
            border-bottom: 1px solid #000;
        }
        .logo {
            width: 100px;
            height: auto;
            max-height: 50px;
            object-fit: contain;
        }
        .title {
            text-align: center;
            font-size: 16pt;
            font-weight: bold;
            margin: 15px 0 20px 0;
            text-transform: uppercase;
            letter-spacing: 1px;
            color: #000;
            padding: 8px 0;
            border-top: 2px solid #000;
            border-bottom: 2px solid #000;
        }
        .info-section {
            margin-bottom: 15px;
        }
        .info-row {
            display: flex;
            margin-bottom: 6px;
            align-items: baseline;
            min-height: 20px;
        }
        .info-label {
            width: 180px;
            font-weight: bold;
            color: #000;
            font-size: 9.5pt;
            flex-shrink: 0;
        }
        .info-value {
            flex: 1;
            border-bottom: 1px solid #000;
            min-height: 18px;
            padding: 2px 8px 1px 8px;
            font-size: 10pt;
            color: #000;
        }
        .info-value:empty::after {
            content: ' ';
            display: inline-block;
        }
        .table-container {
            margin: 20px 0;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
        }
        th {
            background: #f0f0f0;
            color: #000;
            font-weight: bold;
            padding: 8px 6px;
            text-align: left;
            font-size: 9.5pt;
            border: 1px solid #000;
        }
        td {
            border: 1px solid #000;
            padding: 8px 6px;
            text-align: left;
            font-size: 10pt;
            background: white;
        }
        .signatures {
            margin-top: 40px;
            display: flex;
            justify-content: space-between;
            padding-top: 15px;
        }
        .signature-box {
            width: 180px;
            text-align: center;
        }
        .signature-line {
            border-top: 1px solid #000;
            margin-top: 50px;
            padding-top: 5px;
            font-weight: 500;
            color: #000;
            font-size: 9.5pt;
        }
        .note {
            margin-top: 20px;
            font-size: 8pt;
            font-style: italic;
            color: #000;
            text-align: center;
            padding: 8px;
            background: #f5f5f5;
            border: 1px solid #000;
        }
    </style>
</head>
<body>
    <button onclick="window.print()" class="print-button no-print">
        🖨️ Imprimer / Enregistrer en PDF
    </button>
    <div class="container">
        <div class="header">
            <img src="{{ asset('images/acep_madagascar_logo-1.png') }}" alt="ACEP Logo" class="logo">
            <div style="flex: 1;"></div>
        </div>

        <div class="title">BORDEREAU DE TRANSMISSION</div>

        <div class="info-section">
            <div class="info-row">
                <div class="info-label">Le Décisionnaire:</div>
                <div class="info-value">Direction Système D'Information</div>
            </div>
            <div class="info-row">
                <div class="info-label">Le Remettant:</div>
                <div class="info-value">{{ $simRequest->creator ? $simRequest->creator->full_name . ' (' . ($simRequest->creator->fonction ?? 'Dépt Informatique') . ')' : '' }}</div>
            </div>
            <div class="info-row">
                <div class="info-label">Le Transmettant:</div>
                <div class="info-value">{{ $simRequest->admin ? $simRequest->admin->full_name . ' (' . ($simRequest->admin->fonction ?? 'Dépt Informatique') . ')' : ($simRequest->validator ? $simRequest->validator->full_name . ' (' . ($simRequest->validator->fonction ?? 'Dépt Informatique') . ')' : '') }}</div>
            </div>
            <div class="info-row">
                <div class="info-label">Le Réceptionnaire:</div>
                <div class="info-value">
                    @if($simRequest->isCreation())
                        @if($simRequest->beneficiary_matricule)
                            {{ $simRequest->beneficiary_matricule }} – {{ trim(($simRequest->beneficiary_name ?? '') . ' ' . ($simRequest->beneficiary_first_name ?? '')) }}
                        @else
                            {{ trim(($simRequest->beneficiary_name ?? '') . ' ' . ($simRequest->beneficiary_first_name ?? '')) }}
                        @endif
                    @elseif($simRequest->collaborator_matricule || $simRequest->collaborator_name)
                        {{ $simRequest->collaborator_matricule ? $simRequest->collaborator_matricule . ' – ' : '' }}{{ trim(($simRequest->collaborator_name ?? '') . ' ' . ($simRequest->collaborator_first_name ?? '')) }}
                    @else
                        {{ $simRequest->user->matricule ?? '' }}{{ $simRequest->user->matricule ? ' – ' : '' }}{{ $simRequest->user->full_name }}
                    @endif
                </div>
            </div>
            <div class="info-row">
                <div class="info-label">Date du transfert:</div>
                <div class="info-value">
                    @php
                        $date = $simRequest->admin_processed_at ?? $simRequest->validated_at ?? now();
                        $dateFormatted = \Carbon\Carbon::parse($date)->locale('fr')->isoFormat('dddd D MMMM YYYY');
                    @endphp
                    {{ $dateFormatted }}
                </div>
            </div>
            <div class="info-row">
                <div class="info-label">Moyen de transfert:</div>
                <div class="info-value"></div>
            </div>
            <div class="info-row">
                <div class="info-label">Lieu de départ:</div>
                <div class="info-value">ACEP FARAVOHITRA</div>
            </div>
            <div class="info-row">
                <div class="info-label">Lieu de destination:</div>
                <div class="info-value">RH - FARAVOHITRA</div>
            </div>
            <div class="info-row">
                <div class="info-label">BT Numéro:</div>
                <div class="info-value"><strong>{{ $simRequest->request_number }}</strong></div>
            </div>
        </div>

        <div class="table-container">
            <table>
                <thead>
                    <tr>
                        <th>Quantité</th>
                        <th>Numéro</th>
                        <th>ICCID :</th>
                        <th>Désignation :</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td style="text-align: center;">1</td>
                        <td>{{ $simRequest->phone_number ?? ($simRequest->sim ? $simRequest->sim->phone_number : 'N/A') }}</td>
                        <td>{{ $simRequest->requested_iccid ?? ($simRequest->sim ? $simRequest->sim->iccid : 'N/A') }}</td>
                        <td>
                            @php
                                $typeLabels = [
                                    'recuperation' => 'Récupération',
                                    'creation' => 'Création',
                                    'suspension' => 'Suspension',
                                    'desactivation' => 'Désactivation',
                                    'ajustement' => 'Ajustement',
                                ];
                                $typeLabel = $typeLabels[$simRequest->request_type] ?? ucfirst($simRequest->request_type);
                                
                                $designation = 'Demande de ' . strtolower($typeLabel);
                                
                                if ($simRequest->plan) {
                                    $designation .= ' de ligne';
                                    if ($simRequest->plan->limite_data > 0) {
                                        $designation .= ' data ' . number_format($simRequest->plan->limite_data, 1, ',', ' ') . 'Go';
                                    }
                                    if ($simRequest->plan->limite_credit > 0) {
                                        $designation .= ($simRequest->plan->limite_data > 0 ? ' avec ' : ' ') . number_format($simRequest->plan->limite_credit, 0, ',', ' ') . ' ariary';
                                    }
                                } elseif ($simRequest->limite_data || $simRequest->limite_credit) {
                                    $designation .= ' de ligne';
                                    if ($simRequest->limite_data > 0) {
                                        $designation .= ' data ' . number_format($simRequest->limite_data, 1, ',', ' ') . 'Go';
                                    }
                                    if ($simRequest->limite_credit > 0) {
                                        $designation .= ($simRequest->limite_data > 0 ? ' avec ' : ' ') . number_format($simRequest->limite_credit, 0, ',', ' ') . ' ariary';
                                    }
                                }
                                
                                if ($simRequest->isCreation() && $simRequest->beneficiary_fonction) {
                                    $designation .= ' pour ' . strtolower($simRequest->beneficiary_fonction);
                                }
                            @endphp
                            {{ $designation }}
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>

        <div class="signatures">
            <div class="signature-box">
                <div class="signature-line">Le Remettant,</div>
            </div>
            <div class="signature-box">
                <div class="signature-line">Le Transmettant (*)</div>
            </div>
            <div class="signature-box">
                <div class="signature-line">Le Réceptionnaire,(*)</div>
            </div>
        </div>

        <div class="note">
            (*): Appose articles reçus conforme le :  et signe.
        </div>
    </div>
</body>
</html>
