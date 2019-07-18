<?php

namespace App\Observers;

use App\Models\Todo;

class TodoObserver
{
    /**
     * Handle the todo "updating" event.
     *
     * @param  \App\Todo  $todo
     * @return void
     */
    public function updating(Todo $todo)
    {
        $todoOriginal = $todo->getOriginal();
        $todo->todoHistories()->create([
            'user_id' => $todoOriginal['user_id'],
            'requirement_id' => $todoOriginal['requirement_id'],
            'task' => $todoOriginal['task'],
            'deadline' => $todoOriginal['deadline'],
            'completed' => $todoOriginal['completed'],
        ]);
    }
}
