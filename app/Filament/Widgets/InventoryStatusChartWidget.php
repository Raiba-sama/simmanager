<?php

namespace App\Filament\Widgets;

use App\Models\Equipment;
use Filament\Widgets\ChartWidget;

class InventoryStatusChartWidget extends ChartWidget
{
    protected static bool $isLazy = false;

    protected static ?string $heading = 'Par statut';

    protected static ?string $description = 'Répartition des équipements par statut';

    protected static ?string $maxHeight = '280px';

    protected static string $color = 'primary';

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
        $statuses = [
            'available' => ['label' => 'Disponible', 'color' => 'rgba(25, 135, 84, 0.8)'],
            'assigned' => ['label' => 'Attribué', 'color' => 'rgba(13, 110, 253, 0.8)'],
            'maintenance' => ['label' => 'En maintenance', 'color' => 'rgba(255, 193, 7, 0.8)'],
            'retired' => ['label' => 'Retiré', 'color' => 'rgba(108, 117, 125, 0.8)'],
            'lost' => ['label' => 'Perdu', 'color' => 'rgba(220, 53, 69, 0.8)'],
            'damaged' => ['label' => 'Endommagé', 'color' => 'rgba(253, 126, 20, 0.8)'],
        ];

        $labels = [];
        $data = [];
        $backgroundColor = [];
        $i = 0;
        $colors = array_column($statuses, 'color');

        foreach ($statuses as $key => $config) {
            $count = Equipment::where('status', $key)->count();
            if ($count > 0) {
                $labels[] = $config['label'];
                $data[] = $count;
                $backgroundColor[] = $colors[$i % count($colors)];
                $i++;
            }
        }

        if (empty($data)) {
            $labels[] = 'Aucun équipement';
            $data[] = 1;
            $backgroundColor[] = 'rgba(108, 117, 125, 0.8)';
        }

        return [
            'datasets' => [
                [
                    'label' => 'Équipements',
                    'data' => $data,
                    'backgroundColor' => $backgroundColor,
                    'borderWidth' => 1,
                ],
            ],
            'labels' => $labels,
        ];
    }
}
