<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

class Requirement extends Model
{
    protected $guarded = [];

    protected $touches = ['project'];

    public function project()
    {
        return $this->belongsTo('App\Models\Project');
    }

    public function todos()
    {
        return $this->hasMany('App\Models\Todo');
    }

    public function addTodo($todoData)
    {
        return $this->todos()
            ->create([
                'user_id' => Auth::id(),
                'task' => $todoData->task,
                'completed' => $todoData->completed ? Carbon::now() : null,
            ]);
    }
}
