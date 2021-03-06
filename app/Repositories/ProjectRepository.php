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
            ->projects
            ->sortBy('name');
    }

    public function paginated()
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
                    if (config('app.env') == "testing") {
                        $query->orderByRaw('completed is NOT NULL, completed DESC')
                            ->latest('updated_at');
                    } else {
                        $query->orderByRaw('completed is NOT NULL, completed DESC')
                            ->orderByRaw('FIELD(impact, "high", "medium", "low")')
                            ->orderByRaw('FIELD(complexity, "easy", "medium", "hard")')
                            ->latest('updated_at');
                    }
                }
            ])
            ->latest('updated_at')
            ->get();
    }
}
