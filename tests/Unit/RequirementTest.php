<?php

namespace Tests\Unit;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class RequirementTest extends TestCase
{
    use RefreshDatabase;

    public function test_a_requirement_belongs_to_a_project()
    {
        $requirement = factory(\App\Models\Requirement::class)->create();

        $this->assertInstanceOf('App\Models\Project', $requirement->project);
    }

}
