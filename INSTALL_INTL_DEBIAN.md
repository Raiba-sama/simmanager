# Installation et activation de l'extension PHP intl sur Debian

## Méthode 1 : Installation via apt (Recommandée)

### Étape 1 : Installer le package php-intl
```bash
sudo apt-get update
sudo apt-get install php-intl
```

### Étape 2 : Vérifier la version PHP installée
```bash
php -v
```

### Étape 3 : Si vous utilisez PHP-FPM, redémarrer le service
```bash
# Pour PHP 8.1
sudo systemctl restart php8.1-fpm

# Pour PHP 8.2
sudo systemctl restart php8.2-fpm

# Pour PHP 8.3
sudo systemctl restart php8.3-fpm
```

### Étape 4 : Si vous utilisez Apache, redémarrer Apache
```bash
sudo systemctl restart apache2
```

### Étape 5 : Vérifier que l'extension est chargée
```bash
php -m | grep intl
```

Si vous voyez `intl` dans la liste, l'extension est activée.

## Méthode 2 : Activation manuelle (si le package est déjà installé)

### Étape 1 : Trouver le fichier php.ini
```bash
php --ini
```

Cela affichera le chemin du fichier de configuration PHP.

### Étape 2 : Vérifier si l'extension existe
```bash
ls /usr/lib/php/*/intl.so
```

### Étape 3 : Activer l'extension dans php.ini
Ouvrez le fichier php.ini :
```bash
sudo nano /etc/php/8.1/fpm/php.ini  # Remplacer 8.1 par votre version
```

Cherchez la ligne :
```ini
;extension=intl
```

Et décommentez-la (enlevez le point-virgule) :
```ini
extension=intl
```

### Étape 4 : Redémarrer les services
```bash
sudo systemctl restart php8.1-fpm
sudo systemctl restart apache2  # ou nginx
```

## Méthode 3 : Créer un fichier de configuration séparé (Recommandée pour Debian)

### Étape 1 : Créer le fichier de configuration
```bash
sudo nano /etc/php/8.1/fpm/conf.d/20-intl.ini
```

### Étape 2 : Ajouter cette ligne dans le fichier
```ini
extension=intl
```

### Étape 3 : Sauvegarder et redémarrer
```bash
sudo systemctl restart php8.1-fpm
sudo systemctl restart apache2  # ou nginx
```

## Vérification finale

### Vérifier que intl est chargé
```bash
php -m | grep intl
```

### Vérifier les fonctions disponibles
```bash
php -r "var_dump(class_exists('NumberFormatter'));"
```

Cela doit afficher `bool(true)`.

### Vérifier via phpinfo()
Créez un fichier temporaire :
```bash
echo "<?php phpinfo(); ?>" | sudo tee /var/www/html/phpinfo.php
```

Puis visitez `http://votre-serveur/phpinfo.php` et cherchez "intl".

**⚠️ Important :** Supprimez le fichier phpinfo.php après vérification pour des raisons de sécurité :
```bash
sudo rm /var/www/html/phpinfo.php
```

## Si l'extension n'est toujours pas chargée

### Vérifier les logs d'erreur
```bash
sudo tail -f /var/log/php8.1-fpm.log
# ou
sudo tail -f /var/log/apache2/error.log
```

### Vérifier les dépendances
```bash
sudo apt-get install libicu-dev
```

Puis réinstallez php-intl :
```bash
sudo apt-get install --reinstall php-intl
```

## Commandes rapides (copier-coller)

```bash
# Installation complète
sudo apt-get update && sudo apt-get install -y php-intl && sudo systemctl restart php8.1-fpm && sudo systemctl restart apache2 && php -m | grep intl
```

Remplacez `php8.1-fpm` par votre version PHP si nécessaire.

