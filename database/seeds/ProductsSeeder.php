<?php

use Illuminate\Database\Seeder;

use App\Models\Product;
class ProductsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
      DB::table('products')->delete();

      factory(Product::class, 100)->create();
    }
}
