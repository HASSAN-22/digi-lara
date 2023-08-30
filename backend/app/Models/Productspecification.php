<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Productspecification extends Model
{
    use HasFactory;

    protected $table = 'productspecifications';

    protected $fillable = [
        'product_id',
        'name',
        'description',
    ];

    public function product(){
        return $this->belongsTo(Product::class);
    }
}
