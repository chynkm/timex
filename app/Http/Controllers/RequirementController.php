<?php

namespace App\Http\Controllers;

use App\Models\Requirement;
use Illuminate\Http\Request;

class RequirementController extends Controller
{
    public function update(Requirement $requirement, Request $request)
    {
        if (auth()->user()->isNot($requirement->project->user)) {
            abort(403);
        }

        $request->validate(['name' => 'required|min:3|max:50']);

        $requirement->update(['name' => $request->name]);

        return redirect()->route('projects.show', ['project' => $requirement->project_id]);
    }
}
