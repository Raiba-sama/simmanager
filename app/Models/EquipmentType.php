<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class EquipmentType extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'category',
        'description',
    ];

    protected $casts = [
        'category' => 'string',
    ];

    // Relations
    public function equipment(): HasMany
    {
        return $this->hasMany(Equipment::class);
    }

    // Scopes
    public function scopeActive($query)
    {
        return $query;
    }

    // Helpers
    public function getCategoryLabelAttribute(): string
    {
        return match($this->category) {
            'computer' => 'Ordinateur',
            'peripheral' => 'Périphérique',
            'network' => 'Réseau',
            'accessory' => 'Accessoire',
            default => $this->category,
        };
    }
}
