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
            ->projects()
            ->orderBy('name')
            ->paginate(config('env.page_limit'));
    }

    public function todos()
    {
        return Auth::user()
            ->projects()
            ->with([
                'requirements' => function($query) {
                    $query->latest('updated_at');
                },
                'requirements.todos' => function($query) {
                    $query->oldest('completed')
                        ->latest();
                }
            ])
            ->latest('updated_at')
            ->get();
    }
}
