<?php

use Faker\Generator as Faker;

$factory->define(App\Product::class, function (Faker $faker) {
    return [
        'name' => $faker->name,
        'price' => $faker->randomFloat($nbMaxDecimals = 2, $min = 100.00, $max = 1000.00),
    ];
});
