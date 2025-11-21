<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SimRequest extends Model
{
    use HasFactory;

    protected $fillable = [
        'request_number',
        'user_id',
        'sim_id',
        'requested_iccid',
        'phone_number',
        'request_type',
        'plan_id',
        'limite_credit',
        'limite_data',
        'motif',
        'justification',
        'priority',
        'status',
        'validator_id',
        'validated_at',
        'rejection_reason',
        'admin_comment',
        'admin_id',
        'admin_processed_at',
        'beneficiary_name',
        'beneficiary_first_name',
        'beneficiary_fonction',
        'beneficiary_matricule',
        'request_details',
        'created_by',
        'updated_by',
    ];

    protected $casts = [
        'validated_at' => 'datetime',
        'admin_processed_at' => 'datetime',
        'limite_credit' => 'decimal:2',
        'limite_data' => 'decimal:2',
        'request_details' => 'array',
    ];

    // Relations
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function sim()
    {
        return $this->belongsTo(Sim::class);
    }

    public function validator()
    {
        return $this->belongsTo(User::class, 'validator_id');
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function updater()
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    public function admin()
    {
        return $this->belongsTo(User::class, 'admin_id');
    }

    public function plan()
    {
        return $this->belongsTo(Plan::class);
    }

    public function histories()
    {
        return $this->hasMany(SimHistory::class, 'request_id');
    }

    public function favoritedBy()
    {
        return $this->belongsToMany(User::class, 'favorites', 'sim_request_id', 'user_id')
                    ->withTimestamps();
    }

    // Scopes
    public function scopeEnAttente($query)
    {
        return $query->where('status', 'en_attente');
    }

    public function scopeValidee($query)
    {
        return $query->where('status', 'validee');
    }

    public function scopeRejetee($query)
    {
        return $query->where('status', 'rejetee');
    }

    public function scopePendingMoreThanDays($query, int $days)
    {
        return $query->where('status', 'en_attente')
            ->where('created_at', '<=', now()->subDays($days));
    }

    // Helpers
    public function isEnAttente(): bool
    {
        return $this->status === 'en_attente';
    }

    public function isValidee(): bool
    {
        return $this->status === 'validee';
    }

    public function isRejetee(): bool
    {
        return $this->status === 'rejetee';
    }

    public function isDemandeEnvoyee(): bool
    {
        return $this->status === 'demande_envoyee';
    }

    public function isPending(): bool
    {
        return $this->status === 'pending';
    }

    public function isRefused(): bool
    {
        return $this->status === 'refused';
    }

    public function isAccepted(): bool
    {
        return $this->status === 'accepted';
    }

    public function isRecuperation(): bool
    {
        return $this->request_type === 'recuperation';
    }

    public function isAjustement(): bool
    {
        return $this->request_type === 'ajustement';
    }

    public function isDesactivation(): bool
    {
        return $this->request_type === 'desactivation';
    }

    public function isSuspension(): bool
    {
        return $this->request_type === 'suspension';
    }

    public function isCreation(): bool
    {
        return $this->request_type === 'creation';
    }

    public function getDaysPendingAttribute(): int
    {
        if ($this->status !== 'en_attente') {
            return 0;
        }
        return now()->diffInDays($this->created_at);
    }

    public function isOverdue(int $days = 3): bool
    {
        return $this->status === 'en_attente' && $this->days_pending >= $days;
    }

    public function getUrgencyLevelAttribute(): string
    {
        if (!$this->isEnAttente()) {
            return 'none';
        }
        
        $days = $this->days_pending;
        if ($days >= 7) {
            return 'urgent';
        } elseif ($days >= 5) {
            return 'high';
        } elseif ($days >= 3) {
            return 'normal';
        }
        return 'low';
    }

    public function needsValidation(): bool
    {
        // Seule la récupération nécessite une validation
        return $this->isRecuperation();
    }

    public function isDirectSubmission(): bool
    {
        // Les autres types sont soumis directement par le validator
        return !$this->needsValidation();
    }

    public static function generateRequestNumber(): string
    {
        $date = now()->format('dmy');
        $lastRequest = self::whereDate('created_at', today())
            ->orderBy('id', 'desc')
            ->first();

        if ($lastRequest && preg_match('/-(\d+)$/', $lastRequest->request_number, $matches)) {
            $counter = (int) $matches[1] + 1;
        } else {
            $counter = 1;
        }

        return $date . '-' . str_pad($counter, 2, '0', STR_PAD_LEFT);
    }
}

