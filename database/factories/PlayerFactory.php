<?php

use Faker\Generator as Faker;

$factory->define(App\Player::class, function (Faker $faker) {
    return [
        'name' => $faker->name,
        'firstname' => $faker->firstName,
        'birthdate' => $faker->date(),
        'inGame' => 0
    ];
});
