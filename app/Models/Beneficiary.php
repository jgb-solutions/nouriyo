<?php

  namespace App\Models;

  use Illuminate\Database\Eloquent\Model;

  class Beneficiary extends Model
  {
    protected $guarded = [];

    public function orders()
    {
      return $this->belongsToMany(Order::class, 'order_details');
    }

    public function getFullNameAttribute()
    {
      return $this->first_name . ' ' . $this->last_name;
    }
  }
