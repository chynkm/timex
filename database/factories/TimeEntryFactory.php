<?php

/* @var $factory \Illuminate\Database\Eloquent\Factory */

use App\Models\HourlyRate;
use App\Models\Requirement;
use App\Models\TimeEntry;
use Faker\Generator as Faker;

$factory->define(TimeEntry::class, function (Faker $faker) {
    return [
        'requirement_id' => factory(Requirement::class),
        'hourly_rate_id' => factory(HourlyRate::class),
        'description' => $this->faker->sentence,
        'time' => $this->faker->randomFloat(2, 0, 10),
    ];
});
