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
        return Auth::user()
            ->projects()
            ->create(['name' => $projectData->name]);
    }

    public function  all()
    {
        return Auth::user()
            ->projects;
    }
}
