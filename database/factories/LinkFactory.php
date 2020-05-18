<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Base\Models\Link;
use Faker\Generator as Faker;

$factory->define(Link::class, function (Faker $faker) {
    return [
        'title' => substr($faker->sentence(2), 0, -1),
        'url' => $faker->url,
        'phone' => $faker->tollFreePhoneNumber,
        'email' => $faker->email,
        'description' => $faker->paragraph,
        'user_id' => factory(App\Base\Models\User::class)->create()->id,
    ];
});
