<?php

  /** @var \Illuminate\Database\Eloquent\Factory $factory */

  use App\Models\Product;
  use Faker\Generator as Faker;

  $factory->define(Product::class, function (Faker $faker) {
    return [
      'name' => $faker->name,
      'quantity' => $faker->numberBetween(5, 500),
      'buying_price' => $faker->numberBetween(5, 50),
      'selling_price' => $faker->numberBetween(51, 80),
      'description' => $faker->text,
    ];
  });
