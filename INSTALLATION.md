# Guide d'Installation Détaillé

## Étape 1 : Installation des dépendances

```bash
composer install
npm install
```

## Étape 2 : Installation de Filament

```bash
composer require filament/filament:"^3.0" -W
php artisan filament:install --panels
```

Lors de l'installation, Filament vous demandera :
- Créer un utilisateur admin (vous pouvez dire non, on le fera via seeder)
- Choisir le panel (admin)

## Étape 3 : Installation de Breeze (Authentification)

```bash
composer require laravel/breeze --dev
php artisan breeze:install blade
npm install && npm run build
```

## Étape 4 : Configuration de la base de données

1. Créer une base de données MySQL nommée `simmanager`
2. Configurer `.env` :
```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=simmanager
DB_USERNAME=root
DB_PASSWORD=
```

## Étape 5 : Migrations et Seeders

```bash
php artisan migrate
php artisan db:seed
composer dump-autoload
```

## Étape 6 : Configuration Email (Optionnel mais recommandé)

Pour les notifications email, configurer dans `.env` :
```env
MAIL_MAILER=smtp
MAIL_HOST=smtp.mailtrap.io
MAIL_PORT=2525
MAIL_USERNAME=your_username
MAIL_PASSWORD=your_password
MAIL_FROM_ADDRESS="noreply@simmanager.local"
MAIL_FROM_NAME="SIM Manager"
```

## Étape 7 : Configuration Queue (Recommandé)

1. Créer la table jobs :
```bash
php artisan queue:table
php artisan migrate
```

2. Configurer `.env` :
```env
QUEUE_CONNECTION=database
```

3. Démarrer le worker (en développement) :
```bash
php artisan queue:work
```

## Étape 8 : Accès à l'application

### Frontend
- URL : `http://localhost:8000`
- Login : `admin@simmanager.local` / `password`

### Filament Admin
- URL : `http://localhost:8000/admin`
- Login : `admin@simmanager.local` / `password`

## Comptes par défaut

Après le seeder, vous avez 3 comptes :

1. **Admin**
   - Email: `admin@simmanager.local`
   - Matricule: `ADMIN001`
   - Password: `password`
   - Rôle: Admin

2. **Validator**
   - Email: `validator@simmanager.local`
   - Matricule: `VAL001`
   - Password: `password`
   - Rôle: Validator

3. **User**
   - Email: `user@simmanager.local`
   - Matricule: `USER001`
   - Password: `password`
   - Rôle: User

## Personnalisation de l'authentification (Login par matricule)

Si vous voulez permettre le login par matricule en plus de l'email, modifier `app/Http/Controllers/Auth/AuthenticatedSessionController.php` (Breeze) :

```php
public function authenticate(Request $request)
{
    $credentials = $request->validate([
        'login' => ['required', 'string'],
        'password' => ['required'],
    ]);

    // Permettre login par email ou matricule
    $field = filter_var($credentials['login'], FILTER_VALIDATE_EMAIL) ? 'email' : 'matricule';
    
    if (Auth::attempt([$field => $credentials['login'], 'password' => $credentials['password']], $request->boolean('remember'))) {
        $request->session()->regenerate();
        return redirect()->intended(RouteServiceProvider::HOME);
    }

    throw ValidationException::withMessages([
        'login' => __('auth.failed'),
    ]);
}
```

Et modifier la vue de login pour changer le label :
```blade
<input type="text" name="login" ... placeholder="Email ou Matricule">
```

## Vérification

1. Vérifier que les routes fonctionnent :
```bash
php artisan route:list
```

2. Vérifier les migrations :
```bash
php artisan migrate:status
```

3. Tester l'application :
- Se connecter avec un compte
- Créer une demande
- Vérifier les logs dans Filament Admin

## Problèmes courants

### Erreur "Class 'App\Helpers\helpers' not found"
```bash
composer dump-autoload
```

### Erreur Filament "Panel not found"
Vérifier que Filament est bien installé et que le panel admin existe.

### Erreur "Route [login] not defined"
Installer Breeze ou créer manuellement les routes d'authentification.

### Emails ne partent pas
- Vérifier la configuration MAIL dans `.env`
- Vérifier que le worker queue tourne si vous utilisez les queues
- Tester avec `php artisan tinker` : `Mail::raw('Test', function($m) { $m->to('test@example.com')->subject('Test'); });`

