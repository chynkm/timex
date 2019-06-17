<?php

namespace App\Repositories;

use App\Models\Project;
use Illuminate\Support\Facades\Auth;

class ProjectRepository
{
    public $requiredFields = ['name'];

    /**
     * @param  object $projectData
     *
     * @return object
     */
    public function save($projectData)
    {
        $project = Auth::user()
                        ->projects()
                        ->create(['name' => $projectData->name]);

        return $project;
    }

    public function all()
    {
        return Project::all();
    }
}
