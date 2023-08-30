<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Morilog\Jalali\Jalalian;

class Productpricehistory extends Model
{
    use HasFactory;

    protected $table = 'productpricehistories';

    protected $appends = ['ir_created_at','ir_updated_at'];

    protected $fillable = ['product_id','price'];

    public function getIrCreatedAtAttribute()
    {
        return Jalalian::forge($this->created_at)->format('%d/%B/%Y');
    }

    public function getIrUpdatedAtAttribute()
    {
        return Jalalian::forge($this->updated_at)->format('%d/%B/%Y');
    }

    public function product(){
        return $this->belongsTo(Product::class);
    }
}
