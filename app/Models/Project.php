<?php

namespace App\Models;

class Project extends MyModel
{
    protected $rules = [
        'name' => 'required|min:3|max:50',
    ];
}
