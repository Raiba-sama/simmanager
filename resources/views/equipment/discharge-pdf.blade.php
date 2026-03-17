{!! '<?xml version="1.0" encoding="UTF-8"?>' !!}
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
    <meta charset="utf-8">
    <title>Décharge - {{ e($dischargeNumber ?? '') }}</title>
    <style>
        @page { margin: 15mm; size: A4; }
        body { font-family: 'DejaVu Sans', Arial, sans-serif; font-size: 11px; margin: 0; padding: 0; color: #212529; }
        .header { border-bottom: 3px solid #00574A; padding-bottom: 12px; margin-bottom: 14px; }
        .title { font-size: 18px; font-weight: 700; color: #00574A; margin: 0; }
        .subtitle { font-size: 12px; color: #6b7280; margin-top: 4px; }
        .meta { margin-top: 8px; font-size: 11px; color: #374151; }
        .meta strong { color: #111827; }
        .section-title { margin: 14px 0 8px 0; font-size: 12px; font-weight: 700; color: #111827; }
        table { width: 100%; border-collapse: collapse; }
        th, td { border: 1px solid #e5e7eb; padding: 8px; vertical-align: top; }
        th { background: #f9fafb; text-align: left; font-weight: 700; font-size: 11px; color: #374151; }
        .badge { display: inline-block; padding: 2px 8px; border-radius: 999px; background: #eef2ff; color: #1e40af; font-size: 10px; font-weight: 700; }
        .notes { border: 1px solid #e5e7eb; background: #f9fafb; padding: 10px; border-radius: 6px; }
        .sign-grid { width: 100%; margin-top: 22px; }
        .sign-box { border: 1px solid #e5e7eb; height: 90px; border-radius: 8px; padding: 10px; }
        .sign-label { font-size: 10px; color: #6b7280; margin-bottom: 6px; font-weight: 700; text-transform: uppercase; letter-spacing: .4px; }
        .footer { margin-top: 18px; font-size: 10px; color: #6b7280; }
    </style>
</head>
<body>
    <div class="header">
        <div class="title">Décharge équipement</div>
        <div class="subtitle">Document interne — Parc Informatique</div>
        <div class="meta">
            <div><strong>N° Décharge :</strong> {{ $dischargeNumber }}</div>
            <div><strong>Date :</strong> {{ $generatedAt?->format('d/m/Y H:i') ?? now()->format('d/m/Y H:i') }}</div>
            <div><strong>Motif :</strong> <span class="badge">{{ $reasonLabel }}</span></div>
            @if(!empty($effectiveDate))
                <div><strong>Date effective :</strong> {{ \Illuminate\Support\Carbon::parse($effectiveDate)->format('d/m/Y') }}</div>
            @endif
        </div>
    </div>

    <div class="section-title">Bénéficiaire / Collaborateur</div>
    <table>
        <tr>
            <th style="width: 25%;">Nom & Prénoms</th>
            <td>{{ $user?->full_name ?? ($user?->name ?? '-') }}</td>
        </tr>
        <tr>
            <th>Matricule</th>
            <td>{{ $user?->matricule ?? '-' }}</td>
        </tr>
        <tr>
            <th>Service / Affectation</th>
            <td>{{ $user?->direction ?? ($user?->zone_affectation ?? ($user?->lieu_affectation ?? '-')) }}</td>
        </tr>
    </table>

    <div class="section-title">Équipements concernés</div>
    <table>
        <thead>
            <tr>
                <th style="width: 14%;">Tag</th>
                <th style="width: 16%;">Type</th>
                <th style="width: 18%;">Marque</th>
                <th style="width: 20%;">Modèle</th>
                <th style="width: 18%;">N° Série</th>
                <th style="width: 14%;">État</th>
            </tr>
        </thead>
        <tbody>
            @forelse($equipmentItems as $item)
                <tr>
                    <td>{{ $item['asset_tag'] ?? '-' }}</td>
                    <td>{{ $item['type'] ?? '-' }}</td>
                    <td>{{ $item['brand'] ?? '-' }}</td>
                    <td>{{ $item['model'] ?? '-' }}</td>
                    <td>{{ $item['serial_number'] ?? '-' }}</td>
                    <td>{{ $item['condition'] ?? '-' }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="6">Aucun équipement.</td>
                </tr>
            @endforelse
        </tbody>
    </table>

    @if(!empty($notes))
        <div class="section-title">Observations</div>
        <div class="notes">{{ $notes }}</div>
    @endif

    <table class="sign-grid">
        <tr>
            <td style="padding-right: 10px;">
                <div class="sign-box">
                    <div class="sign-label">Signature bénéficiaire</div>
                    <div>Nom : {{ $user?->full_name ?? '-' }}</div>
                    <div>Date :</div>
                    <div>Signature :</div>
                </div>
            </td>
            <td style="padding-left: 10px;">
                <div class="sign-box">
                    <div class="sign-label">Signature gestionnaire</div>
                    <div>Nom : {{ $generatedBy?->full_name ?? ($generatedBy?->name ?? '-') }}</div>
                    <div>Date :</div>
                    <div>Signature :</div>
                </div>
            </td>
        </tr>
    </table>

    <div class="footer">
        Ce document atteste la remise / reprise des équipements listés ci-dessus selon le motif indiqué.
    </div>
</body>
</html>

