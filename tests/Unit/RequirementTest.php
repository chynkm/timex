<?php

namespace Tests\Unit;

use App\Models\Requirement;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class RequirementTest extends TestCase
{
    use RefreshDatabase;

    public function test_a_requirement_belongs_to_a_project()
    {
        $requirement = factory(Requirement::class)->create();

        $this->assertInstanceOf('App\Models\Project', $requirement->project);
    }

    public function test_a_requirement_has_many_todos()
    {
        $requirement = factory(Requirement::class)->create();

        $this->assertInstanceOf(Collection::class, $requirement->todos);
    }

}
