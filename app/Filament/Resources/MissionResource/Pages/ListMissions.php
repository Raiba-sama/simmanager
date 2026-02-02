<?php

namespace App\Filament\Resources\MissionResource\Pages;

use App\Filament\Resources\MissionResource;
use App\Filament\Widgets\MissionStatsWidget;
use App\Filament\Widgets\MissionsByAgencyChartWidget;
use App\Filament\Widgets\MissionsByTypeChartWidget;
use App\Filament\Widgets\MissionsPerMonthChartWidget;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListMissions extends ListRecords
{
    protected static string $resource = MissionResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }

    protected function getHeaderWidgets(): array
    {
        return [
            MissionStatsWidget::class,
            MissionsPerMonthChartWidget::class,
            MissionsByTypeChartWidget::class,
            MissionsByAgencyChartWidget::class,
        ];
    }

    public function getHeaderWidgetsColumns(): int | array
    {
        return 2;
    }
}
