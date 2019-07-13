<?php

namespace Tests\Unit;

use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class HourlyRateTest extends TestCase
{
    public function test_hourly_rate_belongs_to_user()
    {
        $hourlyRate = factory('App\Models\HourlyRate')->create();

        $this->assertInstanceOf('App\Models\User', $hourlyRate->user);
    }
}
