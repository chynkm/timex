<?php

namespace Tests\Repository;

use App\Repositories\HourlyRateRepository;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class HourlyRateTest extends TestCase
{
    use WithFaker, RefreshDatabase;

    public function test_get_current_hourly_rate()
    {
        $this->signIn();
        $user = ['user_id' => auth()->id()];

        factory('App\Models\HourlyRate', 4)->create();
        factory('App\Models\HourlyRate', 5)->create($user);
        $hourlyRate = factory('App\Models\HourlyRate')->create($user);

        $hourlyRateRepository = new HourlyRateRepository;

        $this->assertDatabaseHas('hourly_rates', $hourlyRate->toArray());
        $this->assertCount(6, $hourlyRateRepository->all());
        $this->assertEquals($hourlyRate->rate, $hourlyRateRepository->currentRate());
    }

    public function test_get_current_hourly_rate_id()
    {
        $this->signIn();
        $user = ['user_id' => auth()->id()];

        factory('App\Models\HourlyRate', 4)->create();
        factory('App\Models\HourlyRate', 5)->create($user);
        $hourlyRate = factory('App\Models\HourlyRate')->create($user);

        $hourlyRateRepository = new HourlyRateRepository;

        $this->assertDatabaseHas('hourly_rates', $hourlyRate->toArray());
        $this->assertCount(6, $hourlyRateRepository->all());
        $this->assertEquals($hourlyRate->id, $hourlyRateRepository->currentRateId());
    }
}
