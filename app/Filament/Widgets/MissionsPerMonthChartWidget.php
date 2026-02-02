<?php

namespace App\Filament\Widgets;

use App\Models\Mission;
use Filament\Widgets\ChartWidget;

class MissionsPerMonthChartWidget extends ChartWidget
{
    protected static ?string $heading = 'Missions par mois';

    protected static ?string $description = 'Nombre de missions (date de début) sur les 6 derniers mois';

    protected static ?string $maxHeight = '250px';

    protected static ?int $sort = 2;

    public static function canView(): bool
    {
        return auth()->user()?->isAdmin() ?? false;
    }

    protected function getType(): string
    {
        return 'bar';
    }

    protected function getData(): array
    {
        $now = now();
        $labels = [];
        $data = [];

        for ($i = 5; $i >= 0; $i--) {
            $date = $now->copy()->subMonths($i);
            $labels[] = $date->translatedFormat('M Y');
            $data[] = Mission::whereMonth('start_date', $date->month)
                ->whereYear('start_date', $date->year)
                ->count();
        }

        return [
            'datasets' => [
                [
                    'label' => 'Missions',
                    'data' => $data,
                    'backgroundColor' => 'rgba(13, 110, 253, 0.6)',
                    'borderColor' => 'rgb(13, 110, 253)',
                    'borderWidth' => 1,
                ],
            ],
            'labels' => $labels,
        ];
    }
}
