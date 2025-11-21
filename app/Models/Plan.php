<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Plan extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'description',
        'limite_credit',
        'limite_data',
        'monthly_cost',
        'operator',
        'active',
    ];

    protected $casts = [
        'limite_credit' => 'decimal:2',
        'limite_data' => 'decimal:2',
        'monthly_cost' => 'decimal:2',
        'active' => 'boolean',
    ];

    // Relations
    public function requests()
    {
        return $this->hasMany(SimRequest::class);
    }

    // Scopes
    public function scopeActive($query)
    {
        return $query->where('active', true);
    }

    public function scopeByOperator($query, $operator)
    {
        return $query->where('operator', $operator);
    }

    // Helpers
    public function getFormattedNameAttribute(): string
    {
        return "{$this->name} - {$this->limite_credit} ariary / {$this->limite_data} GB";
    }
}
