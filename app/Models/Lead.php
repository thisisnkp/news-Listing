<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Lead extends Model
{
    protected $fillable = [
        'source',
        'name',
        'phone',
        'email',
        'service',
        'subject',
        'message',
        'dedupe_key',
        'mail_send',
        'form_resubmit',
        'resubmit_count',
        'last_mail_sent_at',
    ];

    protected $casts = [
        'mail_send'         => 'boolean',
        'form_resubmit'     => 'boolean',
        'resubmit_count'    => 'integer',
        'last_mail_sent_at' => 'datetime',
    ];

    public function scopeLatestFirst($q)
    {
        return $q->orderByDesc('created_at')->orderByDesc('id');
    }
}
