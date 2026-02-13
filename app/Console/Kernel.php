<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    /**
     * Define the application's command schedule.
     */
    protected function schedule(Schedule $schedule): void
    {
        // Vérifier les demandes en attente chaque jour à 9h00
        $schedule->command('requests:check-pending --days=3')
            ->dailyAt('09:00')
            ->timezone('Indian/Antananarivo');
        
        // Restaurer les ajustements temporaires expirés chaque jour à 8h00
        $schedule->command('adjustments:restore-expired')
            ->dailyAt('08:00')
            ->timezone('Indian/Antananarivo');
    }

    /**
     * Register the commands for the application.
     */
    protected function commands(): void
    {
        $this->load(__DIR__.'/Commands');

        require base_path('routes/console.php');
    }
}
