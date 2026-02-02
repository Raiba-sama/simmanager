<?php

namespace App\Filament\Widgets;

use App\Models\Mission;
use Filament\Widgets\ChartWidget;

class MissionsByAgencyChartWidget extends ChartWidget
{
    protected static ?string $heading = 'Missions par agence';

    protected static ?string $description = 'Top 8 agences par nombre de missions';

    protected static ?string $maxHeight = '250px';

    protected static ?int $sort = 4;

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
        $query = Mission::query()
            ->join('agencies', 'missions.agency_id', '=', 'agencies.id')
            ->selectRaw('agencies.code, agencies.name, count(missions.id) as total')
            ->groupBy('agencies.id', 'agencies.code', 'agencies.name')
            ->orderByDesc('total')
            ->limit(8);

        $results = $query->get();

        $labels = $results->map(fn ($r) => $r->code . ' - ' . \Illuminate\Support\Str::limit($r->name, 15))->toArray();
        $data = $results->pluck('total')->toArray();

        if (empty($data)) {
            $labels[] = 'Aucune';
            $data[] = 0;
        }

        return [
            'datasets' => [
                [
                    'label' => 'Missions',
                    'data' => $data,
                    'backgroundColor' => 'rgba(25, 135, 84, 0.6)',
                    'borderColor' => 'rgb(25, 135, 84)',
                    'borderWidth' => 1,
                ],
            ],
            'labels' => $labels,
        ];
    }
}
