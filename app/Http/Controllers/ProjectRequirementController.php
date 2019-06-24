<?php

namespace App\Http\Controllers;

use App\Models\Project;
use Illuminate\Http\Request;

class ProjectRequirementController extends Controller
{
    public function store(Project $project, Request $request)
    {
        if (auth()->user()->isNot($project->user)) {
            abort(403);
        }

        $request->validate(['name' => 'required|min:3|max:50']);
        $project->addRequirement($request->name);

        return redirect()->route('projects.show', ['project' => $project->id]);
    }
}
