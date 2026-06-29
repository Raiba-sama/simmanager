<!DOCTYPE html>
<html lang="fr">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
    <meta charset="UTF-8">
    <title>{{ $isReturn ? 'Bordereau Retour Réparation' : 'Bordereau Envoi Réparation' }} - {{ $equipment->asset_tag ?? $equipment->serial_number }}</title>
    <style>
        @page { size: A4; margin: 15mm; }
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body {
            font-family: 'DejaVu Sans', Arial, sans-serif;
            font-size: 10pt; color: #000;
        }
        .header {
            display: flex; justify-content: space-between;
            align-items: flex-start; margin-bottom: 20px;
            padding-bottom: 10px; border-bottom: 1px solid #000;
        }
        .logo { width: 90px; height: auto; }
        .title {
            text-align: center; font-size: 15pt; font-weight: bold;
            margin: 15px 0 20px 0; text-transform: uppercase;
            letter-spacing: 1px; color: #000; padding: 8px 0;
            border-top: 2px solid #000; border-bottom: 2px solid #000;
        }
        .info-section { margin-bottom: 15px; }
        .info-row {
            display: flex; margin-bottom: 6px;
            align-items: flex-end; min-height: 20px;
        }
        .info-label {
            width: 185px; font-weight: bold; color: #000;
            font-size: 9.5pt; flex-shrink: 0;
        }
        .info-value {
            flex: 1; border-bottom: 1px solid #000;
            min-height: 18px; padding: 2px 8px 1px 8px;
            font-size: 10pt; color: #000;
        }
        table { width: 100%; border-collapse: collapse; margin-top: 10px; }
        th {
            background: #f0f0f0; color: #000; font-weight: bold;
            padding: 7px 6px; text-align: left;
            font-size: 9pt; border: 1px solid #000;
        }
        td {
            border: 1px solid #000; padding: 7px 6px;
            text-align: left; font-size: 9.5pt; background: white;
        }
        .section-title {
            font-size: 9.5pt; font-weight: bold;
            margin: 14px 0 5px 0; text-transform: uppercase;
            letter-spacing: .5px;
        }
        .notes-box {
            border: 1px solid #000; padding: 6px 8px;
            font-size: 9.5pt; min-height: 28px;
        }
        .signatures {
            margin-top: 40px; display: flex;
            justify-content: space-between; padding-top: 15px;
        }
        .signature-box { width: 160px; text-align: center; }
        .signature-line {
            border-top: 1px solid #000; margin-top: 50px;
            padding-top: 5px; font-weight: bold;
            font-size: 9.5pt;
        }
        .note {
            margin-top: 20px; font-size: 8pt; font-style: italic;
            text-align: center; padding: 8px;
            background: #f5f5f5; border: 1px solid #000;
        }
        .badge-repair {
            display: inline-block; padding: 2px 7px; border-radius: 3px;
            background: #fff3cd; color: #856404;
            font-size: 9pt; font-weight: bold;
        }
        .badge-return {
            display: inline-block; padding: 2px 7px; border-radius: 3px;
            background: #d1fae5; color: #065f46;
            font-size: 9pt; font-weight: bold;
        }
    </style>
</head>
<body>
    <div class="header">
        <img src="{{ public_path('images/acep_madagascar_logo-1.png') }}" alt="ACEP Logo" class="logo">
        <div style="flex:1;"></div>
    </div>

    <div class="title">
        {{ $isReturn ? 'BORDEREAU DE RETOUR DE RÉPARATION' : 'BORDEREAU D\'ENVOI EN RÉPARATION' }}
    </div>

    <div class="info-section">
        <div class="info-row">
            <div class="info-label">Le Décisionnaire :</div>
            <div class="info-value">Direction Système D'Information</div>
        </div>
        <div class="info-row">
            <div class="info-label">{{ $isReturn ? 'Réceptionné par :' : 'Envoyé par :' }}</div>
            <div class="info-value">{{ $creator ? $creator->full_name . ' (' . ($creator->fonction ?? 'Dépt Informatique') . ')' : '' }}</div>
        </div>
        <div class="info-row">
            <div class="info-label">Fournisseur / Réparateur :</div>
            <div class="info-value">{{ $sendout->supplier_name }}</div>
        </div>
        <div class="info-row">
            <div class="info-label">Référence / N° Bon :</div>
            <div class="info-value">{{ $sendout->supplier_reference ?? '—' }}</div>
        </div>
        <div class="info-row">
            <div class="info-label">Date d'envoi :</div>
            <div class="info-value">{{ $sendout->sent_at ? \Carbon\Carbon::parse($sendout->sent_at)->locale('fr')->isoFormat('dddd D MMMM YYYY') : '—' }}</div>
        </div>
        @if($isReturn)
        <div class="info-row">
            <div class="info-label">Date de retour :</div>
            <div class="info-value">{{ $sendout->returned_at ? \Carbon\Carbon::parse($sendout->returned_at)->locale('fr')->isoFormat('dddd D MMMM YYYY') : '—' }}</div>
        </div>
        @else
        <div class="info-row">
            <div class="info-label">Retour prévu le :</div>
            <div class="info-value">{{ $sendout->expected_return_at ? \Carbon\Carbon::parse($sendout->expected_return_at)->locale('fr')->isoFormat('dddd D MMMM YYYY') : '—' }}</div>
        </div>
        @endif
        <div class="info-row">
            <div class="info-label">Lieu de départ :</div>
            <div class="info-value">{{ $isReturn ? $sendout->supplier_name : 'ACEP FARAVOHITRA' }}</div>
        </div>
        <div class="info-row">
            <div class="info-label">Lieu de destination :</div>
            <div class="info-value">{{ $isReturn ? 'ACEP FARAVOHITRA' : $sendout->supplier_name }}</div>
        </div>
        <div class="info-row">
            <div class="info-label">BT Numéro :</div>
            <div class="info-value">
                <strong>{{ $sendout->supplier_reference ?? ('REP-' . str_pad($sendout->id, 4, '0', STR_PAD_LEFT) . '-' . \Carbon\Carbon::parse($sendout->sent_at)->format('Ymd')) }}</strong>
            </div>
        </div>
    </div>

    <div class="section-title">Équipement concerné</div>
    <table>
        <thead>
            <tr>
                <th style="width:5%;">Qté</th>
                <th style="width:12%;">Tag actif</th>
                <th style="width:16%;">Type</th>
                <th style="width:22%;">Marque / Modèle</th>
                <th style="width:22%;">N° de Série</th>
                <th style="width:12%;">État</th>
                <th style="width:11%;">Statut</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td style="text-align:center;">1</td>
                <td>{{ $equipment->asset_tag ?? '—' }}</td>
                <td>{{ $equipment->equipmentType?->name ?? '—' }}</td>
                <td>{{ trim(($equipment->brand ?? '') . ' ' . ($equipment->model ?? '')) ?: '—' }}</td>
                <td>{{ $equipment->serial_number ?? '—' }}</td>
                <td>{{ $equipment->condition_label }}</td>
                <td>
                    @if($isReturn)
                        <span class="badge-return">Retourné</span>
                    @else
                        <span class="badge-repair">En réparation</span>
                    @endif
                </td>
            </tr>
        </tbody>
    </table>

    @if($sendout->notes)
    <div class="section-title">Observations</div>
    <div class="notes-box">{{ $sendout->notes }}</div>
    @endif

    <div class="signatures">
        <div class="signature-box">
            <div class="signature-line">Le Remettant,</div>
        </div>
        <div class="signature-box">
            <div class="signature-line">Le Transportant (*)</div>
        </div>
        <div class="signature-box">
            <div class="signature-line">Le Réceptionnaire,(*)</div>
        </div>
    </div>

    <div class="note">
        (*): Appose articles reçus conforme le :  et signe.
    </div>
</body>
</html>
