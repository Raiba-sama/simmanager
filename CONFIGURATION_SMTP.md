# Configuration SMTP pour l'envoi d'emails

## Configuration dans le fichier `.env`

Pour configurer l'envoi d'emails via SMTP, ajoutez/modifiez les variables suivantes dans votre fichier `.env` :

```env
# Type de mailer (smtp pour POP/SMTP)
MAIL_MAILER=smtp

# Configuration SMTP
MAIL_HOST=votre-serveur-smtp.com
MAIL_PORT=587
MAIL_USERNAME=votre-email@domaine.com
MAIL_PASSWORD=votre-mot-de-passe
MAIL_ENCRYPTION=tls

# Adresse d'expéditeur
MAIL_FROM_ADDRESS=noreply@votre-domaine.com
MAIL_FROM_NAME="ACEP Madagascar - SIM Manager"
```

## Exemples de configuration selon le fournisseur

### Gmail
```env
MAIL_MAILER=smtp
MAIL_HOST=smtp.gmail.com
MAIL_PORT=587
MAIL_USERNAME=votre-email@gmail.com
MAIL_PASSWORD=votre-mot-de-passe-app
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS=votre-email@gmail.com
MAIL_FROM_NAME="ACEP Madagascar"
```

**Note pour Gmail** : Vous devez utiliser un "Mot de passe d'application" et non votre mot de passe habituel. Activez l'authentification à deux facteurs et créez un mot de passe d'application dans les paramètres de votre compte Google.

### Outlook/Office 365
```env
MAIL_MAILER=smtp
MAIL_HOST=smtp.office365.com
MAIL_PORT=587
MAIL_USERNAME=votre-email@domaine.com
MAIL_PASSWORD=votre-mot-de-passe
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS=votre-email@domaine.com
MAIL_FROM_NAME="ACEP Madagascar"
```

### Serveur SMTP personnalisé (ex: serveur d'entreprise)
```env
MAIL_MAILER=smtp
MAIL_HOST=smtp.votre-entreprise.com
MAIL_PORT=587
MAIL_USERNAME=votre-email@votre-entreprise.com
MAIL_PASSWORD=votre-mot-de-passe
MAIL_ENCRYPTION=tls
# ou 'ssl' pour le port 465
MAIL_FROM_ADDRESS=noreply@votre-entreprise.com
MAIL_FROM_NAME="ACEP Madagascar - SIM Manager"
```

### Ports courants
- **Port 587** : TLS (recommandé)
- **Port 465** : SSL
- **Port 25** : Non chiffré (déconseillé, souvent bloqué)

## Vérification de la configuration

Après avoir modifié le fichier `.env`, exécutez :

```bash
php artisan config:clear
php artisan cache:clear
```

## Test d'envoi d'email

Pour tester la configuration SMTP, vous pouvez utiliser la fonctionnalité d'envoi de bordereaux dans Filament :

1. Allez dans **Bordereaux de transmission** (`/admin/transmission-sheets`)
2. Sélectionnez un ou plusieurs bordereaux
3. Cliquez sur **Bulk actions** > **Envoyer par email**
4. Entrez l'adresse email de destination
5. Cliquez sur **Envoyer**

## Dépannage

### Erreur "Connection timeout"
- Vérifiez que le serveur SMTP est accessible depuis votre serveur
- Vérifiez le port (587 pour TLS, 465 pour SSL)
- Vérifiez les paramètres de pare-feu

### Erreur "Authentication failed"
- Vérifiez le nom d'utilisateur et le mot de passe
- Pour Gmail, utilisez un mot de passe d'application
- Vérifiez que l'authentification est activée sur le serveur SMTP

### Erreur "Could not instantiate mail driver"
- Vérifiez que `MAIL_MAILER=smtp` dans le `.env`
- Exécutez `php artisan config:clear`

### Erreur SSL "certificate verify failed"
Cette erreur se produit lorsque le certificat SSL du serveur SMTP ne peut pas être vérifié. Solutions :

**Option 1 : Désactiver la vérification SSL (développement uniquement)**
Ajoutez dans votre `.env` :
```env
MAIL_VERIFY_PEER=false
MAIL_VERIFY_PEER_NAME=false
MAIL_ALLOW_SELF_SIGNED=true
```

**Option 2 : Utiliser SSL au lieu de TLS**
Si votre serveur utilise SSL (port 465), changez :
```env
MAIL_PORT=465
MAIL_ENCRYPTION=ssl
```

**Option 3 : Vérifier le certificat**
- Vérifiez que le certificat du serveur SMTP est valide
- Vérifiez que la date système est correcte
- Contactez votre administrateur réseau pour vérifier le certificat

**⚠️ Attention** : Désactiver la vérification SSL réduit la sécurité. Utilisez uniquement en développement ou si vous êtes sûr de la sécurité de votre connexion.

### Les emails ne sont pas reçus
- Vérifiez le dossier spam/courrier indésirable
- Vérifiez les logs Laravel : `storage/logs/laravel.log`
- Vérifiez que `MAIL_FROM_ADDRESS` est valide

## Logs

Les erreurs d'envoi d'email sont enregistrées dans :
- `storage/logs/laravel.log`

Recherchez les entrées avec le tag `Erreur envoi email bordereaux` pour voir les détails des erreurs SMTP.

## Configuration avancée

Pour une configuration plus avancée (timeout, authentification personnalisée, etc.), modifiez le fichier `config/mail.php`.

