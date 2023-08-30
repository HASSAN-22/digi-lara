<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Debtor extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'order_id',
        'amount',
        'description',
        'status',
    ];

    public function scopeSearch($query){
        $search = trim(request()->get('search'));
        $user = auth()->user();
        if($search != ''){
            $search = typeService($search)->debtorSatus('en')->fixPriceFormat()->get();
            $query = $query->whereRelation('user','name',$search)
                ->orWhere('amount',$search)->orWhere('status',$search)->orWhere('order_id',$search);

            if(!$user->isAdmin()) {
                $query = $query->where('user_id',$user->id);
            }
        }
        return $query;
    }

    public function user(){
        return $this->belongsTo(User::class);
    }

    public function transactions()
    {
        return $this->morphMany(Transaction::class, 'transactionable');
    }
}
