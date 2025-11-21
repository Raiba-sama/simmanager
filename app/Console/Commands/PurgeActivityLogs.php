<?php

namespace App\Console\Commands;

use App\Models\ActivityLog;
use Illuminate\Console\Command;

class PurgeActivityLogs extends Command
{
    protected $signature = 'logs:purge {--days=90 : Nombre de jours à conserver}';
    protected $description = 'Purge les logs d\'activité plus anciens que X jours';

    public function handle()
    {
        $days = (int) $this->option('days');
        $cutoffDate = now()->subDays($days);

        $count = ActivityLog::where('created_at', '<', $cutoffDate)->delete();

        $this->info("{$count} log(s) supprimé(s) (plus anciens que {$days} jours).");

        return Command::SUCCESS;
    }
}

