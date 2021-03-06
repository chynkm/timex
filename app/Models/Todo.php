<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Todo extends Model
{
    protected $guarded = [];

    protected $touches = ['requirement', 'project'];

    public function requirement()
    {
        return $this->belongsTo('App\Models\Requirement');
    }

    public function user()
    {
        return $this->belongsTo('App\Models\User');
    }

    public function project()
    {
        return $this->requirement->project();
    }

    public function todoHistories()
    {
        return $this->hasMany('App\Models\TodoHistory');
    }
}
