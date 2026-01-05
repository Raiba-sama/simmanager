<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Equipment extends Model
{
    use HasFactory;

    protected $fillable = [
        'equipment_type_id',
        'brand',
        'model',
        'serial_number',
        'asset_tag',
        'mac_address',
        'ip_address',
        'purchase_date',
        'purchase_price',
        'warranty_expires_at',
        'status',
        'condition',
        'specifications',
        'notes',
        'created_by',
    ];

    protected $casts = [
        'purchase_date' => 'date',
        'purchase_price' => 'decimal:2',
        'warranty_expires_at' => 'date',
        'specifications' => 'array',
        'status' => 'string',
        'condition' => 'string',
    ];

    // Relations
    public function equipmentType(): BelongsTo
    {
        return $this->belongsTo(EquipmentType::class);
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function assignments(): HasMany
    {
        return $this->hasMany(EquipmentAssignment::class);
    }

    public function currentAssignment(): HasOne
    {
        return $this->hasOne(EquipmentAssignment::class)->whereNull('returned_at')->latest();
    }

    public function maintenance(): HasMany
    {
        return $this->hasMany(EquipmentMaintenance::class);
    }

    public function history(): HasMany
    {
        return $this->hasMany(EquipmentHistory::class);
    }

    public function transmissionSheetItems(): HasMany
    {
        return $this->hasMany(TransmissionSheetItem::class);
    }

    // Scopes
    public function scopeAvailable($query)
    {
        return $query->where('status', 'available');
    }

    public function scopeAssigned($query)
    {
        return $query->where('status', 'assigned');
    }

    public function scopeInMaintenance($query)
    {
        return $query->where('status', 'maintenance');
    }

    // Helpers
    public function getStatusLabelAttribute(): string
    {
        return match($this->status) {
            'available' => 'Disponible',
            'assigned' => 'Attribué',
            'maintenance' => 'En maintenance',
            'retired' => 'Retiré',
            'lost' => 'Perdu',
            'damaged' => 'Endommagé',
            default => $this->status,
        };
    }

    public function getConditionLabelAttribute(): string
    {
        return match($this->condition) {
            'new' => 'Neuf',
            'excellent' => 'Excellent',
            'good' => 'Bon',
            'fair' => 'Moyen',
            'poor' => 'Mauvais',
            default => $this->condition,
        };
    }

    public function isAvailable(): bool
    {
        return $this->status === 'available';
    }

    public function isAssigned(): bool
    {
        return $this->status === 'assigned';
    }

    public function getCurrentAssignee()
    {
        $assignment = $this->currentAssignment;
        if ($assignment) {
            return $assignment->assignedToUser ?? $assignment->assignedToAgency;
        }
        return null;
    }
}
