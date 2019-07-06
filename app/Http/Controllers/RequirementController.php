<?php

namespace App\Http\Controllers;

use App\Http\Requests\RequirementSave;
use App\Models\Project;
use App\Models\Requirement;
use Illuminate\Http\Request;

class RequirementController extends Controller
{
    public function update(Requirement $requirement, RequirementSave $request)
    {
        $requirement->update(['name' => $request->name]);

        return redirect()->route('projects.show', ['project' => $requirement->project_id]);
    }

    public function create(Project $project)
    {
        return view('requirements.createEdit', compact('project'));
    }

    public function store(Project $project, RequirementSave $request)
    {
        $project->addRequirement($request->name);

        return redirect()->route('projects.show', ['project' => $project->id]);
    }

    public function edit(Requirement $requirement)
    {
        return view('requirements.createEdit', compact('project', 'requirement'));
    }

    public function projectRequirement(Project $project)
    {
        $requirements = $project->requirements
            ->sortBy('name')
            ->pluck('name', 'id');

        return response()->json([
            'status' => true,
            'html' => view('requirements.projectRequirement', compact('requirements'))->render()
        ]);
    }

}
