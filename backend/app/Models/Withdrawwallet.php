<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Withdrawwallet extends Model
{
    use HasFactory;

    protected $table = 'withdrawwallets';

    protected $fillable = [
        'user_id',
        'amount',
        'shaba',
        'cart_number',
        'status',
    ];

    public function scopeSearch($query){
        $search = trim(request()->get('search'));
        $user = auth()->user();
        if($search != ''){
            $search = typeService($search)->withdrawStatus('en')->get();
            $query = $query->whereRelation('user','name',$search)
                ->orWhere('amount','like',"%$search%")
                ->orWhere('cart_number','like',"%$search%")
                ->orWhere('shaba','like',"%$search%")
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
}
