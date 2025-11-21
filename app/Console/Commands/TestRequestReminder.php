<?php

namespace App\Console\Commands;

use App\Models\SimRequest;
use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class TestRequestReminder extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'test:request-reminder {--days=5 : Nombre de jours à simuler}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Crée ou modifie une demande de test pour tester le système de rappels';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $days = (int) $this->option('days');
        $testDate = Carbon::now()->subDays($days);

        $this->info("Recherche d'une demande en attente à modifier...");

        // Chercher une demande en attente existante
        $request = SimRequest::where('status', 'en_attente')
            ->orderBy('created_at', 'desc')
            ->first();

        if (!$request) {
            $this->warn("Aucune demande en attente trouvée. Création d'une demande de test...");
            
            // Créer une demande de test
            $user = User::where('role', 'user')->first();
            if (!$user) {
                $this->error("Aucun utilisateur avec le rôle 'user' trouvé. Veuillez créer un utilisateur d'abord.");
                return Command::FAILURE;
            }

            $request = SimRequest::create([
                'request_number' => SimRequest::generateRequestNumber(),
                'user_id' => $user->id,
                'request_type' => 'recuperation',
                'status' => 'en_attente',
                'motif' => 'Demande de test pour le système de rappels',
            ]);

            $this->info("Demande de test créée : #{$request->request_number}");
        } else {
            $this->info("Demande trouvée : #{$request->request_number}");
        }
        
        // Modifier la date de création pour simuler un délai (utiliser DB::table pour bypasser les timestamps)
        \DB::table('sim_requests')
            ->where('id', $request->id)
            ->update([
                'created_at' => $testDate,
                'updated_at' => $testDate,
            ]);
        
        // Recharger le modèle
        $request->refresh();
        
        $this->info("Date de création modifiée pour simuler {$days} jours d'attente");

        $daysPending = Carbon::now()->diffInDays($request->created_at);
        $this->info("La demande #{$request->request_number} est maintenant en attente depuis {$daysPending} jour(s)");
        $this->info("Date de création simulée : {$request->created_at->format('d/m/Y H:i')}");

        $this->newLine();
        $this->info("Vous pouvez maintenant tester la commande de rappels :");
        $this->line("php artisan requests:check-pending --days=3");

        return Command::SUCCESS;
    }
}
