<?php

namespace Tests\Unit;

use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class ProjectTest extends TestCase
{
    use RefreshDatabase;

    public function test_it_has_a_path()
    {
        $project = factory('App\Models\Project')->create();

        $this->assertEquals('/projects/'.$project->id, $project->path());
    }

    public function test_it_belongs_to_a_user()
    {
        $project = factory('App\Models\Project')->create();

        $this->assertInstanceOf('App\Models\User', $project->user);
    }

    public function test_it_can_add_a_requirement()
    {
        $project = factory('App\Models\Project')->create();

        $requirement = $project->addRequirement('Test requirement');

        $this->assertCount(1, $project->requirements);
        $this->assertTrue($project->requirements->contains($requirement));
    }
}
