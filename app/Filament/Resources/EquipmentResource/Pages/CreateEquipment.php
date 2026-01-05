<?php

namespace App\Filament\Resources\EquipmentResource\Pages;

use App\Filament\Resources\EquipmentResource;
use App\Models\EquipmentAssignment;
use App\Models\TransmissionSheet;
use App\Models\TransmissionSheetItem;
use App\Models\User;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class CreateEquipment extends CreateRecord
{
    protected static string $resource = EquipmentResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $data['created_by'] = auth()->id();
        return $data;
    }

    protected function afterCreate(): void
    {
        $data = $this->form->getState();
        
        // Si le statut est "attribué", créer l'attribution et le bordereau
        if (($data['status'] ?? null) === 'assigned') {
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
                        'notes' => $data['assignment_notes'] ?? 'Attribution automatique lors de la création de l\'équipement',
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
