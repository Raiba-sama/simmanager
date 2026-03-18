<?php

namespace App\Filament\Resources\TransmissionSheetResource\Pages;

use App\Filament\Resources\TransmissionSheetResource;
use App\Models\TransmissionSheetItem;
use Barryvdh\DomPDF\Facade\Pdf;
use Filament\Actions;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\EditRecord;

class EditTransmissionSheet extends EditRecord
{
    protected static string $resource = TransmissionSheetResource::class;

    protected function mutateFormDataBeforeSave(array $data): array
    {
        unset($data['equipment_items']);
        return $data;
    }

    protected function afterSave(): void
    {
        $items = $this->form->getState()['equipment_items'] ?? [];

        $existingIds = [];
        foreach ($items as $item) {
            if (empty($item['equipment_id'])) continue;

            $existing = TransmissionSheetItem::where('transmission_sheet_id', $this->record->id)
                ->where('equipment_id', $item['equipment_id'])
                ->first();

            if ($existing) {
                $existing->update([
                    'condition_at_transmission' => $item['condition_at_transmission'] ?? 'good',
                    'notes' => $item['notes'] ?? null,
                ]);
                $existingIds[] = $existing->id;
            } else {
                $new = TransmissionSheetItem::create([
                    'transmission_sheet_id' => $this->record->id,
                    'equipment_id' => $item['equipment_id'],
                    'quantity' => 1,
                    'condition_at_transmission' => $item['condition_at_transmission'] ?? 'good',
                    'notes' => $item['notes'] ?? null,
                ]);
                $existingIds[] = $new->id;
            }
        }

        TransmissionSheetItem::where('transmission_sheet_id', $this->record->id)
            ->whereNotIn('id', $existingIds)
            ->delete();
    }

    protected function getHeaderActions(): array
    {
        return [
            Actions\Action::make('generatePdf')
                ->label('Générer PDF')
                ->icon('heroicon-o-document-arrow-down')
                ->color('success')
                ->action(function () {
                    $sheet = $this->record->load([
                        'toUser', 'fromUser',
                        'toAgency.zone', 'fromAgency.zone',
                        'creator',
                        'items.equipment.equipmentType',
                    ]);

                    $pdf = Pdf::loadView('transmission-sheets.pdf', ['transmissionSheet' => $sheet]);
                    $pdf->setOption('encoding', 'utf-8');
                    $pdf->setOption('defaultFont', 'DejaVu Sans');
                    $pdf->setPaper('a4', 'portrait');

                    $filename = 'BT_' . ($sheet->sheet_number ?? $sheet->id) . '.pdf';

                    return response()->streamDownload(
                        fn () => print($pdf->output()),
                        $filename
                    );
                }),
            Actions\DeleteAction::make(),
        ];
    }
}
