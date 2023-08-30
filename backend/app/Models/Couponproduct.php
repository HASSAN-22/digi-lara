<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Couponproduct extends Model
{
    use HasFactory;

    public $timestamps = false;

    protected $table = 'couponproducts';

    protected $fillable = [
      'coupon_id',
      'product_id'
    ];

    public function coupon(){
        return $this->belongsTo(Coupon::class);
    }

    public function product(){
        return $this->belongsTo(Product::class);
    }
}
