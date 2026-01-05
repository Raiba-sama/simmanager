<?php

namespace App\Filament\Resources\TransmissionSheetResource\Pages;

use App\Filament\Resources\TransmissionSheetResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListTransmissionSheets extends ListRecords
{
    protected static string $resource = TransmissionSheetResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
