<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Amazingalert extends Model
{
    use HasFactory;

    public $timestamps = false;

    protected $table = 'amazingalerts';

    protected $fillable = ['product_id','user_id'];

    public function user(){
        return $this->belongsTo(User::class);
    }

    public function product(){
        return $this->belongsTo(Product::class);
    }
}
