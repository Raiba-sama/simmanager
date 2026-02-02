<?php

namespace App\Filament\Widgets;

use App\Models\Mission;
use Filament\Widgets\ChartWidget;

class MissionsByTypeChartWidget extends ChartWidget
{
    protected static ?string $heading = 'Répartition par type';

    protected static ?string $description = 'Nombre de missions par type';

    protected static ?string $maxHeight = '250px';

    protected static ?int $sort = 3;

    public static function canView(): bool
    {
        return auth()->user()?->isAdmin() ?? false;
    }

    protected function getType(): string
    {
        return 'doughnut';
    }

    protected function getData(): array
    {
        $counts = Mission::selectRaw('type, count(*) as total')
            ->groupBy('type')
            ->pluck('total', 'type');

        $labels = [];
        $data = [];
        $colors = [
            'rgba(13, 110, 253, 0.8)',
            'rgba(25, 135, 84, 0.8)',
            'rgba(255, 193, 7, 0.8)',
            'rgba(253, 126, 20, 0.8)',
            'rgba(220, 53, 69, 0.8)',
            'rgba(108, 117, 125, 0.8)',
        ];

        foreach (Mission::TYPES as $key => $label) {
            $total = $counts->get($key, 0);
            if ($total > 0) {
                $labels[] = $label;
                $data[] = $total;
            }
        }

        if (empty($data)) {
            $labels[] = 'Aucune mission';
            $data[] = 1;
        }

        $backgroundColor = array_slice($colors, 0, count($data));
        while (count($backgroundColor) < count($data)) {
            $backgroundColor[] = 'rgba(108, 117, 125, 0.8)';
        }

        return [
            'datasets' => [
                [
                    'label' => 'Missions',
                    'data' => array_values($data),
                    'backgroundColor' => $backgroundColor,
                    'borderWidth' => 1,
                ],
            ],
            'labels' => $labels,
        ];
    }
}
