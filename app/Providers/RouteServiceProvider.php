<?php

namespace App\Providers;

use App\Models\Project;
use App\Models\Requirement;
use App\Models\TimeEntry;
use App\Models\Todo;
use Illuminate\Foundation\Support\Providers\RouteServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Route;

class RouteServiceProvider extends ServiceProvider
{
    /**
     * This namespace is applied to your controller routes.
     *
     * In addition, it is set as the URL generator's root namespace.
     *
     * @var string
     */
    protected $namespace = 'App\Http\Controllers';

    /**
     * Define your route model bindings, pattern filters, etc.
     *
     * @return void
     */
    public function boot()
    {
        parent::boot();

        Route::bind('project', function ($value) {
            $project = Project::find($value);

            if ($project && auth()->user()->is($project->user)) {
                return $project;
            }

            return abort(
                redirect()->route('projects.index')
                    ->with('alert', [
                        'class' => 'warning',
                        'message' => __('form.requested_project_not_found'),
                    ])
            );
        });

        Route::bind('requirement', function ($value) {
            $requirement = Requirement::find($value);

            if ($requirement && auth()->user()->is($requirement->project->user)) {
                return $requirement;
            }

            return abort(
                redirect()->route('projects.index')
                    ->with('alert', [
                        'class' => 'warning',
                        'message' => __('form.requested_requirement_not_found'),
                    ])
            );
        });

        Route::bind('timeEntry', function ($value) {
            $timeEntry = TimeEntry::find($value);

            if ($timeEntry && auth()->user()->is($timeEntry->requirement->project->user)) {
                return $timeEntry;
            }

            return abort(
                redirect()->route('timeEntries.index')
                    ->with('alert', [
                        'class' => 'warning',
                        'message' => __('form.requested_time_entry_not_found'),
                    ])
            );
        });

        Route::bind('todo', function ($value) {
            $todo = Todo::find($value);

            if ($todo && auth()->user()->is($todo->user)) {
                return $todo;
            }

            return abort(
                redirect()->route('projects.index')
                    ->with('alert', [
                        'class' => 'warning',
                        'message' => __('form.requested_todo_not_found'),
                    ])
            );
        });
    }

    /**
     * Define the routes for the application.
     *
     * @return void
     */
    public function map()
    {
        $this->mapApiRoutes();

        $this->mapWebRoutes();

        //
    }

    /**
     * Define the "web" routes for the application.
     *
     * These routes all receive session state, CSRF protection, etc.
     *
     * @return void
     */
    protected function mapWebRoutes()
    {
        Route::middleware('web')
             ->namespace($this->namespace)
             ->group(base_path('routes/web.php'));
    }

    /**
     * Define the "api" routes for the application.
     *
     * These routes are typically stateless.
     *
     * @return void
     */
    protected function mapApiRoutes()
    {
        Route::prefix('api')
             ->middleware('api')
             ->namespace($this->namespace)
             ->group(base_path('routes/api.php'));
    }
}
