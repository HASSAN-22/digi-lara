<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Basket extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'product_id',
        'productsize_id',
        'productcolor_id',
        'count'
    ];

    public function productSize(){
        return $this->belongsTo(Productsize::class, 'productsize_id','id');
    }

    public function productColor(){
        return $this->belongsTo(Productcolor::class,'productcolor_id','id');
    }

    public function user(){
        return $this->belongsTo(User::class);
    }

    public function product(){
        return $this->belongsTo(Product::class);
    }

    public function coupon()
    {
        return $this->hasOneThrough(Coupon::class, Couponuser::class,'user_id','id','user_id','coupon_id');
    }
}
