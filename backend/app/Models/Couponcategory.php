<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Couponcategory extends Model
{
    use HasFactory;

    public $timestamps = false;

    protected $table = 'couponcategories';

    protected $fillable = [
      'category_id',
      'coupon_id'
    ];

    public function coupon(){
        return $this->belongsTo(Coupon::class);
    }

    public function category(){
        return $this->belongsTo(Category::class);
    }
}
