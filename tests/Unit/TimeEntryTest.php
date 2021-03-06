<?php

namespace Tests\Unit;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class TimeEntryTest extends TestCase
{
    public function test_it_belongs_to_a_requirement()
    {
        $timeEntry = factory('App\Models\TimeEntry')->create();

        $this->assertInstanceOf('App\Models\Requirement', $timeEntry->requirement);
    }

    public function test_time_entry_belongs_to_a_user()
    {
        $timeEntry = factory('App\Models\TimeEntry')->create();

        $this->assertInstanceOf('App\Models\User', $timeEntry->user);
    }
}
