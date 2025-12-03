<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Conversation; // Added for relationship
use App\Models\User; // Added for relationship

class Message extends Model
{
    protected $fillable = [
        'conversation_id', 'sender_type', 'sender_id', 'type', 'body', 'media_url', 'status'
    ];

    public function conversation()
    {
        return $this->belongsTo(Conversation::class);
    }

    public function sender()
    {
        return $this->belongsTo(User::class, 'sender_id');
    }
}
