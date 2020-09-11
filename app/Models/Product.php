<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
  protected $guarded = [];

  public function packages()
  {
    return $this->belongsToMany(Package::class)->withPivot('quantity');
  }

  public function orders()
  {
    return $this->belongsToMany(Order::class, 'order_details');
  }

  public function getImageUrlAttribute()
  {
    return asset('storage/' . $this->image);
  }

  public function scopeRand($query)
  {
    $query->orderByRaw('RAND()');
  }
}
