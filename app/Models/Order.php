<?php

  namespace App\Models;

  use Illuminate\Database\Eloquent\Model;

  class Order extends Model
  {
    public function client()
    {
      return $this->belongsTo(User::class);
    }

    public function products()
    {
      return $this->belongsToMany(Product::class);
    }

    public function packages()
    {
      return $this->belongsToMany(Package::class);
    }

    public function beneficiaries()
    {
      return $this->belongsToMany(Beneficiary::class);
    }

    public static function getHash()
    {
      do {
        $hash = rand(0000000000, 9999999999);
      } while (static::whereHash($hash)->first());

      return $hash;
    }
  }
