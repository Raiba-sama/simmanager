<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Export SIMs</title>
    <style>
        @page {
            margin: 10mm;
            size: A4 landscape;
        }
        body {
            font-family: Arial, sans-serif;
            font-size: 7px;
            margin: 0;
            padding: 0;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 5px;
            table-layout: fixed;
        }
        th, td {
            border: 1px solid #ddd;
            padding: 3px;
            text-align: left;
            word-wrap: break-word;
            overflow: hidden;
        }
        th {
            background-color: #00574A;
            color: white;
            font-weight: bold;
            font-size: 7px;
        }
        td {
            font-size: 7px;
        }
        tr:nth-child(even) {
            background-color: #f9fafb;
        }
        .header {
            text-align: center;
            margin-bottom: 10px;
        }
        .header h1 {
            color: #00574A;
            margin: 0;
            font-size: 14px;
        }
        .header p {
            margin: 2px 0;
            color: #666;
            font-size: 8px;
        }
        .col-iccid { width: 12%; }
        .col-phone { width: 10%; }
        .col-operator { width: 10%; }
        .col-plan { width: 10%; }
        .col-cost { width: 10%; }
        .col-status { width: 8%; }
        .col-assigned { width: 12%; }
        .col-matricule { width: 8%; }
        .col-date-assign { width: 10%; }
        .col-date-create { width: 10%; }
    </style>
</head>
<body>
    <div class="header">
        <h1>Liste des SIMs</h1>
        <p>Export généré le {{ now()->format('d/m/Y à H:i') }}</p>
    </div>
    
    <table>
        <thead>
            <tr>
                <th class="col-iccid">ICCID</th>
                <th class="col-phone">Numéro téléphone</th>
                <th class="col-operator">Opérateur</th>
                <th class="col-plan">Type de plan</th>
                <th class="col-cost">Coût mensuel</th>
                <th class="col-status">Statut</th>
                <th class="col-assigned">Assignée à</th>
                <th class="col-matricule">Matricule</th>
                <th class="col-date-assign">Date attribution</th>
                <th class="col-date-create">Date création</th>
            </tr>
        </thead>
        <tbody>
            @forelse($sims as $sim)
            <tr>
                <td>{{ Str::limit($sim->iccid, 18) }}</td>
                <td>{{ Str::limit($sim->phone_number ?? 'N/A', 12) }}</td>
                <td>{{ Str::limit($sim->operator ?? 'N/A', 15) }}</td>
                <td>{{ Str::limit($sim->plan_type ?? 'N/A', 15) }}</td>
                <td>{{ $sim->monthly_cost ? number_format($sim->monthly_cost, 0, ',', ' ') . ' ar' : 'N/A' }}</td>
                <td>{{ ucfirst($sim->status) }}</td>
                <td>{{ Str::limit($sim->assignedUser->full_name ?? 'Non attribuée', 20) }}</td>
                <td>{{ Str::limit($sim->assignedUser->matricule ?? 'N/A', 10) }}</td>
                <td>{{ $sim->assigned_at ? \Carbon\Carbon::parse($sim->assigned_at)->format('d/m/Y') : 'N/A' }}</td>
                <td>{{ $sim->created_at->format('d/m/Y') }}</td>
            </tr>
            @empty
            <tr>
                <td colspan="10" style="text-align: center;">Aucune SIM trouvée</td>
            </tr>
            @endforelse
        </tbody>
    </table>
</body>
</html>

