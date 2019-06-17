<?php

namespace Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Str;

class ProjectTest extends TestCase
{
    use WithFaker, RefreshDatabase;

    public function test_only_authenticated_users_can_create_projects()
    {
        $attributes = factory('App\Models\Project')->raw();

        $this->post(route('projects.store'), $attributes)
            ->assertRedirect('login');
    }

    public function test_user_can_create_a_project()
    {
        $this->refreshApplication();
        $this->actingAs(factory('App\Models\User')->create());

        $attributes = ['name' => 'my first project'];

        $this->post(route('projects.store'), $attributes)
            ->assertRedirect(route('projects.index'));

        $this->assertDatabaseHas('projects', $attributes);

        $this->get(route('projects.index'))
            ->assertSee($attributes['name']);
    }

    public function test_a_project_can_view_a_project()
    {
        $project = factory('App\Models\Project')->create();

        $this->get(route('projects.show', ['id' => $project->id]))
            ->assertSee($project->name);
    }

    /**
     * @dataProvider invalidProjectNameProvider
     */
    public function test_project_name_invalidations($input, $output, $message)
    {
        $this->actingAs(factory('App\Models\User')->create());

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
