<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class EquipmentDischarge extends Model
{
    use HasFactory;

    protected $fillable = [
        'discharge_number',
        'user_id',
        'generated_by',
        'reason',
        'effective_date',
        'notes',
        'equipment_snapshot',
    ];

    protected $casts = [
        'effective_date' => 'date',
        'equipment_snapshot' => 'array',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function generator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'generated_by');
    }
}

