<?php

namespace App\Repositories;

use App\Models\Project;
use Illuminate\Support\Facades\Auth;

class ProjectRepository
{
    public function save($project, $projectData)
    {
        if (is_null($project)) {
            return Auth::user()
                ->projects()
                ->create(['name' => $projectData->name]);
        }

        return $project->update(['name' => $projectData->name]);
    }

    public function all()
    {
        return Auth::user()
            ->projects;
    }
}
