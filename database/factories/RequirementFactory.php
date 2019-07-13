<?php

/* @var $factory \Illuminate\Database\Eloquent\Factory */

use App\Models\Requirement;
use Faker\Generator as Faker;

$factory->define(Requirement::class, function (Faker $faker) {
    return [
        'project_id' => factory(App\Models\Project::class),
        'name' => $faker->word,
    ];
});
