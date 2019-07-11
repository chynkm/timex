<?php

namespace App\Http\Controllers;

use App\Models\Requirement;
use App\Models\Todo;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class TodoController extends Controller
{
    public function store(Requirement $requirement, Request $request)
    {
        $request->validate(['task' => 'required|min:3|max:1000']);

        $requirement->addTodo($request);

        return redirect()->route('todos.index', ['requirement' => $requirement->id])
            ->with('alert', [
                'class' => 'success',
                'message' => __('form.todo_saved_successfully'),
            ]);
    }

    public function index(Requirement $requirement)
    {
        $todo = new Todo;
        $todos = Auth::user()
            ->todos()
            ->latest()
            ->paginate(config('env.page_limit'));

        return view('todos.index', compact('todos', 'todo', 'requirement'));
    }

    /**
     * @todo add validation $request->validate(['task' => 'required|min:3|max:1000']);
     */
    public function update(Todo $todo, Request $request)
    {
        $todo->update([
            'task' => $request->task,
            'completed' => $request->completed ? Carbon::now() : null,
        ]);

        return redirect()->route('todos.index', ['requirement' => $todo->requirement_id])
            ->with('alert', [
                'class' => 'success',
                'message' => __('form.todo_saved_successfully'),
            ]);
    }
}
