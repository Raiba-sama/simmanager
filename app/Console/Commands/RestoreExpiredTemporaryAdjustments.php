<?php

namespace App\Console\Commands;

use App\Models\SimRequest;
use App\Models\SimHistory;
use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class RestoreExpiredTemporaryAdjustments extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'adjustments:restore-expired';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Restaure automatiquement les ajustements temporaires expirés aux valeurs précédentes';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Recherche des ajustements temporaires expirés...');

        // Trouver les ajustements temporaires expirés qui ont été livrés
        $expiredAdjustments = SimRequest::where('request_type', 'ajustement')
            ->where('is_temporary', true)
            ->whereNotNull('temporary_end_date')
            ->whereNotNull('delivered_at')
            ->whereDate('temporary_end_date', '<=', now()->toDateString())
            ->get()
            ->filter(function ($adjustment) {
                // Ne pas restaurer si une demande de restauration existe déjà
                $hasRestoration = SimRequest::where('request_type', 'ajustement')
                    ->where('phone_number', $adjustment->phone_number)
                    ->whereJsonContains('request_details->restoration_of', $adjustment->request_number)
                    ->exists();
                return !$hasRestoration;
            });

        if ($expiredAdjustments->isEmpty()) {
            $this->info('Aucun ajustement temporaire expiré à restaurer.');
            return Command::SUCCESS;
        }

        $this->info("Trouvé {$expiredAdjustments->count()} ajustement(s) temporaire(s) expiré(s) à restaurer.");

        $restored = 0;
        $errors = 0;

        foreach ($expiredAdjustments as $adjustment) {
            try {
                DB::beginTransaction();

                // Vérifier que l'ajustement a des valeurs précédentes à restaurer
                if (!$adjustment->previous_limite_credit && !$adjustment->previous_limite_data && !$adjustment->previous_plan_id) {
                    $this->warn("Ajustement {$adjustment->request_number} : aucune valeur précédente à restaurer, ignoré.");
                    DB::rollBack();
                    continue;
                }

                // Récupérer l'utilisateur système ou admin pour créer la demande de restauration
                $systemUser = User::where('role', 'admin')->first() ?? User::first();
                if (!$systemUser) {
                    $this->error('Aucun utilisateur trouvé pour créer la demande de restauration.');
                    DB::rollBack();
                    continue;
                }

                // Créer une nouvelle demande d'ajustement pour restaurer les valeurs précédentes
                $restorationRequest = SimRequest::create([
                    'request_number' => SimRequest::generateRequestNumber(),
                    'user_id' => $adjustment->user_id,
                    'sim_id' => $adjustment->sim_id,
                    'phone_number' => $adjustment->phone_number,
                    'request_type' => 'ajustement',
                    'plan_id' => $adjustment->previous_plan_id,
                    'limite_credit' => $adjustment->previous_limite_credit,
                    'limite_data' => $adjustment->previous_limite_data,
                    'collaborator_matricule' => $adjustment->collaborator_matricule,
                    'collaborator_name' => $adjustment->collaborator_name,
                    'collaborator_first_name' => $adjustment->collaborator_first_name,
                    'collaborator_agence' => $adjustment->collaborator_agence,
                    'status' => 'pending',
                    'created_by' => $systemUser->id,
                    'request_details' => [
                        'restoration_of' => $adjustment->request_number,
                        'restoration_reason' => 'Restauration automatique après expiration de l\'ajustement temporaire',
                        'original_temporary_end_date' => $adjustment->temporary_end_date->format('Y-m-d'),
                    ],
                ]);

                // Créer un historique pour la restauration
                SimHistory::create([
                    'request_id' => $adjustment->id,
                    'sim_id' => $adjustment->sim_id,
                    'action' => 'restored',
                    'user_id' => $systemUser->id,
                    'user_matricule' => $systemUser->matricule,
                    'notes' => "Restauration automatique après expiration. Nouvelle demande créée: {$restorationRequest->request_number}",
                    'old_data' => [
                        'limite_credit' => $adjustment->limite_credit,
                        'limite_data' => $adjustment->limite_data,
                        'plan_id' => $adjustment->plan_id,
                    ],
                    'new_data' => [
                        'limite_credit' => $restorationRequest->limite_credit,
                        'limite_data' => $restorationRequest->limite_data,
                        'plan_id' => $restorationRequest->plan_id,
                        'restoration_request_number' => $restorationRequest->request_number,
                    ],
                ]);

                $this->info("✓ Ajustement {$adjustment->request_number} restauré. Nouvelle demande: {$restorationRequest->request_number}");
                $restored++;

                DB::commit();
            } catch (\Exception $e) {
                DB::rollBack();
                $errors++;
                $this->error("✗ Erreur lors de la restauration de l'ajustement {$adjustment->request_number}: {$e->getMessage()}");
                Log::error('Error restoring expired temporary adjustment', [
                    'adjustment_id' => $adjustment->id,
                    'request_number' => $adjustment->request_number,
                    'error' => $e->getMessage(),
                    'trace' => $e->getTraceAsString(),
                ]);
            }
        }

        $this->info("\nRésumé:");
        $this->info("- Restauré(s): {$restored}");
        $this->info("- Erreur(s): {$errors}");

        return Command::SUCCESS;
    }
}
