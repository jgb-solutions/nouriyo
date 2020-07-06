<?php

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
         $this->call(UsersSeeder::class);
         $this->call(ProductsSeeder::class);
         $this->call(PackagesSeeder::class);
         $this->call(ClientsSeeder::class);
         $this->call(BeneficiariesSeeder::class);
    }
}
