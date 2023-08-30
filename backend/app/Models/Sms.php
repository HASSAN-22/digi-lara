<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Sms extends Model
{
    use HasFactory;

    protected $fillable = ['mobile','message','type'];

    public function scopeSearch($query){
        $search = trim(request()->get('search'));
        if($search != ''){
            $query = $query->where('mobile','like',"%$search%");
        }
        return $query;
    }
}
