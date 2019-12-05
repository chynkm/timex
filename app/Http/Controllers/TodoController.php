<?php

namespace App\Http\Controllers;

use App\Http\Requests\TodoSaveRequest;
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

    public function store(Requirement $requirement, TodoSaveRequest $request)
    {
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
            ->orderByRaw('completed is NOT NULL, completed DESC');

        if (config('app.env') != "testing") {
            $todos = $todos->orderByRaw('FIELD(impact, "high", "medium", "low")')
                ->orderByRaw('FIELD(complexity, "easy", "medium", "hard")');
        }

        $todos = $todos->latest('updated_at')
            ->paginate(config('env.page_limit'));

        return view('todos.index', compact('todos', 'todo', 'requirement'));
    }

    public function update(Todo $todo, TodoSaveRequest $request)
    {
        $todo->update([
            'completed' => $request->completed ? Carbon::now() : null,
            'task' => $request->task,
            'impact' => $request->impact,
            'complexity' => $request->complexity,
        ]);

        return back()->with('alert', [
                'class' => 'success',
                'message' => __('form.todo_saved_successfully'),
            ]);
    }
}
