<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Todo extends Model
{
    protected $guarded = [];

    public function requirement()
    {
        return $this->belongsTo('App\Models\Requirement');
    }

    public function user()
    {
        return $this->belongsTo('App\Models\User');
    }
}
