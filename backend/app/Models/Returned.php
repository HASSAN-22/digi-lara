<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Returned extends Model
{
    use HasFactory;

    protected $table = 'returneds';

    protected $fillable = [
        'order_id',
        'orderdetail_id',
        'description',
        'status',
    ];

    public function order(){
        return $this->belongsTo(Order::class);
    }

    public function orderDetail(){
        return $this->belongsTo(Orderdetail::class,'orderdetail_id','id');
    }

    public function user(){
        return $this->belongsTo(User::class);
    }
}
