<?php

namespace App\Filament\Resources\EquipmentResource\Pages;

use App\Filament\Resources\EquipmentResource;
use App\Imports\EquipmentImport;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;
use Filament\Forms;
use Maatwebsite\Excel\Facades\Excel;

class ListEquipment extends ListRecords
{
    protected static string $resource = EquipmentResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\Action::make('importExcel')
                ->label('Importer Excel')
                ->icon('heroicon-o-arrow-up-tray')
                ->form([
                    Forms\Components\FileUpload::make('excel_file')
                        ->label('Fichier Excel (.xlsx)')
                        ->acceptedFileTypes(['application/vnd.openxmlformats-officedocument.spreadsheetml.sheet'])
                        ->required()
                        ->helperText('Format attendu: Colonnes SN, Marque, Modèle, N° Inventaire, User Mle, etc.'),
                ])
                ->action(function (array $data) {
                    $file = $data['excel_file'];
                    
                    try {
                        $import = new EquipmentImport();
                        Excel::import($import, $file);
                        
                        $imported = $import->getImported();
                        $skipped = $import->getSkipped();
                        $errors = $import->getErrors();
                        
                        $title = "Import terminé";
                        $body = "{$imported} équipement(s) importé(s)";
                        
                        if ($skipped > 0) {
                            $body .= "\n{$skipped} équipement(s) ignoré(s) (déjà existant(s))";
                        }
                        
                        $notification = \Filament\Notifications\Notification::make()
                            ->title($title)
                            ->body($body);
                        
                        if (!empty($errors)) {
                            $errorCount = count($errors);
                            $body .= "\n\n⚠ {$errorCount} erreur(s) détectée(s)";
                            
                            // Logger les erreurs
                            \Log::warning('Erreurs import équipements', ['errors' => $errors]);
                            
                            // Afficher les premières erreurs dans la notification
                            if ($errorCount <= 5) {
                                $body .= "\n\n" . implode("\n", array_slice($errors, 0, 5));
                            } else {
                                $body .= "\n\n" . implode("\n", array_slice($errors, 0, 3));
                                $body .= "\n... et " . ($errorCount - 3) . " autre(s) erreur(s)";
                            }
                            
                            $notification->warning();
                        } else {
                            $notification->success();
                        }
                        
                        $notification->send();
                    } catch (\Exception $e) {
                        \Filament\Notifications\Notification::make()
                            ->title('Erreur lors de l\'import')
                            ->body($e->getMessage())
                            ->danger()
                            ->send();
                        
                        \Log::error('Erreur import équipements Excel', [
                            'error' => $e->getMessage(),
                            'trace' => $e->getTraceAsString()
                        ]);
                    }
                }),
            Actions\CreateAction::make(),
        ];
    }
}
