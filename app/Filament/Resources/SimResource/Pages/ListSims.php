<?php

namespace App\Filament\Resources\SimResource\Pages;

use App\Filament\Resources\SimResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;
use Filament\Forms;
use Illuminate\Support\Facades\DB;

class ListSims extends ListRecords
{
    protected static string $resource = SimResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\Action::make('importCsv')
                ->label('Importer CSV')
                ->icon('heroicon-o-arrow-up-tray')
                ->form([
                    Forms\Components\FileUpload::make('csv_file')
                        ->label('Fichier CSV')
                        ->acceptedFileTypes(['text/csv', 'text/plain'])
                        ->required()
                        ->helperText('Format: ICCID,Phone,Operator,Plan,Cost'),
                ])
                ->action(function (array $data) {
                    $file = $data['csv_file'];
                    $lines = file($file->getRealPath(), FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
                    
                    $imported = 0;
                    DB::beginTransaction();
                    try {
                        foreach ($lines as $index => $line) {
                            if ($index === 0) continue;
                            $data = str_getcsv($line);
                            if (empty($data[0])) continue;
                            
                            $iccid = trim($data[0]);
                            if (\App\Models\Sim::where('iccid', $iccid)->exists()) {
                                continue;
                            }
                            
                            \App\Models\Sim::create([
                                'iccid' => $iccid,
                                'phone_number' => $data[1] ?? null,
                                'operator' => $data[2] ?? null,
                                'plan_type' => $data[3] ?? null,
                                'monthly_cost' => isset($data[4]) ? (float) $data[4] : null,
                                'status' => 'libre',
                            ]);
                            $imported++;
                        }
                        DB::commit();
                        \Filament\Notifications\Notification::make()
                            ->title("{$imported} SIM(s) importée(s)")
                            ->success()
                            ->send();
                    } catch (\Exception $e) {
                        DB::rollBack();
                        \Filament\Notifications\Notification::make()
                            ->title('Erreur: ' . $e->getMessage())
                            ->danger()
                            ->send();
                    }
                }),
            Actions\CreateAction::make(),
        ];
    }
}

