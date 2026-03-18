<?php

namespace App\Filament\Resources\TransmissionSheetResource\Pages;

use App\Filament\Resources\TransmissionSheetResource;
use App\Models\TransmissionSheetItem;
use Filament\Resources\Pages\CreateRecord;

class CreateTransmissionSheet extends CreateRecord
{
    protected static string $resource = TransmissionSheetResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $data['created_by'] = auth()->id();
        unset($data['equipment_items']);
        return $data;
    }

    protected function afterCreate(): void
    {
        $items = $this->form->getState()['equipment_items'] ?? [];

        foreach ($items as $item) {
            if (empty($item['equipment_id'])) continue;

            TransmissionSheetItem::create([
                'transmission_sheet_id' => $this->record->id,
                'equipment_id' => $item['equipment_id'],
                'quantity' => 1,
                'condition_at_transmission' => $item['condition_at_transmission'] ?? 'good',
                'notes' => $item['notes'] ?? null,
            ]);
        }
    }
}
