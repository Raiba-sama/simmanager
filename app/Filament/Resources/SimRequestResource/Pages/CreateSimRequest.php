<?php

namespace App\Filament\Resources\SimRequestResource\Pages;

use App\Filament\Resources\SimRequestResource;
use Filament\Resources\Pages\CreateRecord;

class CreateSimRequest extends CreateRecord
{
    protected static string $resource = SimRequestResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $data['request_number'] = \App\Models\SimRequest::generateRequestNumber();
        $data['created_by'] = auth()->id();
        return $data;
    }
}

