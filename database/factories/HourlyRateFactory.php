<?php

/* @var $factory \Illuminate\Database\Eloquent\Factory */

use App\Models\HourlyRate;
use Faker\Generator as Faker;

$factory->define(HourlyRate::class, function (Faker $faker) {
    return [
        'rate' => $this->faker->randomFloat(2, 100, 1000)
    ];
});
