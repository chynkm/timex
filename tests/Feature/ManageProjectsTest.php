<?php

namespace Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Str;

class ManageProjectsTest extends TestCase
{
    use WithFaker, RefreshDatabase;

    public function test_guest_cannot_create_projects()
    {
        $attributes = factory('App\Models\Project')->raw();

        $this->post(route('projects.store'), $attributes)
            ->assertRedirect('login');
    }

    public function test_guest_cannot_view_projects()
    {
        $this->get(route('projects.index'))
            ->assertRedirect('login');
    }

    public function test_guest_cannot_access_create_project_view()
    {
        $this->get(route('projects.create'))
            ->assertRedirect('login');
    }

    public function test_guest_cannot_view_single_project()
    {
        $project = factory('App\Models\Project')->create();

        $this->get(route('projects.show', ['project' => $project->id]))
            ->assertRedirect('login');
    }

    public function test_user_can_create_a_project()
    {
        $this->refreshApplication();
        $this->signIn();

        $this->get(route('projects.create'))
            ->assertStatus(200);

        $attributes = ['name' => 'my first project'];

        $this->post(route('projects.store'), $attributes)
            ->assertRedirect(route('projects.index'));

        $this->assertDatabaseHas('projects', $attributes);

        $this->get(route('projects.index'))
            ->assertSee($attributes['name']);
    }

    public function test_a_user_can_view_a_project()
    {
        $this->signIn();
        $project = factory('App\Models\Project')->create(['user_id' => auth()->id()]);

        $this->get(route('projects.show', ['id' => $project->id]))
            ->assertSee($project->name);
    }

    public function test_an_authenticated_user_cannot_view_other_users_projects()
    {
        $this->signIn();
        $project = factory('App\Models\Project')->create();

        $this->get(route('projects.show', ['id' => $project->id]))
            ->assertRedirect(route('projects.index'))
            ->assertSessionHas('alert');
    }

    public function test_a_user_can_update_his_project()
    {
        $this->signIn();
        $project = factory('App\Models\Project')->create(['user_id' => auth()->id()]);

        $this->get(route('projects.index'))
            ->assertSee($project['name']);

        $this->get(route('projects.edit', ['project' => $project->id]))
            ->assertStatus(200);

        $newProject = ['name' => 'new project name'];

        $this->put(route('projects.update', ['project' => $project->id]), $newProject)
            ->assertRedirect(route('projects.index'));

        $this->assertDatabaseHas('projects', $newProject);

        $this->get(route('projects.show', ['project' => $project->id]))
            ->assertSee($newProject['name']);
    }

    public function test_a_user_cannot_view_or_update_other_user_project()
    {
        $this->signIn();
        $project = factory('App\Models\Project')->create();

        $this->assertDatabaseHas('projects', $project->toArray());

        $this->get(route('projects.edit', ['project' => $project->id]))
            ->assertRedirect(route('projects.index'));

        $newProject = ['name' => 'new project name'];

        $this->put(route('projects.update', ['project' => $project->id]), $newProject)
            ->assertRedirect(route('projects.index'));

        $this->assertDatabaseMissing('projects', $newProject);

        $this->get(route('projects.show', ['project' => $project->id]))
            ->assertDontSee($newProject['name']);
    }

    public function test_guest_cannot_edit_single_project()
    {
        $project = factory('App\Models\Project')->create();

        $this->get(route('projects.edit', ['project' => $project->id]))
            ->assertRedirect('login');
    }

    public function test_guest_cannot_update_single_project()
    {
        $project = factory('App\Models\Project')->create();

        $newProject = ['name' => 'new project name'];

        $this->put(route('projects.update', ['project' => $project->id]), $newProject)
            ->assertRedirect('login');
    }

    /**
     * @dataProvider invalidProjectNameProvider
     */
    public function test_project_name_invalidations_for_project_update($input, $output, $message)
    {
        $this->signIn();
        $project = factory('App\Models\Project')->create(['user_id' => auth()->id()]);

        $this->put(route('projects.update', ['project' => $project->id]), $input)
            ->assertSessionHasErrors('name');
    }

    /**
     * @dataProvider invalidProjectNameProvider
     */
    public function test_project_name_invalidations($input, $output, $message)
    {
        $this->signIn();

        $this->post(route('projects.store'), $input)
            ->assertSessionHasErrors('name');
    }

    public function invalidProjectNameProvider()
    {
        $this->refreshApplication();

        $attributes1 = factory('App\Models\Project')->raw(['name' => '']);
        $attributes2 = factory('App\Models\Project')->raw(['name' => null]);
        $attributes3 = factory('App\Models\Project')->raw(['name' => Str::random(51)]);
        $attributes4 = factory('App\Models\Project')->raw(['name' => Str::random(100)]);
        $attributes5 = factory('App\Models\Project')->raw(['name' => Str::random(2)]);

        return [
            [$attributes1, false, 'blank $project, validation = false'],
            [$attributes2, false, '$project->name = null, validation = false'],
            [$attributes3, false, '$project->name = 51 characters, validation = false'],
            [$attributes4, false, '$project->name = 100 characters, validation = false'],
            [$attributes5, false, '$project->name = 2 characters, validation = false'],
        ];
    }
}
