<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Repositories\ProjectRepository;
use App\Models\Project;

class ProjectController extends Controller
{
    protected $projectRepo;

    public function __construct(ProjectRepository $projectRepo)
    {
        $this->projectRepo = $projectRepo;
    }

    public function store(Request $request)
    {
        $request->validate(['name' => 'required|min:3|max:50']);

        $this->projectRepo->save($request);

        return redirect()->route('projects.index');
    }

    public function index()
    {
        $projects = $this->projectRepo->all();

        return view('projects.index', compact('projects'));
    }

    public function create()
    {
        return view('projects.create');
    }

    public function show(Project $project)
    {
        if (auth()->user()->isNot($project->user)) {
            return redirect()->route('projects.index')
                ->with('alert', [
                    'class' => 'warning',
                    'message' => __('form.requested_project_not_found'),
                ]);
        }

        return view('projects.show', compact('project'));
    }
}
