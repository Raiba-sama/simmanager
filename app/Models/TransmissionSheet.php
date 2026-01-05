<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class TransmissionSheet extends Model
{
    use HasFactory;

    protected $fillable = [
        'sheet_number',
        'type',
        'from_user_id',
        'from_agency_id',
        'to_user_id',
        'to_agency_id',
        'created_by',
        'transmission_date',
        'status',
        'notes',
        'signed_by_recipient',
        'signed_at',
        'recipient_signature',
    ];

    protected $casts = [
        'transmission_date' => 'date',
        'signed_by_recipient' => 'boolean',
        'signed_at' => 'datetime',
    ];

    // Relations
    public function fromUser(): BelongsTo
    {
        return $this->belongsTo(User::class, 'from_user_id');
    }

    public function fromAgency(): BelongsTo
    {
        return $this->belongsTo(Agency::class, 'from_agency_id');
    }

    public function toUser(): BelongsTo
    {
        return $this->belongsTo(User::class, 'to_user_id');
    }

    public function toAgency(): BelongsTo
    {
        return $this->belongsTo(Agency::class, 'to_agency_id');
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function items(): HasMany
    {
        return $this->hasMany(TransmissionSheetItem::class);
    }

    public function assignments(): HasMany
    {
        return $this->hasMany(EquipmentAssignment::class);
    }

    // Scopes
    public function scopeDraft($query)
    {
        return $query->where('status', 'draft');
    }

    public function scopeCompleted($query)
    {
        return $query->where('status', 'completed');
    }

    // Helpers
    public function getTypeLabelAttribute(): string
    {
        return match($this->type) {
            'assignment' => 'Attribution',
            'return' => 'Retour',
            'transfer' => 'Transfert',
            default => $this->type,
        };
    }

    public function getStatusLabelAttribute(): string
    {
        return match($this->status) {
            'draft' => 'Brouillon',
            'pending' => 'En attente',
            'completed' => 'Complété',
            'cancelled' => 'Annulé',
            default => $this->status,
        };
    }

    public function getFromAttribute()
    {
        return $this->fromUser ?? $this->fromAgency;
    }

    public function getToAttribute()
    {
        return $this->toUser ?? $this->toAgency;
    }

    public function isDraft(): bool
    {
        return $this->status === 'draft';
    }

    public function isCompleted(): bool
    {
        return $this->status === 'completed';
    }
}
