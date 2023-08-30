<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Couponuser extends Model
{
    use HasFactory;

    public $timestamps = false;

    protected $table = 'couponusers';

    protected $fillable = [
        'coupon_id',
        'user_id',
        'is_available'
    ];

    public function coupon(){
        return $this->belongsTo(Coupon::class);
    }

    public function user(){
        return $this->belongsTo(User::class);
    }
}
