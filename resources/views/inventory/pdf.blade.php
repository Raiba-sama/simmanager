<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Inventaire des Équipements</title>
    <style>
        @page {
            margin: 15mm;
            size: A4 landscape;
        }
        body {
            font-family: 'DejaVu Sans', sans-serif;
            font-size: 9px;
            color: #212529;
        }
        .header {
            text-align: center;
            margin-bottom: 20px;
            border-bottom: 3px solid #00574A;
            padding-bottom: 15px;
        }
        .header h1 {
            margin: 0;
            color: #00574A;
            font-size: 18px;
        }
        .header .date {
            font-size: 10px;
            color: #666;
            margin-top: 5px;
        }
        .stats {
            display: table;
            width: 100%;
            margin-bottom: 15px;
            border-collapse: collapse;
        }
        .stats-row {
            display: table-row;
        }
        .stats-cell {
            display: table-cell;
            padding: 8px;
            border: 1px solid #ddd;
            background-color: #f9f9f9;
            text-align: center;
            font-size: 9px;
        }
        .stats-label {
            font-weight: bold;
            color: #00574A;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
        }
        th {
            background-color: #00574A;
            color: white;
            padding: 8px 4px;
            text-align: left;
            font-weight: bold;
            font-size: 8px;
            border: 1px solid #004d42;
        }
        td {
            padding: 6px 4px;
            border: 1px solid #ddd;
            font-size: 8px;
        }
        tr:nth-child(even) {
            background-color: #f9f9f9;
        }
        .badge {
            display: inline-block;
            padding: 2px 6px;
            border-radius: 3px;
            font-size: 7px;
            font-weight: bold;
        }
        .badge-success { background-color: #d4edda; color: #155724; }
        .badge-info { background-color: #d1ecf1; color: #0c5460; }
        .badge-warning { background-color: #fff3cd; color: #856404; }
        .badge-danger { background-color: #f8d7da; color: #721c24; }
        .footer {
            margin-top: 20px;
            padding-top: 10px;
            border-top: 1px solid #ddd;
            text-align: center;
            font-size: 8px;
            color: #666;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>INVENTAIRE DES ÉQUIPEMENTS INFORMATIQUES</h1>
        <div class="date">Généré le {{ now()->format('d/m/Y à H:i') }}</div>
    </div>

    @php
        $total = $equipment->count();
        $available = $equipment->where('status', 'available')->count();
        $assigned = $equipment->where('status', 'assigned')->count();
        $maintenance = $equipment->where('status', 'maintenance')->count();
        $totalValue = $equipment->whereNotNull('purchase_price')->sum('purchase_price');
    @endphp

    <div class="stats">
        <div class="stats-row">
            <div class="stats-cell stats-label">Total</div>
            <div class="stats-cell">{{ $total }}</div>
            <div class="stats-cell stats-label">Disponibles</div>
            <div class="stats-cell">{{ $available }}</div>
            <div class="stats-cell stats-label">Attribués</div>
            <div class="stats-cell">{{ $assigned }}</div>
            <div class="stats-cell stats-label">En maintenance</div>
            <div class="stats-cell">{{ $maintenance }}</div>
            <div class="stats-cell stats-label">Valeur totale</div>
            <div class="stats-cell">{{ number_format($totalValue, 0, ',', ' ') }} XOF</div>
        </div>
    </div>

    <table>
        <thead>
            <tr>
                <th>Tag</th>
                <th>Type</th>
                <th>Marque</th>
                <th>Modèle</th>
                <th>N° Série</th>
                <th>Statut</th>
                <th>Condition</th>
                <th>Assigné à</th>
                <th>Prix</th>
                <th>Date création</th>
            </tr>
        </thead>
        <tbody>
            @foreach($equipment as $item)
            @php
                $currentAssignment = $item->assignments->where('returned_at', null)->first();
                $assignedTo = $currentAssignment?->assignedToUser?->full_name ?? $currentAssignment?->assignedToAgency?->name ?? '-';
            @endphp
            <tr>
                <td><strong>{{ $item->asset_tag ?? '-' }}</strong></td>
                <td>{{ $item->equipmentType->name ?? '-' }}</td>
                <td>{{ $item->brand ?? '-' }}</td>
                <td>{{ $item->model ?? '-' }}</td>
                <td>{{ $item->serial_number ?? '-' }}</td>
                <td>
                    <span class="badge badge-{{ match($item->status) {
                        'available' => 'success',
                        'assigned' => 'info',
                        'maintenance' => 'warning',
                        'retired' => 'danger',
                        default => 'info',
                    } }}">
                        {{ match($item->status) {
                            'available' => 'Disponible',
                            'assigned' => 'Attribué',
                            'maintenance' => 'Maintenance',
                            'retired' => 'Retiré',
                            default => $item->status,
                        } }}
                    </span>
                </td>
                <td>{{ match($item->condition) {
                    'new' => 'Neuf',
                    'excellent' => 'Excellent',
                    'good' => 'Bon',
                    'fair' => 'Moyen',
                    'poor' => 'Mauvais',
                    default => $item->condition,
                } }}</td>
                <td>{{ $assignedTo }}</td>
                <td>{{ $item->purchase_price ? number_format($item->purchase_price, 0, ',', ' ') . ' XOF' : '-' }}</td>
                <td>{{ $item->created_at->format('d/m/Y') }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>

    <div class="footer">
        <p>© {{ date('Y') }} ACEP Madagascar - Parc Manager</p>
        <p>Développé par Joachim et l'équipe DSI</p>
    </div>
</body>
</html>

