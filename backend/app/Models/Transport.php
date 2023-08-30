<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Transport extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'weight_type',
        'is_free',
        'is_freight',
        'fixed_price',
        'tax',
        'from_day',
        'to_day',
    ];

    public function scopeSearch($query){
        $search = trim(request()->get('search'));
        if($search != ''){
            $search = typeService($search)->transportWeightType('en')->isFree()->isFreight()->get();
            $query = $query->where('name','like',"%$search%")
                ->orWhere('fixed_price','like',"%$search%")
                ->orWhere('is_free',$search)
                ->orWhere('is_freight',$search)
                ->orWhere('tax',$search)
                ->orWhere('weight_type',$search);
        }
        return $query;
    }
}
