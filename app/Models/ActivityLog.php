<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ActivityLog extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_identifier',
        'action',
        'table_name',
        'record_id',
        'request_payload',
        'response_payload',
        'ip_address',
        'user_agent',
        'description',
    ];

    protected $casts = [
        'request_payload' => 'array',
        'response_payload' => 'array',
    ];

    // Scopes
    public function scopeForUser($query, $identifier)
    {
        return $query->where('user_identifier', $identifier);
    }

    public function scopeForAction($query, $action)
    {
        return $query->where('action', $action);
    }

    public function scopeForTable($query, $table)
    {
        return $query->where('table_name', $table);
    }

    public function scopeDateRange($query, $start, $end)
    {
        return $query->whereBetween('created_at', [$start, $end]);
    }
}

