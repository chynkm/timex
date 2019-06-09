<?php

namespace Test\Repositories;

use Tests\TestCase;
use App\Repositories\ProjectRepository;
use App\Models\Project;
use Illuminate\Support\Str;

class ProjectTest extends TestCase
{
    /**
     * @dataProvider provideInvalidData
     */
    public function testProjectCreationFails($input, $output, $message)
    {
        $projectRepo = new ProjectRepository;

        $this->assertSame(
            $output,
            $projectRepo->save($input),
            $message
        );
    }

    public function provideInvalidData()
    {
        $project = null;

        $project1 = new Project;

        $project2 = new Project;
        $project2->name = null;

        $project3 = new Project;
        $project3->name = Str::random(51);

        $project4 = new Project;
        $project4->name = Str::random(100);

        $project5 = new Project;
        $project5->name = Str::random(2);

        return [
            [$project, false, '$project = null, validation = false'],
            [$project1, false, 'blank $project, validation = false'],
            [$project2, false, '$project->name = null, validation = false'],
            [$project3, false, '$project->name = 51 characters, validation = false'],
            [$project4, false, '$project->name = 100 characters, validation = false'],
            [$project5, false, '$project->name = 2 characters, validation = false'],
        ];
    }

    /**
     * @dataProvider provideValidData
     */
    public function testProjectCreationSuccess($input, $output, $message)
    {
        /*
        // this creates the rows in the DB, hence mocking
        $projectRepo = new ProjectRepository;
        $this->assertInstanceOf($output, $projectRepo->save($input), $message);*/

        // Create a mock for the ProjectRepository class,
        // only mock the save() method.
        $mock = $this->getMockBuilder(ProjectRepository::class)
                     ->setMethods(['save'])
                     ->getMock();

        // Set up the expectation for the save() method
        // to be called only once and with the string $input
        // as its parameter.
        $mock->expects($this->once())
             ->method('save')
             ->with($this->equalTo($input))
             ->willReturn($input);

        $this->assertInstanceOf($output, $mock->save($input), $message);
    }

    public function provideValidData()
    {
        $project1 = new Project;
        $project1->name = Str::random(50);

        $project2 = new Project;
        $project2->name = Str::random(3);

        return [
            [$project1, Project::class, '$project->name = 50 characters, should return instance'],
            [$project2, Project::class, '$project->name = 3 characters, should return instance'],
        ];
    }
}
