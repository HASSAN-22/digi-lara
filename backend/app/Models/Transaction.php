<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Transaction extends Model
{
    use HasFactory;

    protected $fillable = [
      'user_id',
      'ref_id',
      'amount',
      'status',
      'paid_by',
      'transactionable_type',
      'transactionable_id',
    ];

    public function scopeSearch($query){
        $search = trim(request()->get('search'));
        if($search != ''){
            $user = auth()->user();
            $search = typeService($search)->transactionStatus('en')->transactionPaidBy('en')->fixPriceFormat()->get();
            $query = $query->where('ref_id','like',"%$search%")->orWhere('amount',$search)
                ->orWhere('status',$search);
            if(!$user->isAdmin()){
                $query = $query->where('user_id',$user->id);
            }
        }
        return $query;
    }

    public function transactionable()
    {
        return $this->morphTo();
    }

    public function user(){
        return $this->belongsTo(User::class);
    }
}
