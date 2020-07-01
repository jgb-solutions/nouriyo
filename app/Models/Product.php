<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
  public function packages()
  {
    return $this->belongsToMany(Package::class);
  }
}
