<?php

namespace App\Filament\Resources\SimRequestResource\Pages;

use App\Filament\Resources\SimRequestResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditSimRequest extends EditRecord
{
    protected static string $resource = SimRequestResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}

