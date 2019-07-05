<?php

namespace Tests\Feature;

use App\Models\Project;
use App\Models\Requirement;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Str;
use Tests\TestCase;

class ProjectRequirementTest extends TestCase
{
    use WithFaker, RefreshDatabase;

    public function test_a_project_can_have_requirements()
    {
        $this->withoutExceptionHandling();
        $this->signIn();

        $project = factory(Project::class)->create(['user_id' => auth()->id()]);

        $requirementName = 'my requirement name';

        $this->post(route('projects.requirement', ['project' => $project->id]), ['name' => $requirementName]);

        $this->get(route('projects.show', ['project' => $project->id]))
            ->assertSee($requirementName);
    }

    public function test_only_project_owner_can_add_requirement()
    {
        $this->signIn();
        $project = factory(Project::class)->create();

        $this->post(route('projects.requirement', ['project' => $project->id]), ['name' => 'new requirement'])
            ->assertRedirect(route('projects.index'));

        $this->assertDatabaseMissing('requirements', ['name' => 'new requirement']);
    }

    public function test_project_can_update_requirement()
    {
        $this->signIn();
        $project = factory(Project::class)->create(['user_id' => auth()->id()]);

        $requirementName = 'my requirement name';

        $this->post(route('projects.requirement', ['project' => $project->id]), ['name' => $requirementName]);

        $this->get(route('projects.show', ['project' => $project->id]))
            ->assertSee($requirementName);

        $requirementName = 'changed requirement';

        $this->patch(route('requirements.requirement', ['requirement' => $project->requirements->first()->id]), ['name' => $requirementName]);

        $this->get(route('projects.show', ['project' => $project->id]))
            ->assertSee($requirementName);
    }

    public function test_only_project_owner_can_update_requirement()
    {
        $this->refreshApplication();
        $this->signIn();
        $project = factory(Project::class)->create(['user_id' => auth()->id()]);

        $requirementName = 'my requirement name';

        $this->post(route('projects.requirement', ['project' => $project->id]), ['name' => $requirementName]);

        $this->get(route('projects.show', ['project' => $project->id]))
            ->assertSee($requirementName);

        $user = factory(User::class)->create();

        $this->signIn($user);
        $requirementName = 'changed requirement';

        $this->patch(route('requirements.requirement', ['requirement' => $project->requirements->first()->id]), ['name' => $requirementName])
            ->assertStatus(403);;

        $this->assertDatabaseMissing('requirements', ['name' => $requirementName]);
    }

    public function test_guest_cannot_create_requirement()
    {
        $project = factory(Project::class)->create();
        $requirementName = $this->faker()->word;

        $this->post(route('projects.requirement', ['project' => $project->id]), ['name' => $requirementName])
            ->assertRedirect('login');
    }

    public function test_guest_cannot_update_requirement()
    {
        $requirement = factory(Requirement::class)->create();

        $this->patch(route('requirements.requirement', ['requirement' => $requirement->id]), ['name' => 'changed requirement'])
            ->assertRedirect('login');
    }

    /**
     * @dataProvider invalidRequirementNameProvider
     */
    public function test_requirement_name_invalidations($input, $output, $message)
    {
        $this->signIn();

        $project = auth()->user()
            ->projects()
            ->create(factory('App\Models\Project')->raw());

        $this->post(route('projects.requirement', ['project' => $project->id]), $input)
            ->assertSessionHasErrors('name');
    }

    /**
     * @dataProvider invalidRequirementNameProvider
     */
    public function test_update_requirement_name_invalidations($input, $output, $message)
    {
        $this->signIn();

        $project = auth()->user()
            ->projects()
            ->create(factory('App\Models\Project')->raw());

        $requirementName = 'my requirement';

        $this->post(route('projects.requirement', ['project' => $project->id]), ['name' => $requirementName]);

        $this->get(route('projects.show', ['project' => $project->id]))
            ->assertSee($requirementName);

        $this->patch(route('requirements.requirement', ['requirement' => $project->requirements->first()->id]), $input)
            ->assertSessionHasErrors('name');
    }

    public function invalidRequirementNameProvider()
    {
        $this->refreshApplication();

        $attributes1 = factory('App\Models\Requirement')->raw(['name' => '']);
        $attributes2 = factory('App\Models\Requirement')->raw(['name' => null]);
        $attributes3 = factory('App\Models\Requirement')->raw(['name' => Str::random(51)]);
        $attributes4 = factory('App\Models\Requirement')->raw(['name' => Str::random(100)]);
        $attributes5 = factory('App\Models\Requirement')->raw(['name' => Str::random(2)]);

        return [
            [$attributes1, false, 'blank $requirement, validation = false'],
            [$attributes2, false, '$requirement->name = null, validation = false'],
            [$attributes3, false, '$requirement->name = 51 characters, validation = false'],
            [$attributes4, false, '$requirement->name = 100 characters, validation = false'],
            [$attributes5, false, '$requirement->name = 2 characters, validation = false'],
        ];
    }
}
