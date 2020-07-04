<?php

  /** @var \Illuminate\Database\Eloquent\Factory $factory */

  use App\Models\User;
  use Faker\Generator as Faker;

  /*
  |--------------------------------------------------------------------------
  | Model Factories
  |--------------------------------------------------------------------------
  |
  | This directory should contain each of the model factory definitions for
  | your application. Factories provide a convenient way to generate new
  | model instances for testing / seeding your application's database.
  |
  */

  $factory->define(User::class, function (Faker $faker) {
    return [
      'email' => $faker->unique()->safeEmail,
      'password' => bcrypt('password'),
      'first_name' => $faker->firstName,
      'last_name' => $faker->lastName,
      'business' => $faker->company,
      'address' => $faker->address,
      'phone' => $faker->phoneNumber,
      'country' => $faker->country,
      'state' => $faker->state,
      'city' => $faker->city,
      'zip' => $faker->postcode,
      'agent' => true,
      'active' => true,
      'limit' => $faker->numberBetween(50, 1000),
    ];
  });
