<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TimeEntry extends Model
{
    protected $guarded = [];

    public function requirement()
    {
        return $this->belongsTo('App\Models\Requirement');
    }
}
