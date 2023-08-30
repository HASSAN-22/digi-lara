<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Becomeseller extends Model
{
    use HasFactory;

    protected $table = 'becomesellers';

    protected $fillable = [
        'user_id',
        'province_id',
        'city_id',
        'shop_name',
        'type',
        'postal_code',
        'phone',
        'mobile',
        'shaba',
        'identity_card_front',
        'identity_card_back',
        'status',
        'address',
        'reject_reason',
    ];

    public function scopeSearch($query){
        $search = trim(request()->get('search'));
        $user = auth()->user();
        if($search != ''){
            $search = typeService($search)->sellerType('en')->status('en')->get();
            $query = $query->whereRelation('user','name',$search)
                ->orWhere('shop_name','like',"%$search%")
                ->orWhere('shaba','like',"%$search%")
                ->orWhere('mobile','like',"%$search%")
                ->orWhere('status',$search);

            if(!$user->isAdmin()) {
                $query = $query->where('user_id',$user->id);
            }
        }
        return $query;
    }


    public function user(){
        return $this->belongsTo(User::class);
    }

    public function province(){
        return $this->belongsTo(Province::class);
    }

    public function city(){
        return $this->belongsTo(City::class);
    }

    public function becomeSellerLegal(){
        return $this->hasOne(Becomesellerlegal::class);
    }

    public function becomeSellerReal(){
        return $this->hasOne(Becomesellerreal::class);
    }
}
