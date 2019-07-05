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
        return $this->saveUpdate(null, $request);
    }

    public function index()
    {
        $projects = $this->projectRepo->all();

        return view('projects.index', compact('projects'));
    }

    public function create()
    {
        return view('projects.createEdit');
    }

    public function show(Project $project)
    {
        return view('projects.show', compact('project'));
    }

    public function edit(Project $project)
    {
        return view('projects.createEdit', compact('project'));
    }

    public function update(Project $project, Request $request)
    {
        return $this->saveUpdate($project, $request);
    }

    protected function saveUpdate($project, Request $request)
    {
        $request->validate(['name' => 'required|min:3|max:50']);

        $this->projectRepo->save($project, $request);

        return redirect()->route('projects.index');
    }
}
