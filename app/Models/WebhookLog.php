<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class WebhookLog extends Model
{
    protected $fillable = [
        'event_type',
        'payload',
        'status',
        'error_message',
        'processed'
    ];

    protected $casts = [
        'processed' => 'boolean',
    ];

    public function getPayloadDataAttribute()
    {
        return json_decode($this->payload, true);
    }
}
