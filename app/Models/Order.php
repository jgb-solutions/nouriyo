<?php

  namespace App\Models;

  use Illuminate\Database\Eloquent\Model;

  class Order extends Model
  {
    protected $guarded = [];

    public function client()
    {
      return $this->belongsTo(Client::class);
    }

    public function products()
    {
      return $this->belongsToMany(Product::class, 'order_details')->withPivot('quantity');
//        ->wherePivot('type', 'product');
    }

    public function packages()
    {
      return $this->belongsToMany(Package::class, 'order_details')->withPivot('quantity');
//          ->wherePivot('type', 'package');
    }

    public function beneficiary()
    {
      return $this->belongsTo(Beneficiary::class);
    }

    public static function getNumber()
    {
      do {
        $number = rand(0000000000, 9999999999);
      } while (static::whereNumber($number)->first());

      return $number;
    }

    public function getTotalAttribute()
    {
      return $this->products()->sum('selling_price') + $this->packages()->sum('price');
    }

    public function getReceiptUrlAttribute()
    {
      return asset($this->receipt);
    }
  }
