<?php

namespace App\Http\Controllers;

use App\Models\Requirement;
use App\Models\Todo;
use App\Repositories\ProjectRepository;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class TodoController extends Controller
{
    protected $projectRepo;

    public function __construct(ProjectRepository $projectRepo)
    {
        $this->projectRepo = $projectRepo;
    }

    public function store(Requirement $requirement, Request $request)
    {
        $request->validate(['task' => 'required|min:3|max:1000']);

        $requirement->addTodo($request);

        return back()->with('alert', [
                'class' => 'success',
                'message' => __('form.todo_saved_successfully'),
            ]);
    }

    public function index(Requirement $requirement)
    {
        $todo = new Todo;

        if (! isset($requirement->id)) {
            $projects = $this->projectRepo->todos();
            return view('todos.projectTodos', compact('projects', 'todo'));
        }

        $todos = $requirement->todos()
            ->oldest('completed')
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
            'completed' => $request->completed ? Carbon::now() : null,
        ]);

        return back()->with('alert', [
                'class' => 'success',
                'message' => __('form.todo_saved_successfully'),
            ]);
    }
}
