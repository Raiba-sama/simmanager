<?php

namespace App\Filament\Widgets;

use App\Models\EquipmentType;
use Filament\Widgets\ChartWidget;

class InventoryTypeChartWidget extends ChartWidget
{
    protected static bool $isLazy = false;

    protected static ?string $heading = 'Par type d\'équipement';

    protected static ?string $description = 'Nombre d\'équipements par type';

    protected static ?string $maxHeight = '280px';

    protected static string $color = 'info';

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
        $types = EquipmentType::withCount('equipment')
            ->orderBy('equipment_count', 'desc')
            ->get();

        $labels = $types->pluck('name')->toArray();
        $data = $types->pluck('equipment_count')->toArray();

        if (empty($data)) {
            $labels = ['Aucun type'];
            $data = [0];
        }

        return [
            'datasets' => [
                [
                    'label' => 'Nombre d\'équipements',
                    'data' => $data,
                    'backgroundColor' => 'rgba(13, 110, 253, 0.7)',
                    'borderColor' => 'rgba(13, 110, 253, 1)',
                    'borderWidth' => 1,
                ],
            ],
            'labels' => $labels,
        ];
    }
}
