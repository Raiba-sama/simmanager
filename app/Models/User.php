<?php

namespace App\Models;

use Filament\Models\Contracts\FilamentUser;
use Filament\Panel;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable implements FilamentUser
{
    use HasApiTokens, HasFactory, Notifiable;

    protected $fillable = [
        'matricule',
        'name',
        'first_name',
        'fonction',
        'lieu_affectation',
        'zone_affectation',
        'direction',
        'numero_flotte',
        'email',
        'password',
        'role',
        'active',
        'avatar_url',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
        'active' => 'boolean',
    ];

    // Relations
    public function simRequests()
    {
        return $this->hasMany(SimRequest::class, 'user_id');
    }

    public function validatedRequests()
    {
        return $this->hasMany(SimRequest::class, 'validator_id');
    }

    public function assignedSims()
    {
        return $this->hasMany(Sim::class, 'assigned_to');
    }

    public function favorites()
    {
        return $this->belongsToMany(SimRequest::class, 'favorites', 'user_id', 'sim_request_id')
                    ->withTimestamps();
    }

    // Helpers
    public function isAdmin(): bool
    {
        return $this->role === 'admin';
    }

    public function isValidator(): bool
    {
        return in_array($this->role, ['admin', 'validator']);
    }

    public function canValidateRequests(): bool
    {
        return $this->isValidator();
    }

    public function canManageSims(): bool
    {
        return $this->isAdmin();
    }

    public function getFullNameAttribute(): string
    {
        return trim($this->name . ' ' . ($this->first_name ?? ''));
    }

    public function getAvatarUrlAttribute($value): ?string
    {
        return $value;
    }

    public function getAvatarAttribute(): string
    {
        if ($this->avatar_url) {
            // If it's a local storage path, use asset()
            if (strpos($this->avatar_url, 'storage/') !== false) {
                return asset($this->avatar_url);
            }
            // Otherwise, it's an external URL
            return $this->avatar_url;
        }
        return 'https://ui-avatars.com/api/?name=' . urlencode($this->full_name) . '&background=0d6efd&color=fff';
    }

    /**
     * Détermine si l'utilisateur peut accéder au panel Filament
     */
    public function canAccessPanel(Panel $panel): bool
    {
        // Seuls les admins peuvent accéder au panel admin
        return $this->isAdmin() && $panel->getId() === 'admin';
    }
}
