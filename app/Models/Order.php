<?php

  namespace App\Models;

  use Illuminate\Database\Eloquent\Model;

  class Order extends Model
  {
    protected $guarded = [];

    public function client()
    {
      return $this->belongsTo(User::class, 'order_details');
    }

    public function products()
    {
      return $this->belongsToMany(Product::class, 'order_details');
    }

    public function packages()
    {
      return $this->belongsToMany(Package::class, 'order_details');
    }

    public function beneficiaries()
    {
      return $this->belongsToMany(Beneficiary::class, 'order_details');
    }

    public static function getHash()
    {
      do {
        $hash = rand(0000000000, 9999999999);
      } while (static::whereHash($hash)->first());

      return $hash;
    }
  }
