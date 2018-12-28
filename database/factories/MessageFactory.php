<?php

use Faker\Generator as Faker;
use Carbon\Carbon;

$factory->define(App\Message::class, function (Faker $faker) {
    return [
        'name'       => $faker->name,
        'title'      => $faker->sentence,
        'tel'        => $faker->sentence,
        'content'    => $faker->sentence,
        'created_at' => Carbon::now()->toDateTimeString(),
        'updated_at' => Carbon::now()->toDateTimeString(),
        'status'     => $faker->boolean,
        'ip'         => ip2long($faker->ipv4),
    ];
});
