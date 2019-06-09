<?php

namespace App\Repositories;

use App\Models\Project;

class ProjectRepository
{
    public $requiredFields = ['name'];

    /**
     * @todo save from HTML array
     *
     * @param  object $projectData
     *
     * @return object|boolean
     */
    public function save($projectData)
    {
        /*$project = new Project();
        if ($project->validate($projectData) === false) {
            return false;
        }*/

        $project = new Project();
        $project->name = $projectData->name;
        $project->save();

        return $project;
    }

    public function all()
    {
        return Project::all();
    }
}
