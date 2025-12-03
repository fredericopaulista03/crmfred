<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class KanbanColumn extends Model
{
    protected $fillable = ['title', 'order', 'color'];

    public function cards()
    {
        return $this->hasMany(KanbanCard::class)->orderBy('order');
    }
}
