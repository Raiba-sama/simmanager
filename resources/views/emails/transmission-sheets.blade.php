<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Bordereaux de transmission</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            line-height: 1.6;
            color: #333;
            max-width: 600px;
            margin: 0 auto;
            padding: 20px;
        }
        .header {
            background-color: #00574A;
            color: white;
            padding: 20px;
            text-align: center;
            border-radius: 5px 5px 0 0;
        }
        .content {
            background-color: #f9f9f9;
            padding: 20px;
            border: 1px solid #ddd;
            border-top: none;
            border-radius: 0 0 5px 5px;
        }
        .bordereau-list {
            margin: 20px 0;
        }
        .bordereau-item {
            background-color: white;
            padding: 15px;
            margin: 10px 0;
            border-left: 4px solid #00574A;
            border-radius: 4px;
        }
        .bordereau-item strong {
            color: #00574A;
        }
        .footer {
            margin-top: 20px;
            padding-top: 20px;
            border-top: 1px solid #ddd;
            font-size: 12px;
            color: #666;
            text-align: center;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>Bordereaux de transmission d'équipements</h1>
    </div>
    
    <div class="content">
        <p>Bonjour,</p>
        
        <p>Vous trouverez ci-joint {{ $count }} bordereau(x) de transmission d'équipement(s) :</p>
        
        <div class="bordereau-list">
            @foreach($transmissionSheets as $sheet)
            <div class="bordereau-item">
                <strong>N° Bordereau :</strong> {{ $sheet->sheet_number }}<br>
                <strong>Type :</strong> {{ match($sheet->type) {
                    'assignment' => 'Attribution',
                    'return' => 'Retour',
                    'transfer' => 'Transfert',
                    default => $sheet->type,
                } }}<br>
                <strong>Date :</strong> {{ $sheet->transmission_date?->format('d/m/Y') ?? '-' }}<br>
                @if($sheet->toUser)
                <strong>Destinataire :</strong> {{ $sheet->toUser->name }} ({{ $sheet->toUser->email }})<br>
                @elseif($sheet->toAgency)
                <strong>Destinataire :</strong> {{ $sheet->toAgency->name }}<br>
                @endif
            </div>
            @endforeach
        </div>
        
        @if(isset($customMessage) && $customMessage)
        <div style="margin-top: 20px; padding: 15px; background-color: #fff3cd; border-left: 4px solid #ffc107; border-radius: 4px;">
            <strong>Message :</strong><br>
            {!! nl2br(e($customMessage)) !!}
        </div>
        @endif
        
        <p style="margin-top: 20px;">Cordialement,<br>
        <strong>Équipe DSI - ACEP</strong></p>
    </div>
    
    <div class="footer">
        <p>© {{ date('Y') }} ACEP Madagascar - Développé par Joachim et l'équipe DSI</p>
        <p>Cet email a été envoyé automatiquement, merci de ne pas y répondre.</p>
    </div>
</body>
</html>

