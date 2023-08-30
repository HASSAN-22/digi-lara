<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Email extends Model
{
    use HasFactory;

    protected $table = 'emails';

    protected $fillable = [
        'user_id',
        'issue',
        'email',
        'message',
        'status'
    ];

    public function scopeSearch($query){
        $search = trim(request()->get('search'));
        if($search != ''){
            $search = typeService($search)->emailStatus('en')->get();
            $query = $query->whereRelation('user','name',$search)
                ->orWhere('email','like',"%$search%")
                ->orWhere('issue','like',"%$search%")
                ->orWhere('status',$search);
        }
        return $query;
    }

    public function user(){
        return $this->belongsTo(User::class);
    }
}
