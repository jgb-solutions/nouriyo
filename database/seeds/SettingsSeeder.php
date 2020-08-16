<?php

  use Illuminate\Database\Seeder;

  use App\Models\Setting;

  class SettingsSeeder extends Seeder
  {
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
      DB::table('settings')->delete();

      Setting::create([
        'transport_fee' => 20,
        'service_fee' => 5,
      ]);
    }
  }
