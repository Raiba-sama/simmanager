<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Sim extends Model
{
    use HasFactory;

    protected $fillable = [
        'iccid',
        'phone_number',
        'status',
        'operator',
        'plan_type',
        'monthly_cost',
        'assigned_to',
        'assigned_to_matricule',
        'assigned_at',
        'metadata',
    ];

    protected $casts = [
        'monthly_cost' => 'decimal:2',
        'assigned_at' => 'datetime',
        'metadata' => 'array',
    ];

    // Relations
    public function assignedUser()
    {
        return $this->belongsTo(User::class, 'assigned_to');
    }

    public function requests()
    {
        return $this->hasMany(SimRequest::class);
    }

    public function histories()
    {
        return $this->hasMany(SimHistory::class);
    }

    // Scopes
    public function scopeLibre($query)
    {
        return $query->where('status', 'libre');
    }

    public function scopeAttribue($query)
    {
        return $query->where('status', 'attribue');
    }

    public function scopeSuspendu($query)
    {
        return $query->where('status', 'suspendu');
    }

    public function scopeDefectueuse($query)
    {
        return $query->where('status', 'defectueuse');
    }

    // Helpers
    public function isLibre(): bool
    {
        return $this->status === 'libre';
    }

    public function isAttribue(): bool
    {
        return $this->status === 'attribue';
    }

    public function isSuspendu(): bool
    {
        return $this->status === 'suspendu';
    }

    public function isDefectueuse(): bool
    {
        return $this->status === 'defectueuse';
    }
}

