{!! '<?xml version="1.0" encoding="UTF-8"?>' !!}
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
    <meta charset="utf-8">
    <title>Bordereau de Transmission - {{ e($transmissionSheet->sheet_number ?? '') }}</title>
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
            position: relative;
            text-align: center;
            margin-bottom: 20px;
            border-bottom: 3px solid #00574A;
            padding-bottom: 15px;
            padding-top: 10px;
            min-height: 70px;
        }
        .header .logo {
            position: absolute;
            left: 0;
            top: 5px;
            max-width: 80px;
            max-height: 60px;
            width: auto;
            height: auto;
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
    @php
        $clean = function($value) {
            if (is_null($value)) return '';
            $str = (string) $value;
            $str = @iconv('UTF-8', 'UTF-8//IGNORE', $str);
            if ($str === false) $str = '';
            $str = preg_replace('/[\x00-\x08\x0B\x0C\x0E-\x1F\x7F]/u', '', $str);
            return htmlspecialchars($str, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8', false);
        };
    @endphp
    <div class="header">
        @php
            $logoPath = public_path('images/acep_madagascar_logo-1.png');
            $logoBase64 = '';
            if (file_exists($logoPath)) {
                $logoData = file_get_contents($logoPath);
                $logoBase64 = 'data:image/png;base64,' . base64_encode($logoData);
            }
        @endphp
        @if($logoBase64)
        <img src="{{ $logoBase64 }}" alt="ACEP Madagascar" class="logo">
        @endif
        <h1>BORDEREAU DE TRANSMISSION D'ÉQUIPEMENT</h1>
        <div class="sheet-number">N° {{ $clean($transmissionSheet->sheet_number ?? '') }}</div>
    </div>

    <div class="info-section">
        <div class="info-row">
            <div class="info-label">Type :</div>
            <div class="info-value">{{ $clean($transmissionSheet->type_label ?? '') }}</div>
        </div>
        <div class="info-row">
            <div class="info-label">Date de transmission :</div>
            <div class="info-value">{{ $transmissionSheet->transmission_date->format('d/m/Y') }}</div>
        </div>
        <div class="info-row">
            <div class="info-label">Statut :</div>
            <div class="info-value">{{ $clean($transmissionSheet->status_label ?? '') }}</div>
        </div>
        @if($transmissionSheet->fromUser)
        <div class="info-row">
            <div class="info-label">De :</div>
            <div class="info-value">
                {{ $clean($transmissionSheet->fromUser->name ?? '') }}
                @if($transmissionSheet->fromUser->matricule)
                    (MLE: {{ $clean($transmissionSheet->fromUser->matricule) }})
                @endif
            </div>
        </div>
        @endif
        @if($transmissionSheet->toUser)
        <div class="info-row">
            <div class="info-label">Bénéficiaire :</div>
            <div class="info-value">
                <strong>{{ $clean($transmissionSheet->toUser->name ?? '') }}</strong>
                @if($transmissionSheet->toUser->first_name)
                    {{ $clean($transmissionSheet->toUser->first_name) }}
                @endif
                @if($transmissionSheet->toUser->matricule)
                    <br>Matricule: {{ $clean($transmissionSheet->toUser->matricule) }}
                @endif
                @if($transmissionSheet->toUser->fonction)
                    <br>Fonction: {{ $clean($transmissionSheet->toUser->fonction) }}
                @endif
                @if($transmissionSheet->toUser->direction)
                    <br>Direction: {{ $clean($transmissionSheet->toUser->direction) }}
                @endif
                @if($transmissionSheet->toUser->lieu_affectation)
                    <br>Lieu d'affectation: {{ $clean($transmissionSheet->toUser->lieu_affectation) }}
                @endif
            </div>
        </div>
        @endif
        @if($transmissionSheet->fromAgency)
        <div class="info-row">
            <div class="info-label">De (Agence) :</div>
            <div class="info-value">{{ $clean($transmissionSheet->fromAgency->name ?? '') }}</div>
        </div>
        @endif
        @if($transmissionSheet->toAgency)
        <div class="info-row">
            <div class="info-label">Vers (Agence) :</div>
            <div class="info-value">{{ $clean($transmissionSheet->toAgency->name ?? '') }}</div>
        </div>
        @endif
    </div>

    <table class="equipment-table">
        <thead>
            <tr>
                <th>Tag / N° Inventaire</th>
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
                <td>
                    @if($item->equipment->asset_tag)
                        <strong>{{ $clean($item->equipment->asset_tag) }}</strong>
                    @else
                        -
                    @endif
                </td>
                <td>{{ $clean($item->equipment->equipmentType->name ?? '-') }}</td>
                <td>{{ $clean($item->equipment->brand ?? '-') }}</td>
                <td>{{ $clean($item->equipment->model ?? '-') }}</td>
                <td>{{ $clean($item->equipment->serial_number ?? '-') }}</td>
                <td>{{ $clean($item->condition_label ?? '-') }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>

    @if($transmissionSheet->notes)
    <div class="notes">
        <div class="notes-label">Notes :</div>
        <div>{{ $clean($transmissionSheet->notes) }}</div>
    </div>
    @endif

    <div class="signature-section">
        <div class="signature-box">
            <div class="signature-line">
                <strong>Émetteur</strong><br>
                @if($transmissionSheet->creator)
                    {{ $clean($transmissionSheet->creator->name ?? '') }}
                @endif
            </div>
        </div>
        <div class="signature-box">
            <div class="signature-line">
                <strong>Bénéficiaire</strong><br>
                @if($transmissionSheet->toUser)
                    {{ $clean($transmissionSheet->toUser->name ?? '') }}
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

