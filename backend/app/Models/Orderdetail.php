<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Orderdetail extends Model
{
    use HasFactory;

    protected $table = 'orderdetails';

    protected $fillable = [
      'user_id',
      'order_id',
      'product_id',
      'brand',
      'name',
      'sku',
      'price',
      'image',
      'count',
      'discount',
      'discount_type',
      'property_type',
      'property_name',
      'property_price',
      'property_color',
      'amount',
      'shipping_status',
      'commission',
    ];

    public function user(){
        return $this->belongsTo(User::class);
    }

    public function order(){
        return $this->belongsTo(Order::class);
    }

    public function product(){
        return $this->belongsTo(Product::class);
    }

    public function returned(){
        return $this->hasOne(Returned::class);
    }
}
