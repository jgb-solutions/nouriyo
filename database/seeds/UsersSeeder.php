<?php

  use Illuminate\Database\Seeder;

  use App\Models\User;

  class UsersSeeder extends Seeder
  {
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
      DB::table('users')->delete();

      $admins = [
        [
          'first_name' => 'Alex',
          'last_name' => 'Saint Surin',
          'business' => 'Nouriyo Ayiti',
          'email' => 'alex@nouriyoayiti.com',
          'password' => bcrypt('password'),
          'admin' => true,
        ],
        [
          'first_name' => 'Rita Marie',
          'last_name' => 'Joseph',
          'business' => 'Nouriyo Ayiti',
          'email' => 'rita@nouriyoayiti.com',
          'password' => bcrypt('password'),
          'admin' => true,
        ],
        [
          'first_name' => 'Wenderson',
          'last_name' => 'Beauchamps',
          'business' => 'Nouriyo Ayiti',
          'email' => 'wendersonbeauchamps@yahoo.fr',
          'password' => bcrypt('password'),
          'admin' => true,
        ],
      ];

      foreach ($admins as $admin) {
        User::create($admin);
      }

      factory(User::class, 30)->create();
    }
  }
