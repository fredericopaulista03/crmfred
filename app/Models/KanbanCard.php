<?php

namespace App\Models;

use App\Models\KanbanColumn;
use App\Models\User;
use Illuminate\Database\Eloquent\Model;

class KanbanCard extends Model
{
    protected $fillable = [
        'kanban_column_id', 'user_id', 'title', 'description',
        'priority', 'due_date', 'order'
    ];

    protected $casts = [
        'due_date' => 'date',
    ];

    public function column()
    {
        return $this->belongsTo(KanbanColumn::class, 'kanban_column_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
