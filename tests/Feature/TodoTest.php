<?php

namespace Tests\Feature;

use App\Models\Project;
use App\Models\Requirement;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Str;
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

        $this->get(route('todos.index', ['requirement' => $requirement->id]))
            ->assertOk();

        $attributes = [
            'task' => 'my first todo',
            'impact' => 'high',
            'complexity' => 'easy',
        ];

        $this->post(route('todos.store', ['requirement' => $requirement->id]), $attributes)
            ->assertRedirect(route('todos.index', ['requirement' => $requirement->id]));

        $this->get(route('todos.index', ['requirement' => $requirement->id]))
            ->assertSee($attributes['task'])
            ->assertSee('high');

        $this->assertDatabaseHas('todos', $attributes);
    }

    public function test_user_can_update_a_todo()
    {
        $this->withoutExceptionHandling();
        $this->signIn();

        $project = factory(Project::class)->create(['user_id' => auth()->id()]);
        $requirement = factory(Requirement::class)->create(['project_id' => $project->id]);

        $this->get(route('todos.index', ['requirement' => $requirement->id]))
            ->assertOk();

        $todo = factory('App\Models\Todo')->create([
            'user_id' => auth()->id(),
            'requirement_id' => $requirement->id,
            'task' => 'my first todo',
            'impact' => 'high',
            'complexity' => 'hard',
        ]);

        $attributes = [
            'completed' => 1,
            'task' => 'my second todo',
            'impact' => 'high',
            'complexity' => 'easy',
        ];

        $this->patch(route('todos.update', ['todo' => $todo->id]), $attributes)
            ->assertRedirect(route('todos.index', ['requirement' => $requirement->id]));

        $this->get(route('todos.index'))
            ->assertSee($attributes['task']);

        $this->get(route('todos.index', ['requirement' => $requirement->id]))
            ->assertSee($attributes['task']);

        $attributes = [
            'completed' => 0,
            'task' => 'my third todo',
            'impact' => 'high',
            'complexity' => 'medium',
        ];

        $this->patch(route('todos.update', ['todo' => $todo->id]), $attributes)
            ->assertRedirect(route('todos.index', ['requirement' => $requirement->id]));

        $this->assertDatabaseHas('todo_histories', [
                'todo_id' => $todo->id,
                'user_id' => $todo->user_id,
                'requirement_id' => $todo->requirement_id,
                'task' => 'my first todo',
                'impact' => 'high',
                'complexity' => 'hard',
                'completed' => null,
            ])
            ->assertDatabaseHas('todo_histories', [
                'todo_id' => $todo->id,
                'user_id' => $todo->user_id,
                'requirement_id' => $todo->requirement_id,
                'task' => 'my second todo',
                'impact' => 'high',
                'complexity' => 'easy',
            ]);
    }

    public function test_user_cannot_view_another_user_todo()
    {
        $this->signIn();

        $project = factory(Project::class)->create(['user_id' => auth()->id()]);
        $requirement = factory(Requirement::class)->create(['project_id' => $project->id]);
        $todo = factory('App\Models\Todo')->create();

        $this->get(route('todos.index', ['requirement' => $requirement->id]))
            ->assertDontSee($todo['task']);
    }

    /**
     * @dataProvider invalidTaskProvider
     */
    public function test_store_task_invalidations($input, $output, $message)
    {
        $this->signIn();

        $project = factory(Project::class)->create(['user_id' => auth()->id()]);
        $requirement = factory(Requirement::class)->create(['project_id' => $project->id]);

        $this->get(route('todos.index', ['requirement' => $requirement->id]))
            ->assertOk();

        $todo = factory('App\Models\Todo')->raw([
            'user_id' => auth()->id(),
            'requirement_id' => $requirement->id,
            'task' => $input,
            'impact' => 'high',
            'complexity' => 'easy',
        ]);

        $this->post(route('todos.store', ['requirement' => $requirement->id]), $todo)
            ->assertSessionHasErrors('task');

        $this->assertDatabaseMissing('todos', $todo);
    }

    public function invalidTaskProvider()
    {
        return [
            ['', false, 'blank $task, validation = false'],
            [null, false, '$task = null, validation = false'],
            [Str::random(1001), false, '$task = 1001 characters, validation = false'],
            [Str::random(1010), false, '$task = 1010 characters, validation = false'],
            [Str::random(2), false, '$task = 2 characters, validation = false'],
        ];
    }

    /**
     * @dataProvider invalidImpactComplexityProvider
     */
    public function test_store_impact_complexity_invalidations($input, $output, $message)
    {
        $this->signIn();

        $project = factory(Project::class)->create(['user_id' => auth()->id()]);
        $requirement = factory(Requirement::class)->create(['project_id' => $project->id]);

        $this->get(route('todos.index', ['requirement' => $requirement->id]))
            ->assertOk();

        $todo = factory('App\Models\Todo')->raw([
            'user_id' => auth()->id(),
            'requirement_id' => $requirement->id,
            'task' => 'first todo',
            'impact' => $input,
            'complexity' => $input,
        ]);

        $this->post(route('todos.store', ['requirement' => $requirement->id]), $todo)
            ->assertSessionHasErrors('impact')
            ->assertSessionHasErrors('complexity');

        $this->assertDatabaseMissing('todos', $todo);
    }

    public function invalidImpactComplexityProvider()
    {
        return [
            ['', false, 'blank, validation = false'],
            [null, false, 'null, validation = false'],
            ['abc', false, 'some characters, validation = false'],
            [1234, false, 'some number, validation = false'],
        ];
    }

    public function test_get_all_todos_of_a_user()
    {
        $this->signIn();

        $project1 = factory(Project::class)->create(['user_id' => auth()->id()]);
        $requirement1 = factory(Requirement::class)->create(['project_id' => $project1->id]);

        $this->get(route('todos.index'))
            ->assertOk();

        $attributes1 = [
            'task' => 'my first todo',
            'impact' => 'high',
            'complexity' => 'easy',
        ];

        $this->post(route('todos.store', ['requirement' => $requirement1->id]), $attributes1)
            ->assertRedirect(route('todos.index'));

        $project2 = factory(Project::class)->create(['user_id' => auth()->id()]);
        $requirement2 = factory(Requirement::class)->create(['project_id' => $project2->id]);

        $attributes2 = [
            'task' => 'my second todo',
            'impact' => 'medium',
            'complexity' => 'medium',
        ];

        $this->post(route('todos.store', ['requirement' => $requirement2->id]), $attributes2)
            ->assertRedirect(route('todos.index'));

        $todo = factory('App\Models\Todo')->create();

        $this->get(route('todos.index'))
            ->assertSee($attributes1['task'])
            ->assertSee($attributes2['task'])
            ->assertDontSee($todo['task']);

        $this->assertDatabaseHas('todos', $attributes1)
            ->assertDatabaseHas('todos', $attributes2);
    }
}
