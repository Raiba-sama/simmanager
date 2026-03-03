<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class EquipmentRepairSendout extends Model
{
    use HasFactory;

    protected $table = 'equipment_repair_sendouts';

    protected $fillable = [
        'equipment_id',
        'supplier_name',
        'supplier_reference',
        'sent_at',
        'expected_return_at',
        'returned_at',
        'status',
        'notes',
        'created_by',
    ];

    protected $casts = [
        'sent_at' => 'date',
        'expected_return_at' => 'date',
        'returned_at' => 'date',
    ];

    public const STATUS_SENT = 'sent';
    public const STATUS_IN_REPAIR = 'in_repair';
    public const STATUS_RETURNED = 'returned';

    public function equipment(): BelongsTo
    {
        return $this->belongsTo(Equipment::class);
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function isReturned(): bool
    {
        return $this->status === self::STATUS_RETURNED || $this->returned_at !== null;
    }

    public function getStatusLabelAttribute(): string
    {
        return match ($this->status) {
            self::STATUS_SENT => 'Envoyé',
            self::STATUS_IN_REPAIR => 'En réparation',
            self::STATUS_RETURNED => 'Retourné',
            default => $this->status,
        };
    }

    protected static function booted(): void
    {
        static::creating(function (self $model): void {
            if ($model->created_by === null && auth()->check()) {
                $model->created_by = auth()->id();
            }
        });
    }
}
