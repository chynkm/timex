<?php

namespace Tests\Feature;

use App\Models\Project;
use App\Models\Requirement;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class TodoTest extends TestCase
{
    use WithFaker, RefreshDatabase;

    public function test_guest_cannot_create_todos()
    {
        $todo = factory('App\Models\Todo')->raw();

        $this->post(route('todos.store', ['requirement' => $todo['requirement_id']]), $todo)
            ->assertRedirect('login');
    }

    public function test_guest_cannot_view_todos()
    {
        $todo = factory('App\Models\Todo')->raw();

        $this->get(route('projects.index', ['requirement' => $todo['requirement_id']]))
            ->assertRedirect('login');
    }

    public function test_guest_cannot_update_todos()
    {
        $todo = factory('App\Models\Todo')->create();

        $attributes = ['task' => 'my modified first todo'];

        $this->patch(route('todos.update', ['todo' => $todo->id]), $attributes)
            ->assertRedirect('login');

        $this->assertDatabaseMissing('todos', $attributes);
    }

    public function test_user_can_create_a_todo()
    {
        $this->signIn();

        $project = factory(Project::class)->create(['user_id' => auth()->id()]);
        $requirement = factory(Requirement::class)->create(['project_id' => $project->id]);

        $this->get(route('projects.show', ['project_id' => $project->id]))
            ->assertOk();

        $attributes = ['task' => 'my first todo'];

        $this->post(route('todos.store', ['requirement' => $requirement->id]), $attributes)
            ->assertRedirect(route('todos.index', ['requirement' => $requirement->id]));

        $this->get(route('todos.index', ['requirement' => $requirement->id]))
            ->assertSee($attributes['task']);

        $this->assertDatabaseHas('todos', $attributes);
    }

    public function test_user_can_update_a_todo()
    {
        $this->signIn();

        $project = factory(Project::class)->create(['user_id' => auth()->id()]);
        $requirement = factory(Requirement::class)->create(['project_id' => $project->id]);

        $this->get(route('todos.index', ['requirement' => $requirement->id]))
            ->assertOk();

        $todo = factory('App\Models\Todo')->create([
            'user_id' => auth()->id(),
            'requirement_id' => $requirement->id,
            'task' => 'my first todo',
        ]);

        $attributes = ['task' => 'my modified first todo'];

        $this->patch(route('todos.update', ['todo' => $todo->id]), $attributes)
            ->assertRedirect(route('todos.index', ['requirement' => $requirement->id]));

        $this->get(route('todos.index', ['requirement' => $requirement->id]))
            ->assertSee($attributes['task']);

        $this->assertDatabaseHas('todos', $attributes);
    }
}
