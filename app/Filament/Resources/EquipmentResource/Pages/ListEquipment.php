<?php

namespace App\Filament\Resources\EquipmentResource\Pages;

use App\Filament\Resources\EquipmentResource;
use App\Imports\EquipmentImport;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;
use Filament\Forms;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Support\Facades\Storage;

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
                        ->acceptedFileTypes(['application/vnd.openxmlformats-officedocument.spreadsheetml.sheet', 'application/vnd.ms-excel'])
                        ->disk('public')
                        ->directory('imports')
                        ->visibility('private')
                        ->required()
                        ->helperText('Format attendu: Colonnes SN, Marque, Modèle, N° Inventaire, User Mle, etc.'),
                ])
                ->action(function (array $data) {
                    $file = $data['excel_file'];
                    
                    // Obtenir le chemin réel du fichier
                    $filePath = null;
                    
                    if (is_string($file)) {
                        // Filament stocke les fichiers dans le disque public, répertoire imports
                        // Le chemin peut être relatif (imports/filename.xlsx) ou absolu
                        
                        // Essayer avec le disque public
                        if (Storage::disk('public')->exists($file)) {
                            $filePath = Storage::disk('public')->path($file);
                        } elseif (Storage::disk('public')->exists('imports/' . basename($file))) {
                            $filePath = Storage::disk('public')->path('imports/' . basename($file));
                        } elseif (Storage::disk('local')->exists($file)) {
                            $filePath = Storage::disk('local')->path($file);
                        } else {
                            // Essayer différents emplacements possibles
                            $possiblePaths = [
                                storage_path('app/public/imports/' . basename($file)),
                                storage_path('app/public/' . $file),
                                storage_path('app/public/' . ltrim($file, '/')),
                                storage_path('app/' . $file),
                                public_path('storage/' . $file),
                                public_path('storage/imports/' . basename($file)),
                                $file, // Chemin absolu
                            ];
                            
                            foreach ($possiblePaths as $path) {
                                if (file_exists($path) && is_file($path)) {
                                    $filePath = $path;
                                    break;
                                }
                            }
                        }
                    } elseif (is_object($file)) {
                        // Si c'est un objet UploadedFile
                        if (method_exists($file, 'getRealPath')) {
                            $filePath = $file->getRealPath();
                        } elseif (method_exists($file, 'path')) {
                            $filePath = $file->path();
                        } elseif (method_exists($file, 'getPathname')) {
                            $filePath = $file->getPathname();
                        }
                    }
                    
                    // Vérifier que le fichier existe et est valide
                    if (!$filePath || !file_exists($filePath)) {
                        throw new \Exception("Le fichier Excel n'a pas pu être trouvé. Chemin: " . ($filePath ?? 'non défini'));
                    }
                    
                    // Vérifier que c'est bien un fichier Excel
                    $extension = strtolower(pathinfo($filePath, PATHINFO_EXTENSION));
                    if (!in_array($extension, ['xlsx', 'xls'])) {
                        throw new \Exception("Le fichier n'est pas un fichier Excel valide (.xlsx ou .xls). Extension détectée: {$extension}");
                    }
                    
                    try {
                        // Vérifier que le fichier est lisible
                        if (!is_readable($filePath)) {
                            throw new \Exception("Le fichier n'est pas lisible. Vérifiez les permissions.");
                        }
                        
                        // Vérifier la taille du fichier
                        $fileSize = filesize($filePath);
                        if ($fileSize === false || $fileSize === 0) {
                            throw new \Exception("Le fichier est vide ou corrompu.");
                        }
                        
                        $import = new EquipmentImport();
                        Excel::import($import, $filePath);
                        
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
                        
                        // Optionnel: Supprimer le fichier après import réussi
                        // if (is_string($file) && Storage::disk('public')->exists($file)) {
                        //     Storage::disk('public')->delete($file);
                        // }
                    } catch (\Maatwebsite\Excel\Validators\ValidationException $e) {
                        $failures = $e->failures();
                        $errorMessages = [];
                        foreach ($failures as $failure) {
                            $errorMessages[] = "Ligne {$failure->row()}: " . implode(', ', $failure->errors());
                        }
                        
                        \Filament\Notifications\Notification::make()
                            ->title('Erreurs de validation')
                            ->body(implode("\n", array_slice($errorMessages, 0, 10)))
                            ->danger()
                            ->send();
                        
                        \Log::error('Erreur validation import équipements Excel', [
                            'errors' => $errorMessages
                        ]);
                    } catch (\Exception $e) {
                        \Filament\Notifications\Notification::make()
                            ->title('Erreur lors de l\'import')
                            ->body($e->getMessage() . "\n\nChemin fichier: " . $filePath)
                            ->danger()
                            ->send();
                        
                        \Log::error('Erreur import équipements Excel', [
                            'error' => $e->getMessage(),
                            'file_path' => $filePath,
                            'trace' => $e->getTraceAsString()
                        ]);
                    }
                }),
            Actions\CreateAction::make(),
        ];
    }
}
