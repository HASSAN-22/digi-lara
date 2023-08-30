<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Coupon extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'code',
        'type',
        'percent',
        'count',
        'start_at',
        'expire_at',
    ];

    public function scopeSearch($query){
        $search = trim(request()->get('search'));
        if($search != ''){
            $search = typeService($search)->couponType()->get();
            $query = $query->where('title','like',"%$search%")
                ->orWhere('code','like',"%$search%")->orWhere('percent',"$search")
                ->orWhere('count',$search)->orWhere('type',$search);
        }
        return $query;
    }

    public function couponProducts(){
        return $this->hasMany(Couponproduct::class);
    }

    public function couponCategories(){
        return $this->hasMany(Couponcategory::class);
    }
    public function syncCouponProducts(){
        return $this->belongsToMany(Couponproduct::class, 'couponproducts','coupon_id','product_id');
    }

    public function syncCouponCategories(){
        return $this->belongsToMany(Couponcategory::class,'couponcategories','coupon_id', 'category_id');
    }

    public function couponUsers(){
        return $this->hasMany(Couponuser::class);
    }

    public function syncCouponUsers(){
        return $this->belongsToMany(Couponuser::class,'couponusers','coupon_id', 'user_id');
    }
}
