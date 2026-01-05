<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Zone extends Model
{
    use HasFactory;

    protected $fillable = [
        'code',
        'name',
        'description',
        'active',
    ];

    protected $casts = [
        'active' => 'boolean',
    ];

    // Relations
    public function agencies(): HasMany
    {
        return $this->hasMany(Agency::class);
    }

    // Scopes
    public function scopeActive($query)
    {
        return $query->where('active', true);
    }
}
