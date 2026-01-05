<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Bordereau de Transmission - {{ $transmissionSheet->sheet_number }}</title>
    <style>
        @page {
            margin: 15mm;
            size: A4;
        }
        body {
            font-family: 'DejaVu Sans', Arial, sans-serif;
            font-size: 11px;
            margin: 0;
            padding: 0;
            color: #212529;
        }
        .header {
            text-align: center;
            margin-bottom: 20px;
            border-bottom: 3px solid #00574A;
            padding-bottom: 15px;
        }
        .header h1 {
            color: #00574A;
            margin: 0;
            font-size: 20px;
            font-weight: bold;
        }
        .header .sheet-number {
            color: #50c2bb;
            font-size: 14px;
            margin-top: 5px;
        }
        .info-section {
            margin-bottom: 20px;
        }
        .info-row {
            display: table;
            width: 100%;
            margin-bottom: 10px;
        }
        .info-label {
            display: table-cell;
            width: 30%;
            font-weight: bold;
            color: #00574A;
            vertical-align: top;
        }
        .info-value {
            display: table-cell;
            width: 70%;
            padding-left: 10px;
        }
        .equipment-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
            margin-bottom: 20px;
        }
        .equipment-table th {
            background-color: #00574A;
            color: white;
            padding: 8px;
            text-align: left;
            font-weight: bold;
            border: 1px solid #00574A;
        }
        .equipment-table td {
            padding: 8px;
            border: 1px solid #ddd;
        }
        .equipment-table tr:nth-child(even) {
            background-color: #f9fafb;
        }
        .signature-section {
            margin-top: 40px;
            display: table;
            width: 100%;
        }
        .signature-box {
            display: table-cell;
            width: 45%;
            vertical-align: top;
            padding: 10px;
        }
        .signature-line {
            border-top: 1px solid #212529;
            margin-top: 60px;
            padding-top: 5px;
            text-align: center;
        }
        .footer {
            margin-top: 30px;
            padding-top: 15px;
            border-top: 1px solid #ddd;
            font-size: 9px;
            color: #666;
            text-align: center;
        }
        .notes {
            margin-top: 20px;
            padding: 10px;
            background-color: #f9fafb;
            border-left: 3px solid #50c2bb;
        }
        .notes-label {
            font-weight: bold;
            color: #00574A;
            margin-bottom: 5px;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>BORDEREAU DE TRANSMISSION D'ÉQUIPEMENT</h1>
        <div class="sheet-number">N° {{ $transmissionSheet->sheet_number }}</div>
    </div>

    <div class="info-section">
        <div class="info-row">
            <div class="info-label">Type :</div>
            <div class="info-value">{{ $transmissionSheet->type_label }}</div>
        </div>
        <div class="info-row">
            <div class="info-label">Date de transmission :</div>
            <div class="info-value">{{ $transmissionSheet->transmission_date->format('d/m/Y') }}</div>
        </div>
        <div class="info-row">
            <div class="info-label">Statut :</div>
            <div class="info-value">{{ $transmissionSheet->status_label }}</div>
        </div>
        @if($transmissionSheet->fromUser)
        <div class="info-row">
            <div class="info-label">De :</div>
            <div class="info-value">
                {{ $transmissionSheet->fromUser->name }}
                @if($transmissionSheet->fromUser->matricule)
                    (MLE: {{ $transmissionSheet->fromUser->matricule }})
                @endif
            </div>
        </div>
        @endif
        @if($transmissionSheet->toUser)
        <div class="info-row">
            <div class="info-label">Bénéficiaire :</div>
            <div class="info-value">
                <strong>{{ $transmissionSheet->toUser->name }}</strong>
                @if($transmissionSheet->toUser->first_name)
                    {{ $transmissionSheet->toUser->first_name }}
                @endif
                @if($transmissionSheet->toUser->matricule)
                    <br>Matricule: {{ $transmissionSheet->toUser->matricule }}
                @endif
                @if($transmissionSheet->toUser->fonction)
                    <br>Fonction: {{ $transmissionSheet->toUser->fonction }}
                @endif
                @if($transmissionSheet->toUser->direction)
                    <br>Direction: {{ $transmissionSheet->toUser->direction }}
                @endif
                @if($transmissionSheet->toUser->lieu_affectation)
                    <br>Lieu d'affectation: {{ $transmissionSheet->toUser->lieu_affectation }}
                @endif
            </div>
        </div>
        @endif
        @if($transmissionSheet->fromAgency)
        <div class="info-row">
            <div class="info-label">De (Agence) :</div>
            <div class="info-value">{{ $transmissionSheet->fromAgency->name }}</div>
        </div>
        @endif
        @if($transmissionSheet->toAgency)
        <div class="info-row">
            <div class="info-label">Vers (Agence) :</div>
            <div class="info-value">{{ $transmissionSheet->toAgency->name }}</div>
        </div>
        @endif
    </div>

    <table class="equipment-table">
        <thead>
            <tr>
                <th>N° Inventaire</th>
                <th>Type</th>
                <th>Marque</th>
                <th>Modèle</th>
                <th>N° Série</th>
                <th>État</th>
            </tr>
        </thead>
        <tbody>
            @foreach($transmissionSheet->items as $item)
            <tr>
                <td>{{ $item->equipment->asset_tag ?? '-' }}</td>
                <td>{{ $item->equipment->equipmentType->name ?? '-' }}</td>
                <td>{{ $item->equipment->brand ?? '-' }}</td>
                <td>{{ $item->equipment->model ?? '-' }}</td>
                <td>{{ $item->equipment->serial_number ?? '-' }}</td>
                <td>{{ $item->condition_label ?? '-' }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>

    @if($transmissionSheet->notes)
    <div class="notes">
        <div class="notes-label">Notes :</div>
        <div>{{ $transmissionSheet->notes }}</div>
    </div>
    @endif

    <div class="signature-section">
        <div class="signature-box">
            <div class="signature-line">
                <strong>Émetteur</strong><br>
                @if($transmissionSheet->creator)
                    {{ $transmissionSheet->creator->name }}
                @endif
            </div>
        </div>
        <div class="signature-box">
            <div class="signature-line">
                <strong>Bénéficiaire</strong><br>
                @if($transmissionSheet->toUser)
                    {{ $transmissionSheet->toUser->name }}
                @endif
            </div>
        </div>
    </div>

    @if($transmissionSheet->signed_by_recipient && $transmissionSheet->signed_at)
    <div class="info-section" style="margin-top: 20px;">
        <div class="info-row">
            <div class="info-label">Signé le :</div>
            <div class="info-value">{{ $transmissionSheet->signed_at->format('d/m/Y à H:i') }}</div>
        </div>
    </div>
    @endif

    <div class="footer">
        <p>Document généré le {{ now()->format('d/m/Y à H:i') }} - ACEP Madagascar - Direction des Systèmes d'Information</p>
        <p>© {{ date('Y') }} - Développé par Joachim et l'équipe DSI - ACEP</p>
    </div>
</body>
</html>

