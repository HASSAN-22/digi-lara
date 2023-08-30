<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Ticket extends Model
{
    use HasFactory;

    protected $fillable = [
        'ticketcategory_id',
        'user_id',
        'title',
        'priority',
        'is_locked',
    ];

    public function scopeSearch($query){
        $search = trim(request()->get('search'));
        $user = auth()->user();
        if($search != ''){
            $search = typeService($search)->isLocked('en')->priority('en')->get();
            $query = $query->where(fn($q)=>$q->whereRelation('user','name',$search)->orWhereRelation('ticketCategory','title',$search))
                ->orWhere('title','like',"%$search%")
                ->orWhere('priority',$search)->orWhere('is_locked',$search);

            if(!$user->isAdmin()) {
                $query = $query->where('user_id',$user->id);
            }
        }
        return $query;
    }

    public function user(){
        return $this->belongsTo(User::class);
    }

    public function ticketCategory(){
        return $this->belongsTo(Ticketcategory::class,'ticketcategory_id','id');
    }

    public function ticketMessages(){
        return $this->hasMany(Ticketmessage::class);
    }
}
