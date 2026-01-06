# Correction de l'erreur "Maximum execution time exceeded"

## Problème
L'erreur "Maximum execution time of 30 seconds exceeded" se produit lors de l'envoi d'emails avec plusieurs PDFs attachés.

## Solutions appliquées

### 1. Code PHP (déjà fait)
- Ajout de `set_time_limit(300)` dans `TransmissionSheetResource.php` et `TransmissionSheetsEmail.php`
- Cela augmente le temps d'exécution à 5 minutes pour ces opérations spécifiques

### 2. Fichier .htaccess (déjà fait)
- Ajout de directives PHP dans `public/.htaccess` pour augmenter `max_execution_time` et `memory_limit`

### 3. Configuration PHP (à vérifier)

#### Pour Apache (XAMPP)

1. **Trouver le bon fichier php.ini** :
   - Ouvrez `httpd.conf` dans `C:\xampp2\apache\conf\`
   - Cherchez la ligne `PHPIniDir` pour connaître le chemin du `php.ini` utilisé par Apache
   - Généralement : `C:\xampp2\php\php.ini`

2. **Modifier php.ini** :
   ```ini
   max_execution_time = 300
   memory_limit = 256M
   ```

3. **Redémarrer Apache** :
   - Arrêtez Apache dans le panneau de contrôle XAMPP
   - Redémarrez Apache
   - ⚠️ **Important** : Redémarrer Apache est nécessaire pour que les changements prennent effet

#### Vérifier la configuration active

Créez un fichier `phpinfo.php` dans `public/` :
```php
<?php
phpinfo();
```

Accédez à `http://localhost/phpinfo.php` et cherchez :
- `max_execution_time` (doit être 300)
- `memory_limit` (doit être 256M)

**⚠️ Supprimez ce fichier après vérification pour des raisons de sécurité !**

### 4. Alternative : Utiliser les queues (recommandé pour production)

Pour les opérations longues comme l'envoi d'emails avec PDFs, utilisez les queues Laravel :

```bash
# Créer la table jobs
php artisan queue:table
php artisan migrate

# Configurer .env
QUEUE_CONNECTION=database

# Démarrer le worker (en arrière-plan)
php artisan queue:work
```

Puis modifiez le code pour utiliser les queues :
```php
Mail::to($data['recipient_email'])
    ->queue(new TransmissionSheetsEmail(...));
```

## Vérification

1. Vérifiez que `max_execution_time` est bien à 300 dans `phpinfo()`
2. Testez l'envoi d'email avec plusieurs bordereaux
3. Vérifiez les logs : `storage/logs/laravel.log`

## Notes importantes

- Le fichier `php.ini` pour CLI (ligne de commande) est différent de celui pour Apache
- Les modifications dans `php.ini` nécessitent un redémarrage d'Apache
- `set_time_limit()` dans le code PHP peut être désactivé si `max_execution_time` est à 0 dans `php.ini`
- Pour la production, utilisez les queues Laravel pour éviter les timeouts

