<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Field Name Based Generators
    |--------------------------------------------------------------------------
    |
    | These generators use keyword matching on the field name to return fake data.
    |
    */

    'field_keywords' => [
        'email'       => fn($faker) => $faker->unique()->safeEmail(),
        'name'        => fn($faker) => $faker->name(),
        'title'       => fn($faker) => $faker->sentence(3),
        'description' => fn($faker) => $faker->paragraph(),
        'content'     => fn($faker) => $faker->paragraph(),
        'phone'       => fn($faker) => $faker->phoneNumber(),
        'image'       => fn($faker) => $faker->imageUrl(300, 300),
    ],

    /*
    |--------------------------------------------------------------------------
    | Column Type Based Generators
    |--------------------------------------------------------------------------
    |
    | These generators fallback to database column types if no keyword matched.
    |
    */

    'column_types' => [
        'string'    => fn($faker) => $faker->sentence(),
        'text'      => fn($faker) => $faker->paragraph(),
        'integer'   => fn($faker) => $faker->numberBetween(1, 1000),
        'boolean'   => fn($faker) => $faker->boolean(),
        'date'      => fn($faker) => now()->addDays(rand(-1000, 1000))->format('Y-m-d'),
        'datetime'  => fn($faker) => now()->addSeconds(rand(-10000000, 10000000)),
        'timestamp' => fn($faker) => now()->addSeconds(rand(-10000000, 10000000)),
        'float'     => fn($faker) => $faker->randomFloat(2, 0, 1000),
        'decimal'   => fn($faker) => $faker->randomFloat(2, 0, 1000),
    ],
];
