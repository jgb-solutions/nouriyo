<?php

  namespace App\Models;

  use Illuminate\Database\Eloquent\Model;

  class CancelledOrder extends Model
  {
    public    $timestamps = false;
    protected $guarded    = [];

    public function orders()
    {
      return $this->belongsTo(Order::class);
    }
  }