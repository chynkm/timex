<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Project extends Model
{
    public $guarded = [];

    public function path()
    {
        return "/projects/{$this->id}";
    }

    public function user()
    {
        return $this->belongsTo('App\Models\User');
    }

    public function requirements()
    {
        return $this->hasMany('App\Models\Requirement');
    }

    public function addRequirement($name)
    {
        return $this->requirements()
            ->create(compact('name'));
    }
}
