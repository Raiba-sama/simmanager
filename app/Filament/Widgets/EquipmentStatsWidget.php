<?php

namespace App\Filament\Widgets;

use App\Models\Equipment;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class EquipmentStatsWidget extends BaseWidget
{
    protected static ?int $sort = 1;
    
    public static function canView(): bool
    {
        return auth()->user()?->isAdmin() ?? false;
    }
    
    protected function getStats(): array
    {
        $total = Equipment::count();
        $available = Equipment::where('status', 'available')->count();
        $assigned = Equipment::where('status', 'assigned')->count();
        $maintenance = Equipment::where('status', 'maintenance')->count();
        
        return [
            Stat::make('Total équipements', $total)
                ->description('Tous les équipements enregistrés')
                ->descriptionIcon('heroicon-o-computer-desktop')
                ->color('primary')
                ->chart([7, 3, 4, 5, 6, 3, 5]),
            
            Stat::make('Disponibles', $available)
                ->description(number_format(($total > 0 ? ($available / $total) * 100 : 0), 1) . '% du total')
                ->descriptionIcon('heroicon-o-check-circle')
                ->color('success')
                ->chart([3, 2, 1, 4, 2, 3, 2]),
            
            Stat::make('Attribués', $assigned)
                ->description(number_format(($total > 0 ? ($assigned / $total) * 100 : 0), 1) . '% du total')
                ->descriptionIcon('heroicon-o-user')
                ->color('info')
                ->chart([2, 3, 4, 3, 2, 4, 3]),
            
            Stat::make('En maintenance', $maintenance)
                ->description(number_format(($total > 0 ? ($maintenance / $total) * 100 : 0), 1) . '% du total')
                ->descriptionIcon('heroicon-o-wrench-screwdriver')
                ->color('warning')
                ->chart([1, 1, 2, 1, 1, 2, 1]),
        ];
    }
}

