<?php

namespace Tests\Unit;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class UserTest extends TestCase
{
    use RefreshDatabase;

    public function test_a_user_has_projects()
    {
        $user = factory(\App\Models\User::class)->create();

        $this->assertInstanceOf(Collection::class, $user->projects);
    }

    public function test_a_user_has_hourly_rates()
    {
        $user = factory(\App\Models\User::class)->create();

        $this->assertInstanceOf(Collection::class, $user->hourlyRates);
    }

    public function test_a_user_has_many_time_entries()
    {
        $user = factory(\App\Models\User::class)->create();

        $this->assertInstanceOf(Collection::class, $user->timeEntries);
    }

    public function test_a_user_has_many_todos()
    {
        $user = factory(\App\Models\User::class)->create();

        $this->assertInstanceOf(Collection::class, $user->todos);
    }
}
