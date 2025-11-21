<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Export Demandes SIM</title>
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
        .col-num { width: 8%; }
        .col-type { width: 7%; }
        .col-status { width: 8%; }
        .col-user { width: 10%; }
        .col-email { width: 12%; }
        .col-matricule { width: 7%; }
        .col-iccid { width: 10%; }
        .col-phone { width: 8%; }
        .col-plan { width: 10%; }
        .col-motif { width: 12%; }
        .col-date { width: 8%; }
    </style>
</head>
<body>
    <div class="header">
        <h1>Liste des Demandes SIM</h1>
        <p>Export généré le {{ now()->format('d/m/Y à H:i') }}</p>
    </div>
    
    <table>
        <thead>
            <tr>
                <th class="col-num">N° Demande</th>
                <th class="col-type">Type</th>
                <th class="col-status">Statut</th>
                <th class="col-user">Demandeur</th>
                <th class="col-email">Email</th>
                <th class="col-matricule">Matricule</th>
                <th class="col-iccid">ICCID</th>
                <th class="col-phone">Téléphone</th>
                <th class="col-plan">Plan</th>
                <th class="col-motif">Motif</th>
                <th class="col-date">Date création</th>
            </tr>
        </thead>
        <tbody>
            @forelse($requests as $request)
            <tr>
                <td>{{ $request->request_number }}</td>
                <td>{{ ucfirst($request->request_type) }}</td>
                <td>{{ ucfirst(str_replace('_', ' ', $request->status)) }}</td>
                <td>{{ Str::limit($request->user->full_name ?? 'N/A', 20) }}</td>
                <td>{{ Str::limit($request->user->email ?? 'N/A', 25) }}</td>
                <td>{{ $request->user->matricule ?? 'N/A' }}</td>
                <td>{{ Str::limit($request->sim->iccid ?? ($request->requested_iccid ?? 'N/A'), 15) }}</td>
                <td>{{ Str::limit($request->phone_number ?? ($request->sim->phone_number ?? 'N/A'), 12) }}</td>
                <td>{{ Str::limit($request->plan->formatted_name ?? 'N/A', 20) }}</td>
                <td>{{ Str::limit($request->motif ?? 'N/A', 25) }}</td>
                <td>{{ $request->created_at->format('d/m/Y') }}</td>
            </tr>
            @empty
            <tr>
                <td colspan="11" style="text-align: center;">Aucune demande trouvée</td>
            </tr>
            @endforelse
        </tbody>
    </table>
</body>
</html>

