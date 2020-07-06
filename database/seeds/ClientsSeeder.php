<?php

  use Illuminate\Database\Seeder;

  use App\Models\Client;

  class ClientsSeeder extends Seeder
  {
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
      DB::table('clients')->delete();

      factory(Client::class, 100)->create();
    }
  }
