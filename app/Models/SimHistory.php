<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SimHistory extends Model
{
    use HasFactory;

    protected $fillable = [
        'sim_id',
        'action',
        'user_id',
        'user_matricule',
        'request_id',
        'old_data',
        'new_data',
        'notes',
    ];

    protected $casts = [
        'old_data' => 'array',
        'new_data' => 'array',
    ];

    // Relations
    public function sim()
    {
        return $this->belongsTo(Sim::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function request()
    {
        return $this->belongsTo(SimRequest::class, 'request_id');
    }

    /**
     * Obtenir le label traduit de l'action
     */
    public function getActionLabelAttribute(): string
    {
        $labels = [
            'created' => 'Création',
            'assigned' => 'Attribution',
            'unassigned' => 'Libération',
            'status_changed' => 'Changement de statut',
            'validated' => 'Validation',
            'rejected' => 'Rejet',
            'cancelled' => 'Annulation',
            'submitted_to_webhook' => 'Envoi au webhook',
            'status_updated' => 'Mise à jour du statut',
            'approved' => 'Approuvée',
            'deleted' => 'Suppression',
        ];

        return $labels[$this->action] ?? ucfirst($this->action);
    }

    /**
     * Obtenir un résumé des changements
     */
    public function getChangesSummaryAttribute(): ?string
    {
        if (!$this->old_data || !$this->new_data) {
            return null;
        }

        $changes = [];
        
        // Changement de statut
        if (isset($this->old_data['status']) && isset($this->new_data['status']) && 
            $this->old_data['status'] !== $this->new_data['status']) {
            $statusLabels = [
                'en_attente' => 'En attente',
                'validee' => 'Validée',
                'rejetee' => 'Rejetée',
                'pending' => 'Pending',
                'accepted' => 'Acceptée',
                'refused' => 'Refusée',
                'demande_envoyee' => 'Demande envoyée',
            ];
            $oldLabel = $statusLabels[$this->old_data['status']] ?? $this->old_data['status'];
            $newLabel = $statusLabels[$this->new_data['status']] ?? $this->new_data['status'];
            $changes[] = "Statut: {$oldLabel} → {$newLabel}";
        }

        // Changement d'assignation
        if (isset($this->old_data['assigned_to']) && isset($this->new_data['assigned_to']) && 
            $this->old_data['assigned_to'] !== $this->new_data['assigned_to']) {
            if ($this->new_data['assigned_to']) {
                $changes[] = "Assignée à un utilisateur";
            } else {
                $changes[] = "Désassignée";
            }
        }

        // Raison de rejet
        if (isset($this->new_data['rejection_reason']) && !empty($this->new_data['rejection_reason'])) {
            $changes[] = "Raison: " . \Illuminate\Support\Str::limit($this->new_data['rejection_reason'], 30);
        }

        // Commentaire admin
        if (isset($this->new_data['admin_comment']) && !empty($this->new_data['admin_comment'])) {
            $changes[] = "Commentaire: " . \Illuminate\Support\Str::limit($this->new_data['admin_comment'], 30);
        }

        return !empty($changes) ? implode(' | ', $changes) : null;
    }
}

