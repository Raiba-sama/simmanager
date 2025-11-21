<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Nouvelle demande de SIM</title>
</head>
<body style="font-family: Arial, sans-serif; line-height: 1.6; color: #333;">
    <div style="max-width: 600px; margin: 0 auto; padding: 20px;">
        <h2 style="color: #0d6efd;">Nouvelle demande de SIM</h2>
        
        <p>Bonjour,</p>
        
        <p>Une nouvelle demande de SIM a été créée et nécessite votre validation :</p>
        
        <div style="background: #f8f9fa; padding: 15px; border-radius: 5px; margin: 20px 0;">
            <p><strong>N° Demande:</strong> {{ $request->request_number }}</p>
            <p><strong>Utilisateur:</strong> {{ $user->full_name }} ({{ $user->matricule }})</p>
            <p><strong>Type:</strong> {{ ucfirst($request->request_type) }}</p>
            <p><strong>Priorité:</strong> {{ ucfirst($request->priority) }}</p>
            @if($request->justification)
                <p><strong>Justification:</strong> {{ $request->justification }}</p>
            @endif
        </div>
        
        <p>
            <a href="{{ url('/admin/sim-requests/' . $request->id . '/edit') }}" 
               style="background: #0d6efd; color: white; padding: 10px 20px; text-decoration: none; border-radius: 5px; display: inline-block;">
                Voir la demande
            </a>
        </p>
        
        <p style="margin-top: 30px; font-size: 12px; color: #666;">
            Ceci est un email automatique, merci de ne pas y répondre.
        </p>
    </div>
</body>
</html>

