<?php

  namespace App\Models;

  use Illuminate\Database\Eloquent\Model;

  class Client extends Model
  {
    protected $guarded = [];

    public function orders()
    {
      return $this->hasMany(Order::class);
    }

    public function beneficiaries()
    {
      return $this->hasManyThrough(Beneficiary::class, Order::class);
    }

    public function getFullNameAttribute()
    {
      return $this->first_name . ' ' . $this->last_name;
    }
  }
