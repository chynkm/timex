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

    public function create(Request $request)
    {
        $request->validate(['name' => 'required']);
        $this->projectRepo->save($request);

        return redirect()->route('projects.index');
    }

    public function index()
    {
        $projects = $this->projectRepo->all();

        return view('projects.index', compact('projects'));
    }

    public function show(Project $project)
    {
        return view('projects.show', compact('project'));
    }
}
