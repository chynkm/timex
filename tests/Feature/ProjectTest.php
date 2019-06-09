<?php

namespace Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class ProjectTest extends TestCase
{
    use WithFaker, RefreshDatabase;

    public function test_create_a_project()
    {
        $attributes = ['name' => $this->faker->name];

        $this->post(route('projects.create'), $attributes)
            ->assertRedirect(route('projects.index'));

        $this->assertDatabaseHas('projects', $attributes);

        $this->get(route('projects.index'))
            ->assertSee($attributes['name']);
    }

    public function test_a_project_requires_name()
    {
        $attributes = factory('App\Models\Project')->raw(['name' => '']);

        $this->post(route('projects.create'), $attributes)
            ->assertSessionHasErrors('name');
    }

    public function test_a_project_can_view_a_project()
    {
        $this->withoutExceptionHandling();

        $project = factory('App\Models\Project')->create();

        $this->get(route('projects.show', ['id' => $project->id]))
            ->assertSee($project->name);
    }
}
