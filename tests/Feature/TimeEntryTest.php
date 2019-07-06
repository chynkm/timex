<?php

namespace Tests\Feature;

use App\Models\HourlyRate;
use App\Models\Project;
use App\Models\Requirement;
use App\Models\TimeEntry;
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
        factory(HourlyRate::class)->create(['user_id' => auth()->id()]);

        $this->post(route('timeEntries.store'), [
            'project_id' => $project->id,
            'requirement_id' => $requirement->id,
            'description' => '1110 Worked on TDD for add time entry 1140 another entry 1200',
        ]);

        $this->get(route('timeEntries.create'))
            ->assertSee($project->name)
            ->assertSee($requirement->project->name)
            ->assertSee('1110 - 1140 Worked on TDD for add time entry (0.50)');
    }

    public function test_other_user_cannot_see_my_time_entry()
    {
        $this->signIn();

        $project = factory(Project::class)->create(['user_id' => auth()->id()]);
        $requirement = factory(Requirement::class)->create(['project_id' => $project->id]);
        factory(HourlyRate::class)->create(['user_id' => auth()->id()]);

        $otherUsertimeEntry = factory(TimeEntry::class)->create();

        $this->post(route('timeEntries.store'), [
            'project_id' => $project->id,
            'requirement_id' => $requirement->id,
            'description' => '1110 Worked on TDD for add time entry 1140 another entry 1200',
        ]);

        $this->get(route('timeEntries.create'))
            ->assertSee('Worked on TDD for add time entry')
            ->assertDontSee($otherUsertimeEntry->description);
    }

    public function test_user_updates_a_time_entry()
    {
        $this->signIn();

        $project = factory(Project::class)->create(['user_id' => auth()->id()]);
        $requirement = factory(Requirement::class)->create(['project_id' => $project->id]);
        factory(HourlyRate::class)->create(['user_id' => auth()->id()]);
        $timeEntry = factory(TimeEntry::class)->create([
            'user_id' => auth()->id(),
            'requirement_id' => $requirement->id
        ]);

        $this->get(route('timeEntries.edit', ['timeEntry' => $timeEntry->id]))
            ->assertOk();

        $this->patch(route('timeEntries.update', ['timeEntry' => $timeEntry->id]), [
            'project_id' => $project->id,
            'requirement_id' => $requirement->id,
            'description' => '1010 TDD test for update time entry 1055',
        ]);

        $this->get(route('timeEntries.create'))
            ->assertSee('1010 - 1055 TDD test for update time entry (0.75)');
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

    public function test_only_list_my_time_entries()
    {
        $this->signIn();

        $timeEntries = factory(TimeEntry::class, 5)->create(['user_id' => auth()->id()]);

        $description = '830 - 930 other users time entry (1)';
        factory(TimeEntry::class)->create(['description' => $description]);

        $this->get(route('timeEntries.index'))
            ->assertSee($timeEntries->last()->description)
            ->assertSee($timeEntries->first()->requirement->project->name)
            ->assertSee($timeEntries->last()->requirement->name)
            ->assertDontSee($description);
    }

    public function test_guest_cannot_view_time_entries_listing()
    {
        $this->get(route('timeEntries.index'))
            ->assertRedirect('login');
    }

    public function test_guest_cannot_view_edit_time_entry_page()
    {
        $timeEntry = factory(TimeEntry::class)->create();

        $this->get(route('timeEntries.edit', ['timeEntry' => $timeEntry->id]))
            ->assertRedirect('login');
    }

    public function test_guest_cannot_view_update_time_entry()
    {
        $timeEntry = factory(TimeEntry::class)->create();

        $this->patch(route('timeEntries.update', ['timeEntry' => $timeEntry->id]), [
                'project_id' => $timeEntry->requirement->project->id,
                'requirement_id' => $timeEntry->requirement->id,
                'description' => '1110 Worked on TDD for add time entry 1140',
            ])
            ->assertRedirect('login');
    }
}
