<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Agency extends Model
{
    use HasFactory;

    protected $fillable = [
        'code',
        'name',
        'address',
        'phone',
        'email',
        'zone_id',
        'active',
    ];

    protected $casts = [
        'active' => 'boolean',
    ];

    // Relations
    public function zone(): BelongsTo
    {
        return $this->belongsTo(Zone::class);
    }

    public function equipmentAssignments(): HasMany
    {
        return $this->hasMany(EquipmentAssignment::class, 'assigned_to_agency_id');
    }

    public function transmissionSheetsFrom(): HasMany
    {
        return $this->hasMany(TransmissionSheet::class, 'from_agency_id');
    }

    public function transmissionSheetsTo(): HasMany
    {
        return $this->hasMany(TransmissionSheet::class, 'to_agency_id');
    }

    // Scopes
    public function scopeActive($query)
    {
        return $query->where('active', true);
    }

    // Helpers
    public function getFullNameAttribute(): string
    {
        return $this->code . ' - ' . $this->name;
    }
}
