<?php

  namespace App\Models;

  use Illuminate\Database\Eloquent\Model;

  class Package extends Model
  {
    protected $guarded = [];

    public function products()
    {
      return $this->belongsToMany(Product::class);
    }

    public function orders()
    {
      $this->belongsToMany(Order::class, 'order_details');
    }
  }