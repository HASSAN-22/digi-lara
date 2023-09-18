<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    use HasFactory;

    protected $fillable = [
      'user_id',
      'shipping_status',
      'transport_cost',
      'reduced_wallet',
      'address',
      'pay_to_seller',
    ];

    public function user(){
        return $this->belongsTo(User::class);
    }

    public function transactions()
    {
        return $this->morphMany(Transaction::class, 'transactionable')->where('transactionable_type',"App\Models\Order");
    }

    public function orderDetails(){
        return $this->hasMany(Orderdetail::class);
    }

    public function returned(){
        return $this->hasOne(Returned::class);
    }
}
