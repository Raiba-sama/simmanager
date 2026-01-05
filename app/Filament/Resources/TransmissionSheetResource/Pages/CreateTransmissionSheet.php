<?php

namespace App\Filament\Resources\TransmissionSheetResource\Pages;

use App\Filament\Resources\TransmissionSheetResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;

class CreateTransmissionSheet extends CreateRecord
{
    protected static string $resource = TransmissionSheetResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $data['created_by'] = auth()->id();
        return $data;
    }
}
