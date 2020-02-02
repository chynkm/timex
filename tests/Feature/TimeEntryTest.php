<?php

namespace Tests\Feature;

use App\Models\HourlyRate;
use App\Models\Project;
use App\Models\Requirement;
use App\Models\TimeEntry;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Str;
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
            ->assertSee($requirement->name)
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
            'description' => '1010 - 1055 TDD test for update time entry (0.75)',
            'time' => 0.8,
        ]);

        $this->get(route('timeEntries.create'))
            ->assertSee('1010 - 1055 TDD test for update time entry (0.75)')
            ->assertSee('0.80');
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
            ->assertSessionHasErrors('project_id');

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
            ->assertSessionHasErrors('requirement_id');

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

    public function test_creating_or_updating_a_non_existing_project_time_entry()
    {
        $this->signIn();
        factory(HourlyRate::class)->create();

        $this->post(route('timeEntries.store'), [
                'project_id' => 123232,
                'requirement_id' => 23232,
                'description' => '1110 Worked on TDD for add time entry 1140',
            ])
            ->assertSessionHasErrors('project_id');

        $this->assertDatabaseMissing('time_entries', [
            'requirement_id' => 23232,
            'description' => '1110 - 1140 Worked on TDD for add time entry (0.50)',
        ]);

        $this->patch(route('timeEntries.update', ['timeEntry' => 34453]), [
            'project_id' => 123232,
            'requirement_id' => 23232,
            'description' => '1010 TDD test for update time entry 1055',
        ]);

        $this->assertDatabaseMissing('time_entries', ['id' => 34453]);
    }

    /**
     * @dataProvider invalidTimeEntryProvider
     */
    public function test_timeEntry_invalidations($input, $field, $message)
    {
        $this->signIn();
        factory(HourlyRate::class)->create(['user_id' => auth()->id()]);

        $this->post(route('timeEntries.store'), $input)
            ->assertSessionHasErrors($field);
    }

    public function invalidTimeEntryProvider()
    {
        $this->refreshApplication();

        $project = factory(Project::class)->create();
        $requirement = factory(Requirement::class)->create(['project_id' => $project->id]);

        $attributes1 = factory('App\Models\TimeEntry')->raw([
                'project_id' => null,
                'requirement_id' => $requirement->id,
                'description' => '800 Some value 830'
            ]);
        $attributes2 = factory('App\Models\TimeEntry')->raw([
                'project_id' => $project->id,
                'requirement_id' => null,
                'description' => '800 Some value 830'
            ]);
        $attributes3 = factory('App\Models\TimeEntry')->raw([
                'project_id' => $project->id,
                'requirement_id' => $requirement->id,
                'description' => null
            ]);
        $attributes4 = factory('App\Models\TimeEntry')->raw([
                'project_id' => $project->id,
                'requirement_id' => null,
                'description' => Str::random(65537),
            ]);
        $attributes5 = factory('App\Models\TimeEntry')->raw([
                'project_id' => $project->id,
                'requirement_id' => null,
                'description' => 'ab',
            ]);
        $attributes6 = factory('App\Models\TimeEntry')->raw([
                'project_id' => null,
                'requirement_id' => null,
                'description' => null,
            ]);
        $attributes7 = factory('App\Models\TimeEntry')->raw();

        return [
            [$attributes1, 'project_id', '$timeEntry->project_id = null'],
            [$attributes2, 'requirement_id', '$timeEntry->requirement_id = null'],
            [$attributes3, 'description', '$timeEntry->description = null characters'],
            [$attributes4, 'description', '$timeEntry->description = 65537 characters'],
            [$attributes5, 'description', '$timeEntry->description = 2 characters'],
            [$attributes6, 'project_id', '$timeEntry attributes null'],
            [$attributes7, 'project_id', 'blank $timeEntry'],
        ];
    }

    /**
     * @dataProvider invalidTimeEntryUpdateProvider
     */
    public function test_timeEntry_update_invalidations($input, $field, $message)
    {
        $user = User::find($input['user_id']);
        $this->signIn($user);
        $timeEntry = factory(TimeEntry::class)->create(['user_id' => auth()->id()]);

        $this->patch(route('timeEntries.update', ['timeEntry' => $timeEntry->id]), $input)
            ->assertSessionHasErrors($field);
    }

    public function invalidTimeEntryUpdateProvider()
    {
        $this->refreshApplication();

        $project = factory(Project::class)->create();
        $requirement = factory(Requirement::class)->create(['project_id' => $project->id]);

        $attributes1 = factory('App\Models\TimeEntry')->raw([
                'project_id' => null,
                'requirement_id' => $requirement->id,
                'description' => '800 Some value 830',
                'time' => 0.5,
            ]);
        $attributes2 = factory('App\Models\TimeEntry')->raw([
                'project_id' => $project->id,
                'requirement_id' => null,
                'description' => '800 Some value 830',
                'time' => 0.5,
            ]);
        $attributes3 = factory('App\Models\TimeEntry')->raw([
                'project_id' => $project->id,
                'requirement_id' => $requirement->id,
                'description' => null,
                'time' => 0.5,
            ]);
        $attributes4 = factory('App\Models\TimeEntry')->raw([
                'project_id' => $project->id,
                'requirement_id' => null,
                'description' => Str::random(65537),
                'time' => 0.5,
            ]);
        $attributes5 = factory('App\Models\TimeEntry')->raw([
                'project_id' => $project->id,
                'requirement_id' => null,
                'description' => 'ab',
                'time' => 0.5,
            ]);
        $attributes6 = factory('App\Models\TimeEntry')->raw([
                'project_id' => null,
                'requirement_id' => null,
                'description' => null,
                'time' => null,
            ]);
        $attributes7 = factory('App\Models\TimeEntry')->raw();
        $attributes8 = factory('App\Models\TimeEntry')->raw(['time' => null]);

        return [
            [$attributes1, 'project_id', '$timeEntry->project_id = null'],
            [$attributes2, 'requirement_id', '$timeEntry->requirement_id = null'],
            [$attributes3, 'description', '$timeEntry->description = null characters'],
            [$attributes4, 'description', '$timeEntry->description = 65537 characters'],
            [$attributes5, 'description', '$timeEntry->description = 2 characters'],
            [$attributes6, 'project_id', '$timeEntry attributes null'],
            [$attributes7, 'project_id', 'blank $timeEntry'],
            [$attributes8, 'time', '$timeEntry->time null'],
        ];
    }

    public function test_user_can_create_a_timeless_time_entry()
    {
        $this->signIn();

        $project = factory(Project::class)->create(['user_id' => auth()->id()]);
        $requirement = factory(Requirement::class)->create(['project_id' => $project->id]);
        factory(HourlyRate::class)->create(['user_id' => auth()->id()]);

        $this->post(route('timeEntries.store'), [
            'project_id' => $project->id,
            'requirement_id' => $requirement->id,
            'description' => 'Timeless time entry',
        ]);

        $this->get(route('timeEntries.create'))
            ->assertSee($project->name)
            ->assertSee($requirement->name)
            ->assertSee('Timeless time entry');

        $this->post(route('timeEntries.store'), [
            'project_id' => $project->id,
            'requirement_id' => $requirement->id,
            'description' => '1233 - 1239 Timeless time entry',
        ]);

        $this->get(route('timeEntries.create'))
            ->assertSee($project->name)
            ->assertSee($requirement->name)
            ->assertSee('1233 - 1239 Timeless time entry');

        $this->post(route('timeEntries.store'), [
            'project_id' => $project->id,
            'requirement_id' => $requirement->id,
            'description' => '1400 1419 Timeless time entry',
        ]);

        $this->get(route('timeEntries.create'))
            ->assertSee($project->name)
            ->assertSee($requirement->name)
            ->assertSee('1400 1419 Timeless time entry');
    }

    public function test_user_can_create_a_time_entry_with_breaks()
    {
        $this->signIn();

        $project = factory(Project::class)->create(['user_id' => auth()->id()]);
        $requirement = factory(Requirement::class)->create(['project_id' => $project->id]);
        factory(HourlyRate::class)->create(['user_id' => auth()->id()]);

        $this->post(route('timeEntries.store'), [
            'project_id' => $project->id,
            'requirement_id' => $requirement->id,
            'description' => '1110 Worked on jenkins 1140 1200 time entry with break 1215',
        ]);

        $this->get(route('timeEntries.create'))
            ->assertSee($project->name)
            ->assertSee($requirement->name)
            ->assertSee('1110 - 1140 Worked on jenkins (0.50)')
            ->assertSee('1200 - 1215 time entry with break (0.25)')
            ->assertDontSee('1140 - 1200  (0.33)')
            ->assertSee('0.75')
            ->assertDontSee('1.08');

        $this->assertDatabaseHas('time_entries', [
            'description' => "1110 - 1140 Worked on jenkins (0.50)\n1200 - 1215 time entry with break (0.25)",
            'time' => 0.75,
        ]);
    }
}
