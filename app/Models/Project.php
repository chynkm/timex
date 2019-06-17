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
}
