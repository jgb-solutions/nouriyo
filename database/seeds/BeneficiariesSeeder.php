<?php

  use Illuminate\Database\Seeder;

  use App\Models\Beneficiary;

  class BeneficiariesSeeder extends Seeder
  {
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
      DB::table('beneficiaries')->delete();

      factory(Beneficiary::class, 100)->create();
    }
  }
