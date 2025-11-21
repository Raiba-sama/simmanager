# Correction de l'erreur "Class NumberFormatter not found" en production

## Problème
L'erreur `Class "NumberFormatter" not found` se produit car l'extension PHP `intl` n'est pas installée ou activée sur le serveur de production. Filament utilise cette extension pour le formatage des nombres.

## Solution recommandée : Installer l'extension PHP intl

### Sur Ubuntu/Debian
```bash
sudo apt-get update
sudo apt-get install php-intl
sudo systemctl restart php8.1-fpm  # Remplacer 8.1 par votre version PHP
# ou
sudo systemctl restart apache2
```

### Sur CentOS/RHEL
```bash
sudo yum install php-intl
sudo systemctl restart php-fpm
# ou
sudo systemctl restart httpd
```

### Vérifier l'installation
```bash
php -m | grep intl
```

Si `intl` apparaît dans la liste, l'extension est installée.

### Vérifier dans php.ini
Assurez-vous que cette ligne n'est pas commentée dans `php.ini` :
```ini
extension=intl
```

## Solution alternative : Si vous ne pouvez pas installer intl

Si vous ne pouvez pas installer l'extension `intl` sur le serveur, les modifications suivantes ont été apportées au code pour éviter l'utilisation de `NumberFormatter` :

1. Les colonnes `->numeric()` ont été remplacées par `->formatStateUsing()` avec `number_format()`
2. Les formulaires utilisent toujours `->numeric()` mais Filament peut fonctionner sans `intl` pour les formulaires

## Vérification après installation

1. Videz le cache :
```bash
php artisan config:clear
php artisan cache:clear
php artisan view:clear
```

2. Testez l'accès à `/admin/users` dans votre navigateur

## Note importante

L'extension `intl` est recommandée pour Laravel et Filament. Elle est utilisée pour :
- Le formatage des nombres
- Le formatage des dates selon les locales
- La validation des emails internationaux
- D'autres fonctionnalités d'internationalisation

Il est fortement recommandé de l'installer plutôt que d'utiliser des solutions de contournement.

