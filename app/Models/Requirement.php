<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Requirement extends Model
{
    protected $guarded = [];

    protected $touches = ['project'];

    public function project()
    {
        return $this->belongsTo('App\Models\Project');
    }
}
