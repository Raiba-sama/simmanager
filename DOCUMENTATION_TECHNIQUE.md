# Documentation Technique - Gestion de Cartes SIM

## Table des matières

1. [Vue d'ensemble](#vue-densemble)
2. [Architecture technique](#architecture-technique)
3. [Structure du projet](#structure-du-projet)
4. [Base de données](#base-de-données)
5. [Modèles et relations](#modèles-et-relations)
6. [Contrôleurs](#contrôleurs)
7. [Routes](#routes)
8. [Vues et composants](#vues-et-composants)
9. [Fonctionnalités principales](#fonctionnalités-principales)
10. [Système de notifications](#système-de-notifications)
11. [Système d'export](#système-dexport)
12. [Système de recherche](#système-de-recherche)
13. [Configuration](#configuration)
14. [Déploiement](#déploiement)
15. [Guide de développement](#guide-de-développement)

---

## Vue d'ensemble

### Description
Application web de gestion de cartes SIM pour l'entreprise ACEP Madagascar. Le système permet de gérer les demandes de SIM, l'attribution des cartes, le suivi des statuts et l'intégration avec un webhook externe pour le traitement des demandes.

### Technologies utilisées
- **Backend**: Laravel 10.x
- **Frontend**: Blade Templates, Bootstrap 5, JavaScript (Vanilla)
- **Base de données**: MySQL/MariaDB
- **Packages**: 
  - `maatwebsite/excel` (v3.1) - Export Excel/PDF
  - `barryvdh/laravel-dompdf` - Génération PDF
- **Police**: Poppins (Google Fonts)
- **Icônes**: Bootstrap Icons

### Prérequis
- PHP >= 8.1
- Composer
- MySQL/MariaDB
- Node.js (optionnel, pour les assets)

---

## Architecture technique

### Pattern MVC
L'application suit le pattern Model-View-Controller (MVC) de Laravel :

- **Models** (`app/Models/`) : Représentent les entités métier
- **Views** (`resources/views/`) : Templates Blade pour l'affichage
- **Controllers** (`app/Http/Controllers/`) : Gèrent la logique métier et les requêtes HTTP

### Structure des couches

```
┌─────────────────────────────────────┐
│         Routes (web.php)             │
└──────────────┬──────────────────────┘
               │
┌──────────────▼──────────────────────┐
│      Controllers                    │
│  - SimRequestController             │
│  - SimController                   │
│  - DashboardController              │
│  - NotificationController            │
└──────────────┬──────────────────────┘
               │
┌──────────────▼──────────────────────┐
│      Models                         │
│  - SimRequest                       │
│  - Sim                              │
│  - User                             │
│  - SimHistory                       │
└──────────────┬──────────────────────┘
               │
┌──────────────▼──────────────────────┐
│      Database                       │
│  - sim_requests                     │
│  - sims                             │
│  - users                            │
│  - sim_histories                    │
└─────────────────────────────────────┘
```

---

## Structure du projet

```
simmanager/
├── app/
│   ├── Console/
│   │   ├── Commands/
│   │   │   ├── CheckPendingRequests.php
│   │   │   └── TestRequestReminder.php
│   │   └── Kernel.php
│   ├── Exports/
│   │   ├── SimRequestsExport.php
│   │   └── SimsExport.php
│   ├── Http/
│   │   ├── Controllers/
│   │   │   ├── SimRequestController.php
│   │   │   ├── SimController.php
│   │   │   ├── DashboardController.php
│   │   │   ├── NotificationController.php
│   │   │   ├── SearchController.php
│   │   │   └── ProfileController.php
│   │   └── Requests/
│   │       └── ProfileUpdateRequest.php
│   ├── Models/
│   │   ├── SimRequest.php
│   │   ├── Sim.php
│   │   ├── User.php
│   │   ├── SimHistory.php
│   │   └── Plan.php
│   └── Notifications/
│       ├── RequestCreated.php
│       ├── RequestValidated.php
│       ├── RequestRejected.php
│       ├── RequestStatusChanged.php
│       ├── SimAssigned.php
│       └── RequestReminder.php
├── database/
│   └── migrations/
│       ├── create_sim_requests_table.php
│       ├── create_sims_table.php
│       ├── create_users_table.php
│       ├── create_sim_histories_table.php
│       ├── create_notifications_table.php
│       ├── create_favorites_table.php
│       └── ...
├── resources/
│   └── views/
│       ├── layouts/
│       │   └── bootstrap.blade.php
│       ├── sim-requests/
│       │   ├── index.blade.php
│       │   ├── show.blade.php
│       │   ├── create-validator.blade.php
│       │   └── bordereau.blade.php
│       ├── sims/
│       │   ├── index.blade.php
│       │   └── show.blade.php
│       ├── dashboard.blade.php
│       ├── notifications/
│       │   └── all.blade.php
│       └── users/
│           └── profile.blade.php
├── routes/
│   └── web.php
├── public/
│   ├── images/
│   │   └── acep_madagascar_logo-1.png
│   └── css/
│       └── custom.css
└── composer.json
```

---

## Base de données

### Schéma relationnel

```
users
├── id (PK)
├── name
├── email
├── matricule (unique)
├── phone_number
├── role (enum: user, validator, admin)
└── ...

sims
├── id (PK)
├── iccid (unique)
├── phone_number
├── status (enum: libre, attribue, suspendu, defectueuse)
├── operator
├── plan_type
├── monthly_cost
├── assigned_to (FK -> users.id)
├── assigned_to_matricule
└── assigned_at

sim_requests
├── id (PK)
├── request_type (enum: recuperation, creation, suspension, desactivation, ajustement)
├── status (enum: en_attente, validee, rejetee, demande_envoyee, pending, accepted, refused)
├── user_id (FK -> users.id)
├── created_by (FK -> users.id)
├── sim_id (FK -> sims.id, nullable)
├── phone_number
├── requested_iccid
├── plan_id (FK -> plans.id, nullable)
├── motif
├── beneficiary_name
├── beneficiary_matricule
├── beneficiary_fonction
├── rejection_reason
└── ...

sim_histories
├── id (PK)
├── sim_id (FK -> sims.id, nullable)
├── request_id (FK -> sim_requests.id, nullable)
├── action (enum: created, updated, assigned, unassigned, status_changed, validated, rejected, ...)
├── user_id (FK -> users.id)
├── user_matricule
├── old_data (JSON)
├── new_data (JSON)
├── notes
└── created_at

notifications
├── id (UUID)
├── type
├── notifiable_type
├── notifiable_id
├── data (JSON)
├── read_at (nullable)
└── created_at

favorites
├── id (PK)
├── user_id (FK -> users.id)
├── sim_request_id (FK -> sim_requests.id)
└── created_at

plans
├── id (PK)
├── name
├── limite_credit
├── limite_data
├── prix
└── ...
```

### Relations principales

- **User** → **SimRequest** (1:N) : Un utilisateur peut créer plusieurs demandes
- **User** → **Sim** (1:N) : Un utilisateur peut avoir plusieurs SIMs assignées
- **SimRequest** → **Sim** (N:1) : Une demande peut être liée à une SIM
- **SimRequest** → **User** (N:1) : Une demande appartient à un utilisateur
- **SimHistory** → **Sim** (N:1) : Un historique appartient à une SIM (nullable)
- **SimHistory** → **SimRequest** (N:1) : Un historique appartient à une demande (nullable)
- **User** → **SimRequest** (N:N via favorites) : Un utilisateur peut avoir plusieurs favoris

---

## Modèles et relations

### User Model (`app/Models/User.php`)

**Relations** :
```php
public function simRequests() {
    return $this->hasMany(SimRequest::class, 'user_id');
}

public function createdRequests() {
    return $this->hasMany(SimRequest::class, 'created_by');
}

public function assignedSims() {
    return $this->hasMany(Sim::class, 'assigned_to');
}

public function favorites() {
    return $this->belongsToMany(SimRequest::class, 'favorites')
                ->withTimestamps();
}
```

**Méthodes utilitaires** :
- `isAdmin()` : Vérifie si l'utilisateur est administrateur
- `isValidator()` : Vérifie si l'utilisateur est validateur
- `canValidateRequests()` : Vérifie si l'utilisateur peut valider des demandes
- `getAvatarAttribute()` : Retourne l'URL de l'avatar

### SimRequest Model (`app/Models/SimRequest.php`)

**Relations** :
```php
public function user() {
    return $this->belongsTo(User::class, 'user_id');
}

public function creator() {
    return $this->belongsTo(User::class, 'created_by');
}

public function sim() {
    return $this->belongsTo(Sim::class, 'sim_id');
}

public function plan() {
    return $this->belongsTo(Plan::class, 'plan_id');
}

public function histories() {
    return $this->hasMany(SimHistory::class, 'request_id');
}

public function favoritedBy() {
    return $this->belongsToMany(User::class, 'favorites')
                ->withTimestamps();
}
```

**Scopes** :
- `scopeEnAttente()` : Filtre les demandes en attente
- `scopeValidee()` : Filtre les demandes validées
- `scopePendingMoreThanDays(int $days)` : Filtre les demandes en attente depuis plus de X jours

**Méthodes utilitaires** :
- `isRecuperation()` : Vérifie si c'est une demande de récupération
- `isCreation()` : Vérifie si c'est une demande de création
- `isEnAttente()` : Vérifie si le statut est "en_attente"
- `isDemandeEnvoyee()` : Vérifie si le statut est "demande_envoyee"
- `getDaysPendingAttribute()` : Calcule le nombre de jours en attente
- `isOverdue(int $days = 3)` : Vérifie si la demande est en retard
- `getUrgencyLevelAttribute()` : Retourne le niveau d'urgence (low, normal, high, urgent)

### Sim Model (`app/Models/Sim.php`)

**Relations** :
```php
public function assignedUser() {
    return $this->belongsTo(User::class, 'assigned_to');
}

public function requests() {
    return $this->hasMany(SimRequest::class, 'sim_id');
}

public function histories() {
    return $this->hasMany(SimHistory::class, 'sim_id');
}
```

**Scopes** :
- `scopeLibre()` : Filtre les SIMs libres
- `scopeAttribue()` : Filtre les SIMs attribuées
- `scopeSuspendu()` : Filtre les SIMs suspendues
- `scopeDefectueuse()` : Filtre les SIMs défectueuses

**Méthodes utilitaires** :
- `isLibre()` : Vérifie si la SIM est libre
- `isAttribue()` : Vérifie si la SIM est attribuée
- `isDefectueuse()` : Vérifie si la SIM est défectueuse

### SimHistory Model (`app/Models/SimHistory.php`)

**Relations** :
```php
public function sim() {
    return $this->belongsTo(Sim::class, 'sim_id');
}

public function request() {
    return $this->belongsTo(SimRequest::class, 'request_id');
}

public function user() {
    return $this->belongsTo(User::class, 'user_id');
}
```

**Accessors** :
- `getActionLabelAttribute()` : Retourne le libellé traduit de l'action
- `getChangesSummaryAttribute()` : Retourne un résumé des changements

---

## Contrôleurs

### SimRequestController

**Méthodes principales** :

1. **`index(Request $request)`**
   - Liste paginée des demandes avec filtres
   - Support des favoris
   - Calcul des jours en attente

2. **`show(SimRequest $simRequest)`**
   - Affiche les détails d'une demande
   - Gère les permissions (validateur peut voir ses propres demandes)

3. **`create()`**
   - Affiche le formulaire de création

4. **`store(Request $request)`**
   - Crée une nouvelle demande selon le type
   - Envoie une notification aux validateurs

5. **`approve(Request $request, SimRequest $simRequest)`**
   - Valide une demande de récupération
   - Assigne une SIM si disponible
   - Envoie au webhook et notifie l'utilisateur

6. **`reject(Request $request, SimRequest $simRequest)`**
   - Rejette une demande avec raison
   - Notifie l'utilisateur

7. **`cancel(SimRequest $simRequest)`**
   - Permet au demandeur d'annuler sa demande

8. **`destroy(SimRequest $simRequest)`**
   - Supprime une demande (validateur/admin)

9. **`submitToWebhook(SimRequest $simRequest)`**
   - Envoie une demande validée au webhook externe
   - Change le statut à "demande_envoyee"

10. **`quickUpdateStatus(Request $request, SimRequest $simRequest)`**
    - Mise à jour rapide du statut opérateur (admin)

11. **`generateBordereau(SimRequest $simRequest)`**
    - Génère le bordereau de transmission en PDF

12. **`export(Request $request)`**
    - Export Excel/PDF des demandes avec filtres

13. **`bulkApprove(Request $request)`**
    - Validation en masse de demandes de récupération

14. **`bulkReject(Request $request)`**
    - Rejet en masse avec raison

15. **`bulkUpdateStatus(Request $request)`**
    - Mise à jour en masse du statut opérateur

16. **`bulkDelete(Request $request)`**
    - Suppression en masse

17. **`toggleFavorite(Request $request, SimRequest $simRequest)`**
    - Ajoute/retire des favoris

**Méthodes privées** :
- `sendRequestToWebhook(SimRequest $simRequest, ?Sim $sim = null)` : Envoie au webhook avec paramètres dynamiques
- `getFonctionsList()` : Liste des fonctions disponibles
- `createRequestHistory(SimRequest $simRequest, string $action, array $data = [])` : Crée un historique

### SimController

**Méthodes principales** :

1. **`index(Request $request)`**
   - Liste paginée avec filtres dynamiques

2. **`show(Sim $sim)`**
   - Détails d'une SIM avec historique

3. **`assign(Request $request, Sim $sim)`**
   - Assigne une SIM à un utilisateur
   - Notifie l'utilisateur

4. **`bulkAssign(Request $request)`**
   - Attribution en masse

5. **`bulkUnassign(Request $request)`**
   - Libération en masse

6. **`updateStatus(Request $request, Sim $sim)`**
   - Change le statut d'une SIM (libre, attribue, suspendu, defectueuse)

7. **`importCsv(Request $request)`**
   - Import en masse d'ICCID (CSV ou texte)
   - Crée les SIMs avec statut "libre" par défaut

8. **`export(Request $request)`**
   - Export Excel/PDF avec filtres

### DashboardController

**Méthodes principales** :

1. **`index(Request $request)`**
   - Affiche le tableau de bord avec statistiques et graphiques

2. **`getChartDataApi(Request $request)`**
   - API pour récupérer les données de graphiques dynamiquement

3. **`getBasicStats($user, $isValidator)`**
   - Statistiques de base (demandes, SIMs, etc.)

4. **`getChartData($user, $isValidator, $startDate, $endDate, $period)`**
   - Agrège les données pour tous les graphiques

5. **`getRequestsEvolution(...)`**
   - Évolution des demandes dans le temps

6. **`getRequestsByType(...)`**
   - Répartition par type de demande

7. **`getRequestsByStatus(...)`**
   - Répartition par statut

8. **`getSimsByStatus()`**
   - Répartition des SIMs par statut

9. **`getAdvancedStats(...)`**
   - Statistiques avancées (taux de validation, temps moyen, etc.)

### NotificationController

**Méthodes principales** :

1. **`index()`**
   - Liste des notifications non lues (pour le dropdown)

2. **`all(Request $request)`**
   - Page complète des notifications paginées

3. **`unreadCount()`**
   - API pour le nombre de notifications non lues

4. **`markAsRead(Request $request, $id)`**
   - Marque une notification comme lue et redirige

5. **`markAllAsRead()`**
   - Marque toutes les notifications comme lues

### SearchController

**Méthode principale** :

1. **`search(Request $request)`**
   - Recherche globale dans les demandes, SIMs et utilisateurs
   - Support des filtres de type (`req:`, `sim:`, `user:`)
   - Retourne des résultats formatés avec métadonnées

### ProfileController

**Méthodes principales** :

1. **`show()`**
   - Affiche le profil utilisateur

2. **`update(Request $request)`**
   - Met à jour les informations du profil
   - Gère l'upload d'avatar

3. **`updatePassword(Request $request)`**
   - Change le mot de passe

---

## Routes

### Routes principales

```php
// Dashboard
Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
Route::get('/dashboard/chart-data', [DashboardController::class, 'getChartDataApi'])->name('dashboard.chart-data');

// Demandes de SIM
Route::resource('sim-requests', SimRequestController::class);
Route::post('sim-requests/{simRequest}/approve', [SimRequestController::class, 'approve'])->name('sim-requests.approve');
Route::post('sim-requests/{simRequest}/reject', [SimRequestController::class, 'reject'])->name('sim-requests.reject');
Route::delete('sim-requests/{simRequest}/cancel', [SimRequestController::class, 'cancel'])->name('sim-requests.cancel');
Route::post('sim-requests/{simRequest}/submit-webhook', [SimRequestController::class, 'submitToWebhook'])->name('sim-requests.submit-webhook');
Route::post('sim-requests/{simRequest}/quick-update-status', [SimRequestController::class, 'quickUpdateStatus'])->name('sim-requests.quick-update-status');
Route::get('sim-requests/{simRequest}/bordereau', [SimRequestController::class, 'generateBordereau'])->name('sim-requests.bordereau');
Route::get('sim-requests/export', [SimRequestController::class, 'export'])->name('sim-requests.export');
Route::post('sim-requests/{simRequest}/toggle-favorite', [SimRequestController::class, 'toggleFavorite'])->name('sim-requests.toggle-favorite');

// Actions en masse
Route::post('sim-requests/bulk-approve', [SimRequestController::class, 'bulkApprove'])->name('sim-requests.bulk-approve');
Route::post('sim-requests/bulk-reject', [SimRequestController::class, 'bulkReject'])->name('sim-requests.bulk-reject');
Route::post('sim-requests/bulk-update-status', [SimRequestController::class, 'bulkUpdateStatus'])->name('sim-requests.bulk-update-status');
Route::delete('sim-requests/bulk-delete', [SimRequestController::class, 'bulkDelete'])->name('sim-requests.bulk-delete');

// SIMs
Route::resource('sims', SimController::class);
Route::post('sims/{sim}/assign', [SimController::class, 'assign'])->name('sims.assign');
Route::post('sims/{sim}/update-status', [SimController::class, 'updateStatus'])->name('sims.update-status');
Route::get('sims/export', [SimController::class, 'export'])->name('sims.export');
Route::post('sims/import-csv', [SimController::class, 'importCsv'])->name('sims.import-csv');
Route::post('sims/bulk-assign', [SimController::class, 'bulkAssign'])->name('sims.bulk-assign');
Route::post('sims/bulk-unassign', [SimController::class, 'bulkUnassign'])->name('sims.bulk-unassign');

// Notifications
Route::get('notifications', [NotificationController::class, 'index'])->name('notifications.index');
Route::get('notifications/all', [NotificationController::class, 'all'])->name('notifications.all');
Route::get('notifications/unread-count', [NotificationController::class, 'unreadCount'])->name('notifications.unread-count');
Route::post('notifications/{id}/read', [NotificationController::class, 'markAsRead'])->name('notifications.mark-read');
Route::post('notifications/read-all', [NotificationController::class, 'markAllAsRead'])->name('notifications.mark-all-read');

// Recherche
Route::get('search', [SearchController::class, 'search'])->name('search');

// Profil
Route::get('profile', [ProfileController::class, 'show'])->name('profile.show');
Route::put('profile', [ProfileController::class, 'update'])->name('profile.update');
Route::put('profile/password', [ProfileController::class, 'updatePassword'])->name('profile.password');
```

---

## Vues et composants

### Layout principal (`resources/views/layouts/bootstrap.blade.php`)

**Structure** :
- Sidebar fixe avec navigation
- Navbar supérieure avec recherche globale et notifications
- Zone de contenu principale
- Système de toasts pour les notifications

**Fonctionnalités JavaScript** :
- Recherche globale avec raccourcis clavier (Ctrl/Cmd + K)
- Système de notifications en temps réel
- Tooltips Bootstrap
- Confirmations visuelles pour actions destructives
- Validation en temps réel des formulaires

### Pages principales

1. **Dashboard** (`dashboard.blade.php`)
   - Statistiques en cartes
   - Graphiques Chart.js (évolution, répartition)
   - Filtres par période

2. **Liste des demandes** (`sim-requests/index.blade.php`)
   - Tableau avec filtres dynamiques
   - Actions en masse
   - Pagination AJAX
   - Export Excel/PDF
   - Favoris

3. **Détails d'une demande** (`sim-requests/show.blade.php`)
   - Informations complètes
   - Actions selon le rôle
   - Historique détaillé
   - Bordereau de transmission

4. **Liste des SIMs** (`sims/index.blade.php`)
   - Tableau avec filtres
   - Actions en masse
   - Import en masse
   - Export

5. **Détails d'une SIM** (`sims/show.blade.php`)
   - Informations complètes
   - Historique
   - Changement de statut (admin)

---

## Fonctionnalités principales

### 1. Gestion des demandes

#### Types de demandes
- **Récupération** : Récupération d'une SIM existante
- **Création** : Création d'une nouvelle ligne
- **Suspension** : Suspension d'une ligne
- **Désactivation** : Désactivation d'une ligne
- **Ajustement** : Ajustement de forfait

#### Workflow
1. Utilisateur crée une demande
2. Notification envoyée aux validateurs
3. Validateur approuve/rejette
4. Si approuvée, admin peut envoyer au webhook
5. Statut change à "demande_envoyee"
6. Bordereau de transmission généré

### 2. Gestion des SIMs

#### Statuts
- **libre** : Disponible pour attribution
- **attribue** : Assignée à un utilisateur
- **suspendu** : Suspendue
- **defectueuse** : Défectueuse/non fonctionnelle

#### Fonctionnalités
- Attribution manuelle ou en masse
- Import en masse d'ICCID (CSV ou texte)
- Changement de statut
- Historique complet

### 3. Système de permissions

#### Rôles
- **user** : Utilisateur simple
  - Créer des demandes
  - Voir ses propres demandes
  - Annuler ses demandes

- **validator** : Validateur
  - Toutes les permissions utilisateur
  - Valider/rejeter les demandes de récupération des utilisateurs simples
  - Supprimer des demandes

- **admin** : Administrateur
  - Toutes les permissions
  - Envoyer les demandes au webhook
  - Gérer les SIMs
  - Actions en masse
  - Voir les bordereaux

### 4. Système de favoris

Les utilisateurs peuvent marquer des demandes comme favorites pour un accès rapide.

### 5. Système d'historique

Toutes les actions importantes sont enregistrées dans `sim_histories` :
- Création de SIM
- Attribution/Libération
- Changement de statut
- Validation/Rejet de demande
- Envoi au webhook
- Import en masse

---

## Système de notifications

### Types de notifications

1. **RequestCreated** : Nouvelle demande créée
2. **RequestValidated** : Demande validée
3. **RequestRejected** : Demande rejetée
4. **RequestStatusChanged** : Statut opérateur changé
5. **SimAssigned** : SIM assignée
6. **RequestReminder** : Rappel pour demande en retard (>3 jours)

### Stockage
- Table `notifications` (Laravel standard)
- Format JSON pour les données
- Support des URLs relatives

### Affichage
- Badge dans la navbar avec compteur
- Dropdown avec dernières notifications
- Page complète avec pagination
- Marquer comme lu au clic
- Redirection vers l'élément concerné

### Commandes planifiées

```php
// app/Console/Kernel.php
$schedule->command('requests:check-pending --days=3')
    ->dailyAt('09:00')
    ->timezone('Indian/Antananarivo');
```

Vérifie quotidiennement les demandes en attente depuis plus de 3 jours et envoie des rappels.

---

## Système d'export

### Formats supportés
- **Excel** (.xlsx) via `maatwebsite/excel`
- **PDF** (.pdf) via Blade templates

### Fonctionnalités
- Export avec filtres appliqués
- Export de sélection (IDs spécifiques)
- Colonnes personnalisées selon le contexte

### Classes d'export

1. **SimRequestsExport** (`app/Exports/SimRequestsExport.php`)
   - Implémente `FromCollection`, `WithHeadings`, `WithMapping`
   - Formatage des données pour Excel

2. **SimsExport** (`app/Exports/SimsExport.php`)
   - Même structure pour les SIMs

### Vues PDF
- `resources/views/sim-requests/export-pdf.blade.php`
- `resources/views/sims/export-pdf.blade.php`
- Optimisées pour A4 paysage

---

## Système de recherche

### Recherche globale
- Barre de recherche dans la navbar
- Raccourci clavier : `Ctrl/Cmd + K`
- Recherche en temps réel (debounce 300ms)

### Filtres de type
- `req:` : Recherche uniquement dans les demandes
- `sim:` : Recherche uniquement dans les SIMs
- `user:` : Recherche uniquement dans les utilisateurs

### Champs recherchés

**Demandes** :
- Numéro de demande
- Nom du demandeur
- ICCID demandé
- Numéro de téléphone
- Type de demande

**SIMs** :
- ICCID
- Numéro de téléphone
- Opérateur
- Matricule assigné

**Utilisateurs** :
- Nom
- Email
- Matricule
- Lieu d'affectation
- Direction

### Résultats
- Affichage formaté avec badges de statut
- Compteurs par type
- Liens directs vers les éléments

---

## Configuration

### Variables d'environnement (`.env`)

```env
APP_NAME="Gestion de Cartes SIM"
APP_ENV=production
APP_KEY=
APP_DEBUG=false
APP_URL=https://votre-domaine.com

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=simmanager
DB_USERNAME=root
DB_PASSWORD=

# Webhook externe
WEBHOOK_URL=https://acepmg.it4life.org/webhook/get_infos
```

### Configuration des fichiers

**`config/app.php`** :
- Timezone : `Indian/Antananarivo`
- Locale : `fr`

**`config/excel.php`** :
- Configuration pour `maatwebsite/excel`

---

## Déploiement

### Prérequis serveur
- PHP >= 8.1 avec extensions :
  - PDO
  - Mbstring
  - OpenSSL
  - Tokenizer
  - XML
  - Ctype
  - JSON
- MySQL/MariaDB >= 5.7
- Composer
- Serveur web (Apache/Nginx)

### Étapes de déploiement

1. **Cloner le projet**
```bash
git clone <repository-url>
cd simmanager
```

2. **Installer les dépendances**
```bash
composer install --optimize-autoloader --no-dev
```

3. **Configuration**
```bash
cp .env.example .env
php artisan key:generate
```

4. **Configurer `.env`**
- Base de données
- URL de l'application
- Webhook URL

5. **Migrer la base de données**
```bash
php artisan migrate --force
```

6. **Créer un utilisateur admin**
```bash
php artisan tinker
>>> User::create(['name' => 'Admin', 'email' => 'admin@example.com', 'password' => Hash::make('password'), 'role' => 'admin', 'matricule' => 'ADMIN001']);
```

7. **Optimiser**
```bash
php artisan config:cache
php artisan route:cache
php artisan view:cache
```

8. **Permissions**
```bash
chmod -R 755 storage bootstrap/cache
chown -R www-data:www-data storage bootstrap/cache
```

### Planification des tâches (Cron)

Ajouter dans crontab :
```bash
* * * * * cd /path-to-project && php artisan schedule:run >> /dev/null 2>&1
```

### Sauvegarde

**Base de données** :
```bash
mysqldump -u root -p simmanager > backup_$(date +%Y%m%d).sql
```

**Fichiers** :
- `storage/app/public` : Avatars utilisateurs
- `public/images` : Logo et images

---

## Guide de développement

### Ajouter un nouveau type de demande

1. **Migration** : Ajouter le type dans l'enum `request_type`
2. **Model** : Ajouter une méthode `isNouveauType()` dans `SimRequest`
3. **Controller** : Ajouter la logique dans `store()` et `sendRequestToWebhook()`
4. **Vue** : Ajouter le formulaire dans `create-validator.blade.php`
5. **Routes** : Aucun changement nécessaire (resource route)

### Ajouter une nouvelle notification

1. **Créer la classe** : `app/Notifications/NouvelleNotification.php`
```php
class NouvelleNotification extends Notification
{
    public function via($notifiable)
    {
        return ['database'];
    }

    public function toArray($notifiable)
    {
        return [
            'title' => 'Titre',
            'message' => 'Message',
            'url' => url()->route('route.name', $this->model, false),
            'icon' => 'bi-icon',
            'color' => '#color',
        ];
    }
}
```

2. **Envoyer la notification** :
```php
$user->notify(new NouvelleNotification($model));
```

### Ajouter un nouveau graphique au dashboard

1. **Controller** : Ajouter une méthode dans `DashboardController`
```php
private function getNouveauGraphique($user, $isValidator, $startDate, $endDate)
{
    // Logique d'agrégation
    return [
        'labels' => [...],
        'data' => [...],
    ];
}
```

2. **Appeler dans `getChartData()`** :
```php
$chartData['nouveau_graphique'] = $this->getNouveauGraphique(...);
```

3. **Vue** : Ajouter le canvas et l'initialisation Chart.js

### Tests

**Tests unitaires** :
```bash
php artisan test
```

**Tests de fonctionnalités** :
- Créer une demande de chaque type
- Valider/rejeter une demande
- Assigner une SIM
- Importer des SIMs
- Exporter en Excel/PDF
- Recherche globale
- Notifications

### Débogage

**Logs** :
```bash
tail -f storage/logs/laravel.log
```

**Tinker** :
```bash
php artisan tinker
```

**Debugbar** (si installé) :
- Accessible en mode développement

---

## Sécurité

### Authentification
- Laravel Sanctum (si API)
- Sessions pour web
- Protection CSRF sur tous les formulaires

### Autorisation
- Vérification des rôles dans les contrôleurs
- Middleware personnalisé si nécessaire
- Gates/Policies pour permissions complexes

### Validation
- Validation côté serveur (Request classes)
- Validation côté client (JavaScript)
- Sanitization des entrées

### Protection des données
- Hashage des mots de passe (bcrypt)
- Protection SQL injection (Eloquent ORM)
- Protection XSS (Blade escaping)

---

## Performance

### Optimisations
- Eager loading des relations (`with()`)
- Cache des configurations
- Pagination pour grandes listes
- Index sur colonnes fréquemment recherchées

### Requêtes optimisées
```php
// Bon
SimRequest::with(['user', 'sim', 'plan'])->paginate(20);

// Mauvais (N+1 problem)
SimRequest::paginate(20); // Charge les relations à chaque itération
```

---

## Maintenance

### Tâches régulières
- Vérifier les logs d'erreurs
- Nettoyer les anciennes notifications (optionnel)
- Sauvegarder la base de données
- Mettre à jour les dépendances

### Commandes utiles
```bash
# Nettoyer le cache
php artisan cache:clear
php artisan config:clear
php artisan route:clear
php artisan view:clear

# Optimiser
php artisan optimize

# Vérifier les migrations
php artisan migrate:status
```

---

## Support et contact

Pour toute question ou problème :
- Consulter les logs : `storage/logs/laravel.log`
- Vérifier la documentation Laravel : https://laravel.com/docs
- Contacter l'équipe de développement

---

**Version** : 1.0.0  
**Dernière mise à jour** : 2025-01-XX  
**Auteur** : Équipe de développement ACEP Madagascar

