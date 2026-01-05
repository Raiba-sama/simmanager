<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class EquipmentAssignment extends Model
{
    use HasFactory;

    protected $fillable = [
        'equipment_id',
        'assigned_to_user_id',
        'assigned_to_agency_id',
        'assigned_by',
        'assigned_at',
        'returned_at',
        'return_reason',
        'notes',
        'transmission_sheet_id',
    ];

    protected $casts = [
        'assigned_at' => 'datetime',
        'returned_at' => 'datetime',
    ];

    // Relations
    public function equipment(): BelongsTo
    {
        return $this->belongsTo(Equipment::class);
    }

    public function assignedToUser(): BelongsTo
    {
        return $this->belongsTo(User::class, 'assigned_to_user_id');
    }

    public function assignedToAgency(): BelongsTo
    {
        return $this->belongsTo(Agency::class, 'assigned_to_agency_id');
    }

    public function assignedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'assigned_by');
    }

    public function transmissionSheet(): BelongsTo
    {
        return $this->belongsTo(TransmissionSheet::class);
    }

    // Scopes
    public function scopeActive($query)
    {
        return $query->whereNull('returned_at');
    }

    public function scopeReturned($query)
    {
        return $query->whereNotNull('returned_at');
    }

    // Helpers
    public function isActive(): bool
    {
        return is_null($this->returned_at);
    }

    public function getAssigneeAttribute()
    {
        return $this->assignedToUser ?? $this->assignedToAgency;
    }
}
