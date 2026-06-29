<!DOCTYPE html>
<html lang="fr">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
    <title>{{ $isReturn ? 'Bordereau Retour Réparation' : 'Bordereau Envoi Réparation' }}</title>
    <style>
        @page { size: A4; margin: 0; }
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: 'DejaVu Sans', Arial, sans-serif; font-size: 10pt; color: #1a1a1a; }
        .page-wrap { padding: 18mm 22mm 20mm 22mm; }

        /* ── Header ──────────────────────────────────── */
        .hdr { width: 100%; border-collapse: collapse; margin-bottom: 14px; }
        .hdr td { vertical-align: middle; padding: 0; }
        .hdr-right { text-align: right; }
        .hdr-right .doc-label { font-size: 8pt; color: #6b7280; text-transform: uppercase; letter-spacing: .8px; }
        .hdr-right .doc-ref {
            display: inline-block; margin-top: 4px;
            background: #f0fdf4; border: 1px solid #6ee7b7;
            color: #065f46; font-size: 9pt; font-weight: bold;
            padding: 3px 10px; border-radius: 4px;
        }

        /* ── Title band ──────────────────────────────── */
        .title-band {
            background: #00574A; color: white;
            text-align: center; font-size: 14pt; font-weight: bold;
            text-transform: uppercase; letter-spacing: 1.5px;
            padding: 11px 0; margin-bottom: 16px;
        }

        /* ── Meta row ────────────────────────────────── */
        .meta { font-size: 8.5pt; color: #6b7280; margin-bottom: 14px; }

        /* ── Section header ──────────────────────────── */
        .sec-hdr {
            background: #f0fdf4; border-left: 3px solid #00574A;
            color: #00574A; font-size: 9pt; font-weight: bold;
            text-transform: uppercase; letter-spacing: .5px;
            padding: 5px 10px; margin: 14px 0 8px 0;
        }

        /* ── Info table ──────────────────────────────── */
        .info-tbl { width: 100%; border-collapse: collapse; margin-bottom: 4px; }
        .info-tbl tr { border-bottom: 1px solid #f1f5f9; }
        .info-tbl td { padding: 5px 8px; vertical-align: top; }
        .info-tbl .lbl {
            width: 38%; font-size: 9pt; font-weight: bold;
            color: #374151; white-space: nowrap;
        }
        .info-tbl .val { font-size: 9.5pt; color: #111827; }
        .info-tbl tr:nth-child(even) td { background: #fafafa; }

        /* ── Equipment table ─────────────────────────── */
        .eq-tbl { width: 100%; border-collapse: collapse; }
        .eq-tbl thead tr { background: #00574A; }
        .eq-tbl thead th {
            color: white; font-size: 8.5pt; font-weight: bold;
            padding: 7px 6px; text-align: left; border: 1px solid #004a40;
        }
        .eq-tbl tbody td {
            border: 1px solid #e2e8f0; padding: 7px 6px;
            font-size: 9.5pt; vertical-align: middle;
        }
        .eq-tbl tbody tr { background: white; }

        /* ── Badges ──────────────────────────────────── */
        .b-repair {
            background: #fef3c7; color: #92400e;
            border: 1px solid #fcd34d; padding: 2px 7px;
            border-radius: 3px; font-weight: bold; font-size: 8.5pt;
        }
        .b-return {
            background: #d1fae5; color: #065f46;
            border: 1px solid #6ee7b7; padding: 2px 7px;
            border-radius: 3px; font-weight: bold; font-size: 8.5pt;
        }

        /* ── Notes ───────────────────────────────────── */
        .notes-box {
            border: 1px solid #fcd34d; border-left: 3px solid #f59e0b;
            background: #fffbeb; padding: 8px 10px;
            font-size: 9.5pt; color: #374151;
        }

        /* ── Divider ─────────────────────────────────── */
        hr.div { border: none; border-top: 1px solid #e5e7eb; margin: 10px 0; }

        /* ── Signatures ──────────────────────────────── */
        .sign-tbl { width: 100%; border-collapse: collapse; margin-top: 36px; }
        .sign-tbl td { width: 33%; text-align: center; padding: 0 10px; vertical-align: bottom; }
        .sign-name { font-size: 9pt; font-weight: bold; color: #374151; margin-bottom: 2px; }
        .sign-area {
            border: 1px solid #d1d5db; border-radius: 4px;
            height: 68px; background: #fafafa;
            margin-top: 6px;
        }
        .sign-line {
            font-size: 8pt; color: #6b7280; margin-top: 5px;
        }

        /* ── Footer note ─────────────────────────────── */
        .footer-note {
            margin-top: 18px; font-size: 7.5pt; font-style: italic;
            color: #6b7280; text-align: center; padding: 6px 12px;
            border: 1px dashed #d1d5db; background: #f9fafb;
        }

        /* ── Page footer ─────────────────────────────── */
        .page-footer {
            border-top: 1px solid #e5e7eb; padding: 5px 0 0 0;
            margin-top: 20px;
            font-size: 7.5pt; color: #9ca3af; text-align: center;
        }
    </style>
</head>
<body>
<div class="page-wrap">

@php
    $refNumber = $sendout->supplier_reference
        ?? ('REP-' . str_pad($sendout->id, 4, '0', STR_PAD_LEFT) . '-' . \Carbon\Carbon::parse($sendout->sent_at)->format('Ymd'));
@endphp

{{-- ── Header ──────────────────────────────────────────── --}}
<table class="hdr">
    <tr>
        <td style="width:120px;">
            <img src="{{ public_path('images/acep_madagascar_logo-1.png') }}" style="width:95px; height:auto;">
        </td>
        <td class="hdr-right">
            <div class="doc-label">Réf. document</div>
            <div class="doc-ref">{{ $refNumber }}</div>
        </td>
    </tr>
</table>

{{-- ── Title band ───────────────────────────────────────── --}}
<div class="title-band">
    {{ $isReturn ? 'BORDEREAU DE RETOUR DE RÉPARATION' : 'BORDEREAU D\'ENVOI EN RÉPARATION' }}
</div>

{{-- ── Date de génération ───────────────────────────────── --}}
<div class="meta">
    Généré le {{ now()->locale('fr')->isoFormat('dddd D MMMM YYYY à HH:mm') }}
</div>

{{-- ── Informations générales ───────────────────────────── --}}
<div class="sec-hdr">Informations générales</div>
<table class="info-tbl">
    <tr>
        <td class="lbl">Le Décisionnaire</td>
        <td class="val">Direction Système D'Information</td>
    </tr>
    <tr>
        <td class="lbl">{{ $isReturn ? 'Réceptionné par' : 'Envoyé par' }}</td>
        <td class="val">
            <strong>{{ $creator?->full_name ?? '—' }}</strong>
            @if($creator?->fonction)
                &nbsp;<span style="color:#6b7280; font-size:9pt;">({{ $creator->fonction }})</span>
            @endif
        </td>
    </tr>
    <tr>
        <td class="lbl">Fournisseur / Réparateur</td>
        <td class="val"><strong>{{ $sendout->supplier_name }}</strong></td>
    </tr>
    <tr>
        <td class="lbl">Référence / N° Bon</td>
        <td class="val">{{ $sendout->supplier_reference ?? '—' }}</td>
    </tr>
    <tr>
        <td class="lbl">Date d'envoi</td>
        <td class="val">{{ $sendout->sent_at ? \Carbon\Carbon::parse($sendout->sent_at)->locale('fr')->isoFormat('dddd D MMMM YYYY') : '—' }}</td>
    </tr>
    @if($isReturn)
    <tr>
        <td class="lbl">Date de retour effective</td>
        <td class="val" style="color:#065f46; font-weight:bold;">
            {{ $sendout->returned_at ? \Carbon\Carbon::parse($sendout->returned_at)->locale('fr')->isoFormat('dddd D MMMM YYYY') : '—' }}
        </td>
    </tr>
    @else
    <tr>
        <td class="lbl">Date de retour prévue</td>
        <td class="val">{{ $sendout->expected_return_at ? \Carbon\Carbon::parse($sendout->expected_return_at)->locale('fr')->isoFormat('dddd D MMMM YYYY') : '—' }}</td>
    </tr>
    @endif
    <tr>
        <td class="lbl">Lieu de départ</td>
        <td class="val">{{ $isReturn ? $sendout->supplier_name : 'ACEP FARAVOHITRA' }}</td>
    </tr>
    <tr>
        <td class="lbl">Lieu de destination</td>
        <td class="val">{{ $isReturn ? 'ACEP FARAVOHITRA' : $sendout->supplier_name }}</td>
    </tr>
</table>

{{-- ── Équipement ────────────────────────────────────────── --}}
<div class="sec-hdr">Équipement concerné</div>
<table class="eq-tbl">
    <thead>
        <tr>
            <th style="width:5%; text-align:center;">Qté</th>
            <th style="width:14%;">Tag actif</th>
            <th style="width:13%;">Type</th>
            <th style="width:22%;">Marque / Modèle</th>
            <th style="width:24%;">N° de Série</th>
            <th style="width:11%;">État</th>
            <th style="width:11%; text-align:center;">Statut</th>
        </tr>
    </thead>
    <tbody>
        <tr>
            <td style="text-align:center; font-weight:bold;">1</td>
            <td><strong>{{ $equipment->asset_tag ?? '—' }}</strong></td>
            <td>{{ $equipment->equipmentType?->name ?? '—' }}</td>
            <td>{{ trim(($equipment->brand ?? '') . ' ' . ($equipment->model ?? '')) ?: '—' }}</td>
            <td style="font-family:monospace; font-size:8.5pt;">{{ $equipment->serial_number ?? '—' }}</td>
            <td>{{ $equipment->condition_label }}</td>
            <td style="text-align:center;">
                @if($isReturn)
                    <span class="b-return">Retourné</span>
                @else
                    <span class="b-repair">En réparation</span>
                @endif
            </td>
        </tr>
    </tbody>
</table>

{{-- ── Observations ──────────────────────────────────────── --}}
@if($sendout->notes)
<div class="sec-hdr">Observations</div>
<div class="notes-box">{{ $sendout->notes }}</div>
@endif

{{-- ── Signatures ────────────────────────────────────────── --}}
<table class="sign-tbl">
    <tr>
        <td>
            <div class="sign-name">Le Remettant</div>
            <div class="sign-area"></div>
            <div class="sign-line">Nom &amp; Signature</div>
        </td>
        <td>
            <div class="sign-name">Le Transportant (*)</div>
            <div class="sign-area"></div>
            <div class="sign-line">Nom &amp; Signature</div>
        </td>
        <td>
            <div class="sign-name">Le Réceptionnaire (*)</div>
            <div class="sign-area"></div>
            <div class="sign-line">Nom &amp; Signature</div>
        </td>
    </tr>
</table>

<div class="footer-note">
    (*) : Appose les articles reçus conformes le ________ et signe. — Ce document atteste l'{{ $isReturn ? 'retour' : 'envoi' }} de l'équipement listé ci-dessus.
</div>

<div class="page-footer">
    Document interne — Parc Informatique ACEP &nbsp;·&nbsp; Confidentiel
</div>

</div>{{-- /.page-wrap --}}
</body>
</html>
