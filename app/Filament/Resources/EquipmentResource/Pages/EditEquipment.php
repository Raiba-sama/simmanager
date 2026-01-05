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
use Illuminate\Support\Str;
use Barryvdh\DomPDF\Facade\Pdf;

class EditEquipment extends EditRecord
{
    protected static string $resource = EquipmentResource::class;

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
                    
                    $transmissionSheet = $assignment->transmissionSheet->load(['toUser', 'fromUser', 'toAgency', 'fromAgency', 'creator', 'items.equipment.equipmentType']);
                    
                    $html = view('transmission-sheets.pdf', compact('transmissionSheet'))->render();
                    
                    // Nettoyer l'encodage
                    $html = mb_convert_encoding($html, 'UTF-8', 'UTF-8');
                    
                    $pdf = Pdf::loadHTML($html);
                    $pdf->setOption('encoding', 'utf-8');
                    $pdf->setOption('defaultFont', 'DejaVu Sans');
                    
                    return $pdf->download('bordereau_' . $transmissionSheet->sheet_number . '.pdf');
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
        // Ne pas sauvegarder les champs temporaires
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

    protected function afterSave(): void
    {
        $data = $this->form->getState();
        $oldStatus = $this->record->getOriginal('status');
        $newStatus = $data['status'] ?? $this->record->status;
        
        // Si le statut passe à "attribué" et qu'il n'y a pas encore d'attribution active
        if ($newStatus === 'assigned' && $oldStatus !== 'assigned') {
            $hasActiveAssignment = $this->record->assignments()->whereNull('returned_at')->exists();
            
            if (!$hasActiveAssignment) {
                $userId = null;
                
                // Si on doit créer un nouvel utilisateur
                if ($data['create_new_user'] ?? false) {
                    $user = User::create([
                        'matricule' => $data['new_user_matricule'],
                        'name' => $data['new_user_name'],
                        'first_name' => $data['new_user_first_name'] ?? null,
                        'email' => $data['new_user_email'],
                        'fonction' => $data['new_user_fonction'] ?? null,
                        'password' => bcrypt('password'), // Mot de passe par défaut
                        'role' => 'user',
                        'active' => true,
                    ]);
                    $userId = $user->id;
                } elseif (!empty($data['assigned_to_user_id'])) {
                    $userId = $data['assigned_to_user_id'];
                }
                
                if ($userId) {
                    DB::transaction(function () use ($userId, $data) {
                        // Créer le bordereau de transmission
                        $sheetNumber = 'BT-' . date('Y') . '-' . strtoupper(Str::random(6));
                        $transmissionSheet = TransmissionSheet::create([
                            'sheet_number' => $sheetNumber,
                            'type' => 'assignment',
                            'to_user_id' => $userId,
                            'created_by' => auth()->id(),
                            'transmission_date' => $data['assignment_date'] ?? now(),
                            'status' => 'completed',
                            'notes' => $data['assignment_notes'] ?? 'Attribution automatique lors de la modification de l\'équipement',
                        ]);
                        
                        // Créer l'item du bordereau
                        TransmissionSheetItem::create([
                            'transmission_sheet_id' => $transmissionSheet->id,
                            'equipment_id' => $this->record->id,
                            'quantity' => 1,
                            'condition_at_transmission' => $this->record->condition ?? 'good',
                            'notes' => null,
                        ]);
                        
                        // Créer l'attribution
                        EquipmentAssignment::create([
                            'equipment_id' => $this->record->id,
                            'assigned_to_user_id' => $userId,
                            'assigned_by' => auth()->id(),
                            'assigned_at' => $data['assignment_date'] ?? now(),
                            'notes' => $data['assignment_notes'] ?? null,
                            'transmission_sheet_id' => $transmissionSheet->id,
                        ]);
                    });
                }
            }
        }
    }
}
