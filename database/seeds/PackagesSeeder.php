<?php

  use Illuminate\Database\Seeder;

  use App\Models\Package;
  use App\Models\Product;

  use Faker\Generator as Faker;

  class PackagesSeeder extends Seeder
  {
    public $faker;

    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function __construct(Faker $faker)
    {
      $this->faker = $faker;
    }

    public function run()
    {
      DB::table('packages')->delete();
      DB::table('package_product')->delete();

      factory(Package::class, 50)->create()->each(function ($package) {
        $products      = Product::rand()->take($this->faker->numberBetween(1, 5))->pluck('id');
        $productsToAdd = [];

        foreach ($products as $id) {
          $productsToAdd[$id] = ['quantity' => $this->faker->numberBetween(1, 5)];
        }

        $package->products()->sync($productsToAdd);
      });
    }
  }
