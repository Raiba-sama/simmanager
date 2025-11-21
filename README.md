# SIM Manager - Application de Gestion de Cartes SIM

Application Laravel complète pour la gestion de cartes SIM avec workflow de validation, audit logs, notifications en temps réel, et interface moderne.

## 📚 Documentation

Pour une documentation technique complète, consultez [DOCUMENTATION_TECHNIQUE.md](DOCUMENTATION_TECHNIQUE.md)

Cette documentation inclut :
- Architecture technique détaillée
- Structure complète de la base de données
- Documentation des modèles et relations
- Guide complet des contrôleurs et routes
- Système de notifications
- Système d'export Excel/PDF
- Guide de développement et déploiement

## 🚀 Installation

### Prérequis
- PHP >= 8.1
- Composer
- MySQL/MariaDB
- Node.js & NPM (pour les assets)

### Étapes d'installation

1. **Installer les dépendances**
```bash
composer install
npm install
```

2. **Installer Filament Admin**
```bash
composer require filament/filament:"^3.0" -W
php artisan filament:install --panels
```

3. **Configurer l'environnement**
```bash
cp .env.example .env
php artisan key:generate
```

Éditer `.env` et configurer :
```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=simmanager
DB_USERNAME=root
DB_PASSWORD=

MAIL_MAILER=smtp
MAIL_HOST=mailpit
MAIL_PORT=1025
MAIL_USERNAME=null
MAIL_PASSWORD=null
MAIL_ENCRYPTION=null
MAIL_FROM_ADDRESS="noreply@simmanager.local"
MAIL_FROM_NAME="${APP_NAME}"

QUEUE_CONNECTION=database
```

4. **Exécuter les migrations**
```bash
php artisan migrate
```

5. **Seeder la base de données**
```bash
php artisan db:seed
```

6. **Configurer l'autoload des helpers**
```bash
composer dump-autoload
```

7. **Installer Breeze (authentification)**
```bash
composer require laravel/breeze --dev
php artisan breeze:install blade
npm install && npm run build
```

8. **Créer un utilisateur admin (optionnel)**
```bash
php artisan tinker
```
```php
\App\Models\User::create([
    'matricule' => 'ADMIN001',
    'name' => 'Admin',
    'email' => 'admin@example.com',
    'password' => bcrypt('password'),
    'role' => 'admin',
    'active' => true,
]);
```

## 📋 Fonctionnalités

### Authentification
- Login par email ou matricule
- Rôles : Admin, Validator, User
- Middleware `role:admin,validator,user`

### Gestion des Utilisateurs
- CRUD complet via Filament
- Filtres par rôle/statut
- Bulk actions (enable/disable)
- Reset password

### Gestion des SIMs
- CRUD complet
- Import CSV en masse
- Attribution/Libération
- Historique des actions
- Statuts : libre, attribue, suspendu

### Workflow de Demandes
- Création de demandes (attribution, suspension, réactivation, retour)
- Génération automatique de numéro de ticket (ddmmyy-01)
- Validation/Rejet par validators
- Notifications email automatiques
- Historique complet

### Activity Logs
- Enregistrement automatique de toutes les actions
- Helper global `logActivity()` et `logModelAction()`
- Filtres et export CSV
- Purge automatique (commande artisan)

### Interface
- Frontend Bootstrap 5 avec Navbar + Sidebar
- Filament Admin Panel
- Dashboard avec statistiques
- Responsive design

## 🔧 Configuration Queue (Recommandé)

Pour les emails en arrière-plan, configurer la queue :

1. **Créer la table jobs**
```bash
php artisan queue:table
php artisan migrate
```

2. **Démarrer le worker**
```bash
php artisan queue:work
```

Ou utiliser Supervisor pour la production.

## 📝 Commandes Artisan

### Purger les logs
```bash
php artisan logs:purge --days=90
```

## 🎨 Structure des fichiers

```
app/
├── Filament/Resources/          # Resources Filament
│   ├── UserResource.php
│   ├── SimResource.php
│   ├── SimRequestResource.php
│   └── ActivityLogResource.php
├── Http/Controllers/
│   ├── SimRequestController.php
│   ├── SimController.php
│   └── UserController.php
├── Mail/                         # Mailables
│   ├── NewRequestNotification.php
│   ├── RequestValidatedNotification.php
│   └── RequestRejectedNotification.php
├── Models/                       # Modèles Eloquent
├── Policies/                     # Policies
├── Services/
│   └── ActivityLogService.php
└── Helpers/
    └── helpers.php               # Helper logActivity

resources/views/
├── layouts/
│   └── app.blade.php            # Layout principal
├── sim-requests/                # Vues demandes
├── sims/                        # Vues SIMs
├── users/                       # Vues utilisateurs
├── emails/                       # Templates emails
└── dashboard.blade.php
```

## 🔐 Sécurité

- CSRF protection activée
- Validation serveur avec `$request->validate()`
- Policies pour autorisations
- Middleware role-based
- Input sanitization
- Bcrypt pour mots de passe

## 📧 Emails

Les emails sont envoyés via queue (recommandé) :
- Nouvelle demande → Validators
- Demande approuvée → Demandeur
- Demande rejetée → Demandeur

## 🗄️ Base de données

Tables principales :
- `users` - Utilisateurs avec rôles
- `sims` - Cartes SIM
- `sim_histories` - Historique SIM
- `sim_requests` - Demandes
- `activity_logs` - Logs d'activité
- `transaction_log_activities` - Logs transactions (optionnel)

## 🚦 Routes principales

- `/dashboard` - Dashboard
- `/sim-requests` - Liste demandes
- `/sim-requests/create` - Créer demande
- `/sims` - Liste SIMs
- `/profile` - Profil utilisateur
- `/admin/*` - Panel Filament

## 👥 Comptes par défaut (Seeder)

- **Admin**: admin@simmanager.local / password
- **Validator**: validator@simmanager.local / password
- **User**: user@simmanager.local / password

## 📚 Utilisation

### Créer une demande
1. Aller sur `/sim-requests/create`
2. Remplir le formulaire
3. Un email est envoyé aux validateurs

### Valider une demande
1. Via Filament Admin → Sim Requests
2. Ou via l'interface frontend
3. Cliquer sur "Approuver" ou "Rejeter"

### Importer des SIMs
1. Via Filament Admin → Sims → Importer CSV
2. Format CSV : `ICCID,Phone,Operator,Plan,Cost`

### Logger une activité
```php
logActivity('custom_action', 'Description', 'table_name', $recordId);
logModelAction($model, 'update', $payload);
```

## 🛠️ Maintenance

### Purger les logs anciens
Ajouter dans `app/Console/Kernel.php` :
```php
protected function schedule(Schedule $schedule)
{
    $schedule->command('logs:purge --days=90')->monthly();
}
```

## 📄 License

MIT

## 🤝 Support

Pour toute question, créer une issue ou contacter l'équipe de développement.
