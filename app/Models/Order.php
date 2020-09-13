<?php

  namespace App\Models;

  use Carbon\Carbon;
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

    public function agentWhoTookTheOrder()
    {
      return $this->belongsTo(User::class, 'taken_by');
    }

    public function agentWhoDeliveredTheOrder()
    {
      return $this->belongsTo(User::class, 'delivered_by');
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
      return asset('storage/' . $this->receipt);
    }

    public function getAgentCantEditAttribute()
    {
      return auth()->user()->agent && $this->created_at->diffInMinutes(Carbon::now()) >= 5;
    }
  }
