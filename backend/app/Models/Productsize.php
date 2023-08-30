<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Productsize extends Model
{
    use HasFactory;

    public $timestamps = false;

    protected $table = 'productsizes';

    protected $fillable = [
        'product_id',
        'size_id',
        'price',
    ];

    public function product(){
        return $this->belongsTo(Product::class);
    }

    public function size(){
        return $this->belongsTo(Size::class);
    }
}
