<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Mission extends Model
{
    protected static function booted(): void
    {
        static::saving(function (Mission $mission) {
            if ($mission->start_date && $mission->end_date && $mission->number_of_days === null) {
                $mission->number_of_days = $mission->start_date->diffInDays($mission->end_date) + 1;
            }
        });
    }

    protected $fillable = [
        'title',
        'type',
        'agency_id',
        'start_date',
        'end_date',
        'number_of_days',
        'status',
        'description',
        'created_by',
    ];

    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date',
    ];

    public const TYPES = [
        'formation' => 'Formation',
        'audit' => 'Audit',
        'support_technique' => 'Support technique',
        'deploiement' => 'Déploiement',
        'maintenance' => 'Maintenance',
        'autre' => 'Autre',
    ];

    public const STATUSES = [
        'planned' => 'Planifiée',
        'in_progress' => 'En cours',
        'completed' => 'Terminée',
        'cancelled' => 'Annulée',
    ];

    public function agency(): BelongsTo
    {
        return $this->belongsTo(Agency::class);
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /** Admins / participants à la mission */
    public function users(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'mission_user')
            ->withPivot('role_in_mission')
            ->withTimestamps();
    }

    public function getNumberOfDaysAttribute($value): ?int
    {
        if ($value !== null && $value !== '') {
            return (int) $value;
        }
        if ($this->start_date && $this->end_date) {
            return $this->start_date->diffInDays($this->end_date) + 1;
        }
        return null;
    }
}
