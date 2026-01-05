<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class EquipmentHistory extends Model
{
    use HasFactory;

    public $timestamps = false;

    protected $fillable = [
        'equipment_id',
        'action',
        'old_value',
        'new_value',
        'performed_by',
        'notes',
        'created_at',
    ];

    protected $casts = [
        'old_value' => 'array',
        'new_value' => 'array',
        'created_at' => 'datetime',
    ];

    // Relations
    public function equipment(): BelongsTo
    {
        return $this->belongsTo(Equipment::class);
    }

    public function performer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'performed_by');
    }

    // Helpers
    public function getActionLabelAttribute(): string
    {
        return match($this->action) {
            'created' => 'Créé',
            'assigned' => 'Attribué',
            'returned' => 'Retourné',
            'transferred' => 'Transféré',
            'status_changed' => 'Statut modifié',
            'maintenance' => 'Maintenance',
            'updated' => 'Modifié',
            default => $this->action,
        };
    }
}
