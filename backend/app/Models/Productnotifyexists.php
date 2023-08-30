<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Productnotifyexists extends Model
{
    use HasFactory;

    protected $table = 'productnotifyexists';

    protected $fillable = ['user_id','product_id'];

    public function user(){
        return $this->belongsTo(User::class);
    }

    public function product(){
        return $this->belongsTo(Product::class);
    }
}
