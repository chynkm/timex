<?php

namespace Tests\Feature;

use App\Models\HourlyRate;
use App\Models\Project;
use App\Models\Requirement;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class TimeEntryTest extends TestCase
{
    use WithFaker, RefreshDatabase;

    public function test_user_can_create_a_time_entry()
    {
        $this->withoutExceptionHandling();
        $this->signIn();

        $project = factory(Project::class)->create(['user_id' => auth()->id()]);
        $requirement = factory(Requirement::class)->create(['project_id' => $project->id]);
        factory(HourlyRate::class)->create();

        $this->post(route('timeEntries.store'), [
            'project_id' => $project->id,
            'requirement_id' => $requirement->id,
            'description' => '1110 Worked on TDD for add time entry 1140 another entry 1200',
        ]);

        $this->get(route('timeEntries.create'))
            ->assertSee('1110 - 1140 Worked on TDD for add time entry (0.50)');
    }

    public function test_other_project_owner_cannot_add_its_time_entry()
    {
        $this->signIn();
        $project = factory(Project::class)->create();
        $requirement = factory(Requirement::class)->create(['project_id' => $project->id]);
        factory(HourlyRate::class)->create();

        $this->post(route('timeEntries.store'), [
                'project_id' => $project->id,
                'requirement_id' => $requirement->id,
                'description' => '1110 Worked on TDD for add time entry 1140',
            ])
            ->assertStatus(403);

        $this->assertDatabaseMissing('time_entries', [
            'requirement_id' => $requirement->id,
            'description' => '1110 - 1140 Worked on TDD for add time entry (0.50)',
        ]);
    }

    public function test_current_project_owner_with_different_requirement_cannot_add_its_time_entry()
    {
        $this->signIn();
        $project = factory(Project::class)->create(['user_id' => auth()->id()]);
        $requirement = factory(Requirement::class)->create();
        factory(HourlyRate::class)->create();

        $this->post(route('timeEntries.store'), [
                'project_id' => $project->id,
                'requirement_id' => $requirement->id,
                'description' => '1110 Worked on TDD for add time entry 1140',
            ])
            ->assertStatus(403);

        $this->assertDatabaseMissing('time_entries', [
            'requirement_id' => $requirement->id,
            'description' => '1110 - 1140 Worked on TDD for add time entry (0.50)',
        ]);
    }

    public function test_guest_cannot_submit_time_entry()
    {
        $project = factory(Project::class)->create();
        $requirement = factory(Requirement::class)->create(['project_id' => $project->id]);
        factory(HourlyRate::class)->create();

        $this->post(route('timeEntries.store'), [
                'project_id' => $project->id,
                'requirement_id' => $requirement->id,
                'description' => '1110 Worked on TDD for add time entry 1140',
            ])
            ->assertRedirect('login');
    }

    public function test_guest_cannot_view_create_time_entry_page()
    {
        $this->get(route('timeEntries.create'))
            ->assertRedirect('login');
    }
}
