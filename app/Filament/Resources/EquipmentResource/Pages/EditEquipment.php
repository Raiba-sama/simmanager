<?php

namespace App\Filament\Resources\EquipmentResource\Pages;

use App\Filament\Resources\EquipmentResource;
use App\Models\EquipmentAssignment;
use App\Models\TransmissionSheet;
use App\Models\TransmissionSheetItem;
use App\Models\User;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Support\Facades\DB;
use Filament\Notifications\Notification;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Barryvdh\DomPDF\Facade\Pdf;

class EditEquipment extends EditRecord
{
    protected static string $resource = EquipmentResource::class;

    /** Données d'attribution du formulaire (conservées avant mutateFormDataBeforeSave pour afterSave) */
    protected ?array $pendingAssignmentData = null;

    protected function getHeaderActions(): array
    {
        return [
            Actions\Action::make('downloadTransmissionSheet')
                ->label('Télécharger le bordereau')
                ->icon('heroicon-o-arrow-down-tray')
                ->color('success')
                ->visible(fn () => $this->record->currentAssignment && $this->record->currentAssignment->transmissionSheet)
                ->action(function () {
                    $assignment = $this->record->currentAssignment;
                    if (!$assignment || !$assignment->transmissionSheet) {
                        \Filament\Notifications\Notification::make()
                            ->title('Aucun bordereau trouvé')
                            ->warning()
                            ->send();
                        return;
                    }
                    
                    $transmissionSheet = $assignment->transmissionSheet->load(['toUser', 'fromUser', 'toAgency.zone', 'fromAgency.zone', 'creator', 'items.equipment.equipmentType']);
                    
                    try {
                        // Utiliser loadView directement avec options DomPDF
                        $pdf = Pdf::loadView('transmission-sheets.pdf', compact('transmissionSheet'));
                        $pdf->setOption('encoding', 'utf-8');
                        $pdf->setOption('defaultFont', 'DejaVu Sans');
                        $pdf->setOption('enable-remote', true);
                        $pdf->setPaper('a4', 'portrait');
                        
                        $filename = 'bordereau_' . preg_replace('/[^a-zA-Z0-9_-]/', '_', $transmissionSheet->sheet_number ?? '') . '.pdf';
                        
                        return response()->streamDownload(function () use ($pdf) {
                            echo $pdf->output();
                        }, $filename, [
                            'Content-Type' => 'application/pdf',
                        ]);
                    } catch (\Exception $e) {
                        \Filament\Notifications\Notification::make()
                            ->title('Erreur lors de la génération du PDF')
                            ->body('Erreur: ' . $e->getMessage())
                            ->danger()
                            ->send();
                        
                        Log::error('Erreur génération PDF bordereau', [
                            'error' => $e->getMessage(),
                            'trace' => $e->getTraceAsString(),
                            'transmission_sheet_id' => $transmissionSheet->id ?? null,
                        ]);
                        
                        return null;
                    }
                }),
            Actions\DeleteAction::make(),
        ];
    }

    protected function mutateFormDataBeforeFill(array $data): array
    {
        // Pré-remplir avec les données de l'attribution actuelle si elle existe
        $currentAssignment = $this->record->currentAssignment;
        if ($currentAssignment && $currentAssignment->assignedToUser) {
            $data['assigned_to_user_id'] = $currentAssignment->assigned_to_user_id;
            $data['assignment_date'] = $currentAssignment->assigned_at?->format('Y-m-d');
            $data['assignment_notes'] = $currentAssignment->notes;
        }
        
        return $data;
    }

    protected function mutateFormDataBeforeSave(array $data): array
    {
        // Fallback: récupérer l'état brut du formulaire (champs conditionnels parfois absents de $data)
        $raw = method_exists($this->form, 'getRawState') ? $this->form->getRawState() : [];
        if (! is_array($raw)) {
            $raw = [];
        }

        // Conserver l'ancien statut (après save(), getOriginal() renvoie la nouvelle valeur)
        $previousStatus = $this->record->status ?? null;

        // Conserver les données d'attribution pour afterSave (priorité à $data, puis $raw)
        $this->pendingAssignmentData = [
            'previous_status' => $previousStatus,
            'create_new_user' => $data['create_new_user'] ?? $raw['create_new_user'] ?? false,
            'new_user_matricule' => $data['new_user_matricule'] ?? $raw['new_user_matricule'] ?? null,
            'new_user_name' => $data['new_user_name'] ?? $raw['new_user_name'] ?? null,
            'new_user_first_name' => $data['new_user_first_name'] ?? $raw['new_user_first_name'] ?? null,
            'new_user_email' => $data['new_user_email'] ?? $raw['new_user_email'] ?? null,
            'new_user_fonction' => $data['new_user_fonction'] ?? $raw['new_user_fonction'] ?? null,
            'assigned_to_user_id' => $data['assigned_to_user_id'] ?? $raw['assigned_to_user_id'] ?? null,
            'assignment_date' => $data['assignment_date'] ?? $raw['assignment_date'] ?? null,
            'assignment_notes' => $data['assignment_notes'] ?? $raw['assignment_notes'] ?? null,
        ];

        // Ne pas sauvegarder les champs temporaires sur le modèle Equipment
        unset($data['create_new_user']);
        unset($data['new_user_matricule']);
        unset($data['new_user_name']);
        unset($data['new_user_first_name']);
        unset($data['new_user_email']);
        unset($data['new_user_fonction']);
        unset($data['assignment_date']);
        unset($data['assignment_notes']);
        unset($data['assigned_to_user_id']);

        return $data;
    }

    /**
     * Tout le cycle de sauvegarde (équipement + attribution) dans une seule transaction.
     * Si l'attribution échoue, l'équipement n'est pas mis à jour non plus.
     */
    protected function validateFormAndUpdateRecordAndCallHooks(): void
    {
        $this->callHook('beforeValidate');
        $data = $this->form->getState();
        $this->callHook('afterValidate');
        $data = $this->mutateFormDataBeforeSave($data);
        $this->callHook('beforeSave');

        DB::transaction(function () use ($data) {
            $this->handleRecordUpdate($this->getRecord(), $data);
            $this->callHook('afterSave');
        });
    }

    protected function afterSave(): void
    {
        try {
            $this->processAssignmentAfterSave();
        } catch (\Throwable $e) {
            Log::error('EditEquipment: erreur attribution après sauvegarde', [
                'equipment_id' => $this->record->id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);
            Notification::make()
                ->title('Erreur lors de l\'enregistrement de l\'attribution')
                ->body($e->getMessage())
                ->danger()
                ->send();
            throw $e;
        }
    }

    protected function processAssignmentAfterSave(): void
    {
        $data = $this->form->getState();
        $assign = $this->pendingAssignmentData ?? [];

        // Compléter $assign avec l'état actuel du formulaire au cas où
        foreach (['assigned_to_user_id', 'assignment_date', 'assignment_notes'] as $key) {
            if (array_key_exists($key, $data) && ($assign[$key] ?? null) === null) {
                $assign[$key] = $data[$key];
            }
        }

        $oldStatus = $assign['previous_status'] ?? null;
        $newStatus = $data['status'] ?? $this->record->status;
        $this->record->unsetRelation('currentAssignment');
        $currentAssignment = $this->record->currentAssignment;

        $resolveUserId = function () use ($assign) {
            if ($assign['create_new_user'] ?? false) {
                $user = User::create([
                    'matricule' => $assign['new_user_matricule'],
                    'name' => $assign['new_user_name'],
                    'first_name' => $assign['new_user_first_name'] ?? null,
                    'email' => $assign['new_user_email'],
                    'fonction' => $assign['new_user_fonction'] ?? null,
                    'password' => bcrypt('password'),
                    'role' => 'user',
                    'active' => true,
                ]);
                return $user->id;
            }
            $uid = $assign['assigned_to_user_id'] ?? null;
            return $uid !== null && $uid !== '' ? (int) $uid : null;
        };

        // Cas 1 : Statut vient de passer à "attribué" OU statut "attribué" sans attribution active → créer attribution
        if ($newStatus === 'assigned' && ($oldStatus !== 'assigned' || ! $currentAssignment)) {
            $hasActiveAssignment = $this->record->assignments()->whereNull('returned_at')->exists();
            if (! $hasActiveAssignment) {
                $userId = $resolveUserId();
                if ($userId) {
                    $this->createAssignmentAndSheet($userId, $assign);
                }
            }
            return;
        }

        // Cas 2 : Statut reste "attribué" avec attribution active → mise à jour ou changement de bénéficiaire
        if ($newStatus === 'assigned' && $currentAssignment) {
            $assignedAt = isset($assign['assignment_date']) ? \Carbon\Carbon::parse($assign['assignment_date']) : $currentAssignment->assigned_at;
            $notes = $assign['assignment_notes'] ?? $currentAssignment->notes;

            $beneficiaryChanged = ($assign['create_new_user'] ?? false)
                || ((int) ($assign['assigned_to_user_id'] ?? 0)) !== (int) $currentAssignment->assigned_to_user_id;

            if ($beneficiaryChanged) {
                $newUserId = $resolveUserId();
                if ($newUserId) {
                    $currentAssignment->update([
                        'returned_at' => now(),
                        'return_reason' => 'Changement de bénéficiaire',
                    ]);
                    $this->createAssignmentAndSheet($newUserId, $assign);
                }
            } else {
                $currentAssignment->update([
                    'assigned_at' => $assignedAt,
                    'notes' => $notes,
                ]);
            }
        }
    }

    /**
     * Crée une attribution et son bordereau de transmission.
     */
    protected function createAssignmentAndSheet(int $userId, array $assign): void
    {
        DB::transaction(function () use ($userId, $assign) {
            $sheetNumber = 'BT-' . date('Y') . '-' . strtoupper(Str::random(6));
            $transmissionSheet = TransmissionSheet::create([
                'sheet_number' => $sheetNumber,
                'type' => 'assignment',
                'to_user_id' => $userId,
                'created_by' => auth()->id(),
                'transmission_date' => $assign['assignment_date'] ?? now(),
                'status' => 'completed',
                'notes' => $assign['assignment_notes'] ?? 'Attribution automatique lors de la modification de l\'équipement',
            ]);

            TransmissionSheetItem::create([
                'transmission_sheet_id' => $transmissionSheet->id,
                'equipment_id' => $this->record->id,
                'quantity' => 1,
                'condition_at_transmission' => $this->record->condition ?? 'good',
                'notes' => null,
            ]);

            EquipmentAssignment::create([
                'equipment_id' => $this->record->id,
                'assigned_to_user_id' => $userId,
                'assigned_by' => auth()->id(),
                'assigned_at' => $assign['assignment_date'] ?? now(),
                'notes' => $assign['assignment_notes'] ?? null,
                'transmission_sheet_id' => $transmissionSheet->id,
            ]);
        });
    }
    
    /**
     * Nettoie une chaîne UTF-8 en supprimant les caractères malformés
     */
    protected static function cleanUtf8String(?string $string): string
    {
        if (empty($string)) {
            return '';
        }
        
        // D'abord, essayer de réparer avec iconv (ignore les caractères invalides)
        $string = @iconv('UTF-8', 'UTF-8//IGNORE//TRANSLIT', $string);
        if ($string === false) {
            $string = '';
        }
        
        // Nettoyer les caractères de contrôle
        $string = preg_replace('/[\x00-\x08\x0B\x0C\x0E-\x1F\x7F]/u', '', $string);
        
        // Supprimer les séquences UTF-8 invalides
        $string = preg_replace('/[\x{FFFE}\x{FFFF}]/u', '', $string);
        
        // Vérifier et réparer l'encodage
        if (!mb_check_encoding($string, 'UTF-8')) {
            // Essayer de convertir depuis différents encodages
            $encodings = ['ISO-8859-1', 'Windows-1252', 'Windows-1251'];
            foreach ($encodings as $encoding) {
                $converted = @mb_convert_encoding($string, 'UTF-8', $encoding);
                if ($converted !== false && mb_check_encoding($converted, 'UTF-8')) {
                    $string = $converted;
                    break;
                }
            }
            
            // Si toujours invalide, utiliser iconv avec IGNORE
            if (!mb_check_encoding($string, 'UTF-8')) {
                $string = @iconv('UTF-8', 'UTF-8//IGNORE', $string);
                if ($string === false) {
                    $string = '';
                }
            }
        }
        
        return $string;
    }
    
    /**
     * Nettoie récursivement les données d'un modèle pour l'encodage UTF-8
     */
    protected static function cleanUtf8Data($data)
    {
        if (is_string($data)) {
            return static::cleanUtf8String($data);
        }
        
        if (is_array($data)) {
            return array_map([static::class, 'cleanUtf8Data'], $data);
        }
        
        if (is_object($data)) {
            // Si c'est un modèle Eloquent, nettoyer les attributs
            if (method_exists($data, 'getAttributes')) {
                $attributes = $data->getAttributes();
                foreach ($attributes as $key => $value) {
                    if (is_string($value)) {
                        $data->setAttribute($key, static::cleanUtf8String($value));
                    } elseif (is_array($value)) {
                        $data->setAttribute($key, static::cleanUtf8Data($value));
                    }
                }
                
                // Nettoyer aussi les relations chargées
                foreach ($data->getRelations() as $relationName => $relation) {
                    if (is_object($relation)) {
                        $data->setRelation($relationName, static::cleanUtf8Data($relation));
                    } elseif (is_array($relation) || $relation instanceof \Illuminate\Support\Collection) {
                        $cleaned = [];
                        foreach ($relation as $item) {
                            $cleaned[] = static::cleanUtf8Data($item);
                        }
                        $data->setRelation($relationName, is_array($relation) ? $cleaned : collect($cleaned));
                    }
                }
            }
            
            return $data;
        }
        
        return $data;
    }
}
