<?php

use Faker\Generator as Faker;

$factory->define(\App\Base\Models\Subject::class, function (Faker $faker) {
    return [
        'name' => $faker->name,
    ];
});
