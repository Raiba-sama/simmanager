<?php

namespace App\Filament\Widgets;

use App\Models\Agency;
use App\Models\Equipment;
use Filament\Widgets\ChartWidget;

class InventoryAgencyChartWidget extends ChartWidget
{
    protected static ?string $heading = 'Par agence';

    protected static ?string $description = 'Équipements attribués par agence';

    protected static ?string $maxHeight = '280px';

    protected static string $color = 'warning';

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
        $agencies = Agency::query()->orderBy('name')->get();
        $labels = [];
        $data = [];

        foreach ($agencies as $agency) {
            $count = Equipment::query()
                ->whereHas('assignments', function ($q) use ($agency) {
                    $q->whereNull('returned_at')->where('assigned_to_agency_id', $agency->id);
                })
                ->count();
            if ($count > 0) {
                $labels[] = \Illuminate\Support\Str::limit($agency->name, 18);
                $data[] = $count;
            }
        }

        if (empty($data)) {
            $labels[] = 'Aucune agence';
            $data[] = 0;
        }

        return [
            'datasets' => [
                [
                    'label' => 'Équipements',
                    'data' => $data,
                    'backgroundColor' => 'rgba(255, 193, 7, 0.7)',
                    'borderColor' => 'rgba(255, 193, 7, 1)',
                    'borderWidth' => 1,
                ],
            ],
            'labels' => $labels,
        ];
    }
}
