<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
  protected $guarded = [];

  public function packages()
  {
    return $this->belongsToMany(Package::class);
  }

  public function orders()
  {
    return $this->belongsToMany(Order::class, 'order_details');
  }

  public function getImageUrlAttribute()
  {
    return asset($this->image);
  }
}
