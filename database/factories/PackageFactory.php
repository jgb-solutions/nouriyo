<?php

  /** @var \Illuminate\Database\Eloquent\Factory $factory */

  use App\Models\Package;
  use Faker\Generator as Faker;

  $factory->define(Package::class, function (Faker $faker) {
    return [
      'name' => $faker->name,
      'price' => $faker->numberBetween(5, 500),
      'quantity' => $faker->numberBetween(5, 50),
      'description' => $faker->text,
    ];
  });
