<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Demande approuvée</title>
</head>
<body style="font-family: Arial, sans-serif; line-height: 1.6; color: #333;">
    <div style="max-width: 600px; margin: 0 auto; padding: 20px;">
        <h2 style="color: #198754;">Demande approuvée</h2>
        
        <p>Bonjour {{ $request->user->name }},</p>
        
        <p>Votre demande de SIM <strong>{{ $request->request_number }}</strong> a été approuvée.</p>
        
        <div style="background: #f8f9fa; padding: 15px; border-radius: 5px; margin: 20px 0;">
            <p><strong>N° Demande:</strong> {{ $request->request_number }}</p>
            <p><strong>Type:</strong> {{ ucfirst($request->request_type) }}</p>
            @if($request->validator)
                <p><strong>Validée par:</strong> {{ $validator->full_name }}</p>
            @endif
            <p><strong>Date:</strong> {{ $request->validated_at->format('d/m/Y H:i') }}</p>
        </div>
        
        <p>
            <a href="{{ url('/sim-requests/' . $request->id) }}" 
               style="background: #198754; color: white; padding: 10px 20px; text-decoration: none; border-radius: 5px; display: inline-block;">
                Voir les détails
            </a>
        </p>
        
        <p style="margin-top: 30px; font-size: 12px; color: #666;">
            Ceci est un email automatique, merci de ne pas y répondre.
        </p>
    </div>
</body>
</html>

