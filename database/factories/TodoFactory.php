<?php

/* @var $factory \Illuminate\Database\Eloquent\Factory */

use App\Models\Project;
use App\Models\Requirement;
use App\Models\Todo;
use App\Models\User;
use Faker\Generator as Faker;

$factory->define(Todo::class, function (Faker $faker) {
    return [
        'user_id' => factory(User::class),
        'requirement_id' => function (array $timeEntry) {
            $project = factory(Project::class)->create(['user_id' => $timeEntry['user_id']]);
            return factory(Requirement::class)->create(['project_id' => $project->id]);
        },
        'task' => $this->faker->sentence,
    ];
});
