<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MailSent extends Model
{
    use HasFactory;

    protected $table = 'mail_sent';

    protected $fillable = [
        'thread_id',
        'label_ids',
        'request_type',
        'request_number',
        'request_matricule',
        'request_name',
        'sim_iccid',
        'sim_assign_to',
        'message_subject',
        'message_corps',
        'request_id',
        'operator_response',
        'status_after_sent',
    ];

    protected $casts = [
        'label_ids' => 'array',
    ];

    // Relations
    public function simRequest()
    {
        return $this->belongsTo(SimRequest::class, 'request_id');
    }
}
