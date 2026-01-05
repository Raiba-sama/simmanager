<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TransmissionSheetItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'transmission_sheet_id',
        'equipment_id',
        'quantity',
        'condition_at_transmission',
        'notes',
    ];

    protected $casts = [
        'quantity' => 'integer',
    ];

    // Relations
    public function transmissionSheet(): BelongsTo
    {
        return $this->belongsTo(TransmissionSheet::class);
    }

    public function equipment(): BelongsTo
    {
        return $this->belongsTo(Equipment::class);
    }

    // Helpers
    public function getConditionLabelAttribute(): string
    {
        return match($this->condition_at_transmission) {
            'new' => 'Neuf',
            'excellent' => 'Excellent',
            'good' => 'Bon',
            'fair' => 'Moyen',
            'poor' => 'Mauvais',
            default => $this->condition_at_transmission,
        };
    }
}
