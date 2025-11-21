<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TransactionLogActivity extends Model
{
    use HasFactory;

    protected $fillable = [
        'transaction_id',
        'user_id',
        'user_matricule',
        'type',
        'status',
        'amount',
        'currency',
        'request_payload',
        'response_payload',
        'ip_address',
        'user_agent',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'request_payload' => 'array',
        'response_payload' => 'array',
    ];

    // Relations
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}

