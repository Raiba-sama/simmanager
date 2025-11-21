<?php

namespace App\Console\Commands;

use App\Models\SimRequest;
use App\Models\User;
use App\Notifications\RequestReminder;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class CheckPendingRequests extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'requests:check-pending {--days=3 : Nombre de jours minimum en attente}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Vérifie les demandes en attente depuis plus de X jours et envoie des notifications de rappel';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $days = (int) $this->option('days');
        $cutoffDate = Carbon::now()->subDays($days);

        // Récupérer les demandes en attente depuis plus de X jours
        $pendingRequests = SimRequest::where('status', 'en_attente')
            ->where('created_at', '<=', $cutoffDate)
            ->with(['user'])
            ->get();

        $this->info("Trouvé {$pendingRequests->count()} demande(s) en attente depuis plus de {$days} jours");

        $notifiedCount = 0;
        $notificationClass = 'App\\Notifications\\RequestReminder';

        foreach ($pendingRequests as $request) {
            $daysPending = Carbon::now()->diffInDays($request->created_at);
            
            // Envoyer notification aux validateurs/admins
            $validators = User::whereIn('role', ['admin', 'validator'])
                ->where('active', true)
                ->get();

            $requestNotified = false;

            foreach ($validators as $validator) {
                // Vérifier si ce validateur a déjà reçu une notification pour cette demande aujourd'hui
                $today = Carbon::today();
                $alreadyNotifiedToday = DB::table('notifications')
                    ->where('notifiable_type', User::class)
                    ->where('notifiable_id', $validator->id)
                    ->where('type', $notificationClass)
                    ->whereRaw("JSON_EXTRACT(data, '$.request_id') = ?", [$request->id])
                    ->whereDate('created_at', $today)
                    ->exists();

                if (!$alreadyNotifiedToday) {
                    $validator->notify(new RequestReminder($request, $daysPending));
                    $notifiedCount++;
                    $requestNotified = true;
                }
            }

            if ($requestNotified) {
                $this->line("Notification envoyée pour la demande #{$request->request_number} ({$daysPending} jours en attente)");
            }
        }

        $this->info("Total: {$notifiedCount} notification(s) envoyée(s)");

        return Command::SUCCESS;
    }
}
