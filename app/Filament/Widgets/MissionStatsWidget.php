<?php

namespace App\Filament\Widgets;

use App\Models\Mission;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class MissionStatsWidget extends BaseWidget
{
    protected static ?int $sort = 1;

    public static function canView(): bool
    {
        return auth()->user()?->isAdmin() ?? false;
    }

    protected function getStats(): array
    {
        $now = now();
        $startOfMonth = $now->copy()->startOfMonth();
        $endOfMonth = $now->copy()->endOfMonth();

        $missionsCeMois = Mission::where('start_date', '<=', $endOfMonth)
            ->where(function ($query) use ($startOfMonth) {
                $query->where('end_date', '>=', $startOfMonth)->orWhereNull('end_date');
            })
            ->count();

        $missionsAvenir = Mission::where('start_date', '>', $now)->whereIn('status', ['planned', 'in_progress'])->count();
        $missionsEnCours = Mission::where('start_date', '<=', $now)->where('end_date', '>=', $now)->whereIn('status', ['planned', 'in_progress'])->count();
        $missionsTerminees = Mission::where('status', 'completed')->count();

        // Données pour les mini graphiques (6 derniers mois)
        $chartData = collect(range(5, 0))->map(fn ($i) => Mission::whereMonth('start_date', $now->copy()->subMonths($i)->month)
            ->whereYear('start_date', $now->copy()->subMonths($i)->year)
            ->count());

        return [
            Stat::make('Missions ce mois', $missionsCeMois)
                ->description('Début ou fin dans le mois en cours')
                ->descriptionIcon('heroicon-o-calendar')
                ->color('primary')
                ->chart($chartData->toArray()),

            Stat::make('À venir', $missionsAvenir)
                ->description('Planifiées, date de début future')
                ->descriptionIcon('heroicon-o-clock')
                ->color('info')
                ->chart($chartData->toArray()),

            Stat::make('En cours', $missionsEnCours)
                ->description('En cours actuellement')
                ->descriptionIcon('heroicon-o-play')
                ->color('warning')
                ->chart($chartData->toArray()),

            Stat::make('Terminées', $missionsTerminees)
                ->description('Statut terminée')
                ->descriptionIcon('heroicon-o-check-circle')
                ->color('success')
                ->chart($chartData->toArray()),
        ];
    }
}
