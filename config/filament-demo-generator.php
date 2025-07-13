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
        'name'         => fn($faker) => $faker->name,
        'first_name'   => fn($faker) => $faker->firstName,
        'last_name'    => fn($faker) => $faker->lastName,
        'username'     => fn($faker) => $faker->userName,

        'email'        => fn($faker) => $faker->unique()->safeEmail,
        'phone'        => fn($faker) => $faker->phoneNumber,
        'mobile'       => fn($faker) => $faker->phoneNumber,
        'address'      => fn($faker) => $faker->address,
        'city'         => fn($faker) => $faker->city,
        'state'        => fn($faker) => $faker->state,
        'country'      => fn($faker) => $faker->country,
        'zipcode'      => fn($faker) => $faker->postcode,

        'url'          => fn($faker) => $faker->url,
        'link'         => fn($faker) => $faker->url,
        'image'        => fn($faker) => $faker->imageUrl(),
        'avatar'       => fn($faker) => $faker->imageUrl(100, 100, 'people'),

        'company'      => fn($faker) => $faker->company,
        'job'          => fn($faker) => $faker->jobTitle,
        'title'        => fn($faker) => $faker->sentence(3),
        'description'  => fn($faker) => $faker->paragraph,
        'summary'      => fn($faker) => $faker->text(200),

        'price'        => fn($faker) => $faker->randomFloat(2, 10, 9999),
        'amount'       => fn($faker) => $faker->randomFloat(2, 10, 9999),
        'quantity'     => fn($faker) => $faker->numberBetween(1, 100),
        'total'        => fn($faker) => $faker->randomFloat(2, 50, 10000),

        'status'       => fn($faker) => $faker->randomElement(['active', 'inactive', 'pending']),
        'type'         => fn($faker) => $faker->randomElement(['basic', 'premium', 'enterprise']),
        'level'        => fn($faker) => $faker->randomElement(['beginner', 'intermediate', 'advanced']),
        'gender'       => fn($faker) => $faker->randomElement(['male', 'female']),

        'date'         => fn($faker) => $faker->date(),
        'dob'          => fn($faker) => $faker->date('Y-m-d', '-18 years'),
        'created'      => fn($faker) => $faker->dateTimeBetween('-1 year', 'now'),
        'time'         => fn($faker) => $faker->time(),

        'color'        => fn($faker) => $faker->safeColorName,
        'code'         => fn($faker) => strtoupper($faker->bothify('??-###')),
        'slug'         => fn($faker) => $faker->slug,
        'token'        => fn($faker) => $faker->sha1,
        'uuid'         => fn($faker) => $faker->uuid,
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
        'bigint'     => fn($faker) => $faker->numberBetween(1, 999999999),
        'binary'     => fn($faker) => base64_encode($faker->text(50)),
        'bit'        => fn($faker) => (int) $faker->boolean,
        'blob'       => fn($faker) => base64_encode($faker->text(100)),
        'boolean'    => fn($faker) => $faker->boolean,

        'char'       => fn($faker) => $faker->lexify('????'),
        'varchar'    => fn($faker) => $faker->word,
        'string'     => fn($faker) => $faker->word,

        'date'       => fn($faker) => $faker->date(),
        'datetime'   => fn($faker) => $faker->dateTime()->format('Y-m-d H:i:s'),
        'timestamp'  => fn($faker) => $faker->dateTime()->getTimestamp(),
        'time'       => fn($faker) => $faker->time(),
        'year'       => fn($faker) => $faker->year,

        'decimal'    => fn($faker) => $faker->randomFloat(2, 1, 9999),
        'double'     => fn($faker) => $faker->randomFloat(2, 1, 9999),
        'float'      => fn($faker) => $faker->randomFloat(2, 1, 9999),

        'enum'       => fn($faker) => $faker->word,
        'int'        => fn($faker) => $faker->numberBetween(1, 1000),
        'integer'    => fn($faker) => $faker->numberBetween(1, 1000),
        'tinyint'    => fn($faker) => $faker->numberBetween(0, 127),
        'smallint'   => fn($faker) => $faker->numberBetween(0, 32000),
        'mediumint'  => fn($faker) => $faker->numberBetween(0, 8388607),

        'json'       => fn($faker) => ['key' => $faker->word],
        'text'       => fn($faker) => $faker->paragraph,
        'longtext'   => fn($faker) => $faker->text(2000),
        'mediumtext' => fn($faker) => $faker->text(500),
        'tinytext'   => fn($faker) => $faker->text(100),

        'tinyblob'   => fn($faker) => base64_encode($faker->text(50)),
        'mediumblob' => fn($faker) => base64_encode($faker->text(200)),
        'longblob'   => fn($faker) => base64_encode($faker->text(500)),

        // GIS types – just return dummy geo values or skip
        'point'      => fn($faker) => 'POINT(30.0000 20.0000)',
        'linestring' => fn($faker) => 'LINESTRING(30 10, 10 30, 40 40)',
        'polygon'    => fn($faker) => 'POLYGON((30 10, 40 40, 20 40, 10 20, 30 10))',
        'multipoint' => fn($faker) => 'MULTIPOINT((10 40), (40 30), (20 20), (30 10))',
        'multilinestring' => fn($faker) => 'MULTILINESTRING((10 10, 20 20), (15 15, 30 15))',
        'multipolygon' => fn($faker) => 'MULTIPOLYGON(((30 20, 45 40, 10 40, 30 20)))',
        'geometry'   => fn($faker) => 'POINT(0 0)',
        'geometrycollection' => fn($faker) => 'GEOMETRYCOLLECTION(POINT(4 6), LINESTRING(4 6,7 10))',
    ],
];
