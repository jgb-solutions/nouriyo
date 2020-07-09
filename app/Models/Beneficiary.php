<?php

  namespace App\Models;

  use Illuminate\Database\Eloquent\Model;

  class Beneficiary extends Model
  {
    protected $guarded = [];

    public function orders()
    {
      return $this->hasMany(Order::class);
    }

    public function clients()
    {
      return $this->hasManyThrough(Client::class, Order::class);
    }

    public function getFullNameAttribute()
    {
      return $this->first_name . ' ' . $this->last_name;
    }
  }
