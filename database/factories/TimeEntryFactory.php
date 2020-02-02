<?php

/* @var $factory \Illuminate\Database\Eloquent\Factory */

use App\Models\HourlyRate;
use App\Models\Project;
use App\Models\Requirement;
use App\Models\TimeEntry;
use App\Models\User;
use Faker\Generator as Faker;

$factory->define(TimeEntry::class, function (Faker $faker) {
    return [
        'user_id' => factory(User::class),
        'requirement_id' => function (array $timeEntry) {
            $project = factory(Project::class)->create(['user_id' => $timeEntry['user_id']]);
            return factory(Requirement::class)->create(['project_id' => $project->id]);
        },
        'hourly_rate_id' => function (array $timeEntry) {
            return factory(HourlyRate::class)->create(['user_id' => $timeEntry['user_id']]);
        },
        'description' => $this->faker->sentence,
        'time' => $this->faker->randomFloat(2, 0, 10),
    ];
});
