<?php

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;

class ImportUsersFromUserF extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'users:import-from-users-f 
                            {--dry-run : Afficher ce qui sera importé sans l\'exécuter}
                            {--skip-existing : Ignorer les utilisateurs existants (par matricule ou email)}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Importer les utilisateurs de la table users-f vers users avec mot de passe par défaut';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $dryRun = $this->option('dry-run');
        $skipExisting = $this->option('skip-existing');
        
        $this->info('Début de l\'importation des utilisateurs depuis users-f...');
        
        try {
            // Vérifier si la table users-f existe
            $tableExists = DB::select("SHOW TABLES LIKE 'users-f'");
            if (empty($tableExists)) {
                $this->error('La table users-f n\'existe pas dans la base de données.');
                return 1;
            }
            
            // Récupérer les colonnes de la table users-f
            $columns = DB::select("SHOW COLUMNS FROM `users-f`");
            $columnNames = array_map(function($col) {
                return $col->Field;
            }, $columns);
            
            $this->info('Colonnes trouvées dans users-f: ' . implode(', ', $columnNames));
            
            // Récupérer toutes les données de users-f
            $usersFromF = DB::table('users-f')->get();
            
            if ($usersFromF->isEmpty()) {
                $this->warn('Aucune donnée trouvée dans la table users-f.');
                return 0;
            }
            
            $this->info("Nombre d'utilisateurs à importer: " . $usersFromF->count());
            
            $imported = 0;
            $skipped = 0;
            $errors = 0;
            
            $defaultPassword = Hash::make('@Zerty123');
            
            foreach ($usersFromF as $userF) {
                try {
                    // Mapper les colonnes (ajuster selon la structure réelle de user-f)
                    $userData = $this->mapUserData($userF, $columnNames);
                    
                    // Vérifier si l'utilisateur existe déjà
                    if ($skipExisting) {
                        $exists = User::where('matricule', $userData['matricule'])
                            ->orWhere('email', $userData['email'])
                            ->exists();
                        
                        if ($exists) {
                            $skipped++;
                            if ($dryRun) {
                                $this->warn("  [SKIP] Utilisateur existant: {$userData['matricule']} ({$userData['email']})");
                            }
                            continue;
                        }
                    }
                    
                    // Ajouter le mot de passe par défaut
                    $userData['password'] = $defaultPassword;
                    
                    // Valeurs par défaut
                    $userData['role'] = $userData['role'] ?? 'user';
                    $userData['active'] = $userData['active'] ?? true;
                    
                    if ($dryRun) {
                        $this->line("  [DRY-RUN] Import: {$userData['matricule']} - {$userData['name']} ({$userData['email']})");
                        $imported++;
                    } else {
                        // Créer l'utilisateur
                        User::create($userData);
                        $imported++;
                        $this->info("  ✓ Importé: {$userData['matricule']} - {$userData['name']}");
                    }
                } catch (\Exception $e) {
                    $errors++;
                    $matricule = $userData['matricule'] ?? 'unknown';
                    $this->error("  ✗ Erreur pour {$matricule}: " . $e->getMessage());
                    Log::error('Import user error', [
                        'matricule' => $matricule,
                        'error' => $e->getMessage(),
                        'trace' => $e->getTraceAsString()
                    ]);
                }
            }
            
            $this->newLine();
            $this->info("Résumé de l'importation:");
            $this->info("  - Importés: {$imported}");
            $this->info("  - Ignorés: {$skipped}");
            $this->info("  - Erreurs: {$errors}");
            
            if ($dryRun) {
                $this->warn("\nMode dry-run activé. Aucune donnée n'a été importée.");
                $this->info("Exécutez la commande sans --dry-run pour effectuer l'importation.");
            }
            
            return 0;
        } catch (\Exception $e) {
            $this->error('Erreur lors de l\'importation: ' . $e->getMessage());
            Log::error('Import users error', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return 1;
        }
    }
    
    /**
     * Mapper les données de users-f vers users
     * Ajustez cette méthode selon la structure réelle de votre table users-f
     */
    private function mapUserData($userF, $columnNames)
    {
        // Fonction helper pour obtenir une valeur avec différents noms possibles
        $getValue = function($possibleNames, $default = null) use ($userF, $columnNames) {
            foreach ($possibleNames as $name) {
                if (in_array($name, $columnNames) && isset($userF->$name)) {
                    return $userF->$name;
                }
            }
            return $default;
        };
        
        // Mapper les colonnes (ajustez selon votre structure)
        $userData = [
            'matricule' => $getValue(['matricule', 'MATRICULE', 'Matricule', 'id', 'ID'], ''),
            'name' => $getValue(['name', 'NAME', 'Name', 'nom', 'NOM', 'Nom', 'last_name', 'lastname'], ''),
            'first_name' => $getValue(['first_name', 'FIRST_NAME', 'FirstName', 'prenom', 'PRENOM', 'Prenom', 'firstname'], null),
            'fonction' => $getValue(['fonction', 'FONCTION', 'Fonction', 'poste', 'POSTE', 'Poste', 'job', 'JOB'], null),
            'email' => $getValue(['email', 'EMAIL', 'Email', 'mail', 'MAIL', 'Mail', 'e_mail'], ''),
            'lieu_affectation' => $getValue(['lieu_affectation', 'LIEU_AFFECTATION', 'lieu', 'LIEU', 'Lieu'], null),
            'zone_affectation' => $getValue(['zone_affectation', 'ZONE_AFFECTATION', 'zone', 'ZONE', 'Zone'], null),
            'direction' => $getValue(['direction', 'DIRECTION', 'Direction', 'dir', 'DIR'], null),
            'numero_flotte' => $getValue(['numero_flotte', 'NUMERO_FLOTTE', 'numero_flotte', 'flotte', 'FLOTTE', 'phone', 'PHONE'], null),
            'role' => $getValue(['role', 'ROLE', 'Role', 'type', 'TYPE'], 'user'),
        ];
        
        // Nettoyer et valider les données
        // Matricule et email sont obligatoires
        if (empty($userData['matricule'])) {
            throw new \Exception('Matricule manquant');
        }
        
        if (empty($userData['email'])) {
            // Générer un email par défaut si manquant
            $userData['email'] = strtolower($userData['matricule']) . '@acepmg.mg';
        }
        
        // Nettoyer l'email
        $userData['email'] = strtolower(trim($userData['email']));
        
        // Valider le format de l'email
        if (!filter_var($userData['email'], FILTER_VALIDATE_EMAIL)) {
            $userData['email'] = strtolower($userData['matricule']) . '@acepmg.mg';
        }
        
        // Nettoyer le nom
        if (empty($userData['name'])) {
            $userData['name'] = 'Utilisateur ' . $userData['matricule'];
        }
        
        // Normaliser le rôle
        $role = strtolower($userData['role']);
        if (!in_array($role, ['admin', 'validator', 'user'])) {
            $userData['role'] = 'user';
        } else {
            $userData['role'] = $role;
        }
        
        return $userData;
    }
}
