<?php

namespace Tests\Feature;

use App\Models\Project;
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

        $requirementName = $this->faker()->word;

        $this->post(route('projects.requirement', ['project' => $project->id]), ['name' => $requirementName]);

        $this->get(route('projects.show', ['project' => $project->id]))
            ->assertSee($requirementName);
    }

    public function test_only_project_owner_can_add_requirement()
    {
        $this->signIn();
        $project = factory(Project::class)->create();

        $this->post(route('projects.requirement', ['project' => $project->id]), ['name' => 'new requirement'])
            ->assertStatus(403);

        $this->assertDatabaseMissing('requirements', ['name' => 'new requirement']);
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
