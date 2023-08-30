<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Brand extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'name',
        'en_name',
        'description',
        'registration_form',
        'link',
        'logo',
        'brand_type',
        'status',
        'reason_rejection',
    ];

    public function scopeSearch($query){
        $search = trim(request()->get('search'));
        $user = auth()->user();
        if($search != ''){
            $search = typeService($search)->brandType('en')->statusConfirm('en')->get();
            $query = $query->whereRelation('user','name',$search)
                ->orWhere('name','like',"%$search%")
                ->orWhere('en_name','like',"%$search%")->orWhere('name','like',"%$search%")
                ->orWhere('brand_type',$search)->orWhere('status',$search);

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
