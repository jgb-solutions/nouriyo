<?php

  namespace App\Models;

  use Illuminate\Contracts\Auth\MustVerifyEmail;
  use Illuminate\Foundation\Auth\User as Authenticatable;
  use Illuminate\Notifications\Notifiable;

  class User extends Authenticatable
  {
    use Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $guarded = [];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
      'password', 'remember_token',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
      'email_verified_at' => 'datetime',
      'admin' => 'boolean',
      'agent' => 'boolean',
      'active' => 'boolean',
    ];

    public function ordersTaken()
    {
      return $this->hasMany(Order::class, 'taken_by');
    }

    public function ordersDelivered()
    {
      return $this->hasMany(Order::class, 'delivered_by');
    }

    public function scopeAdmins($query)
    {
      return $query->whereAdmin(1);
    }

    public function scopeAgents($query)
    {
      return $query->whereAgent(1);
    }

    public function getFullNameAttribute()
    {
      return $this->first_name . ' ' . $this->last_name;
    }

    public function getCanTakeOrdersAttribute() {
        $total_orders_sum = $this->ordersDelivered->reduce(function ($value, $ordersDelivered) {
          return $value + $ordersDelivered->total;
        }, 0);

        return $this->limit > $total_orders_sum;
    }
  }
