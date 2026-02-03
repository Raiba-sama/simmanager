<?php

namespace App\Filament\Widgets;

use App\Models\Equipment;
use App\Models\Zone;
use Filament\Widgets\ChartWidget;

class InventoryZoneChartWidget extends ChartWidget
{
    protected static bool $isLazy = false;

    protected static ?string $pollingInterval = null;

    protected static ?string $heading = 'Par zone';

    protected static ?string $description = 'Équipements attribués par zone';

    protected static ?string $maxHeight = '280px';

    protected static string $color = 'success';

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
        $zones = Zone::query()->orderBy('name')->get();
        $labels = [];
        $data = [];

        foreach ($zones as $zone) {
            $count = Equipment::query()
                ->whereHas('assignments', function ($q) {
                    $q->whereNull('returned_at');
                })
                ->whereHas('assignments.assignedToAgency', function ($q) use ($zone) {
                    $q->where('zone_id', $zone->id);
                })
                ->count();
            if ($count > 0) {
                $labels[] = $zone->name;
                $data[] = $count;
            }
        }

        if (empty($data)) {
            $labels[] = 'Aucune zone';
            $data[] = 0;
        }

        return [
            'datasets' => [
                [
                    'label' => 'Équipements',
                    'data' => $data,
                    'backgroundColor' => 'rgba(25, 135, 84, 0.7)',
                    'borderColor' => 'rgba(25, 135, 84, 1)',
                    'borderWidth' => 1,
                ],
            ],
            'labels' => $labels,
        ];
    }
}
