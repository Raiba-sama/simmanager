<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class EquipmentMaintenance extends Model
{
    use HasFactory;

    protected $fillable = [
        'equipment_id',
        'maintenance_type',
        'description',
        'cost',
        'performed_by',
        'performed_at',
        'next_maintenance_due',
        'created_by',
    ];

    protected $casts = [
        'cost' => 'decimal:2',
        'performed_at' => 'datetime',
        'next_maintenance_due' => 'date',
    ];

    // Relations
    public function equipment(): BelongsTo
    {
        return $this->belongsTo(Equipment::class);
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    // Helpers
    public function getMaintenanceTypeLabelAttribute(): string
    {
        return match($this->maintenance_type) {
            'repair' => 'Réparation',
            'upgrade' => 'Mise à niveau',
            'cleaning' => 'Nettoyage',
            'inspection' => 'Inspection',
            default => $this->maintenance_type,
        };
    }
}
