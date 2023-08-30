<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Contact extends Model
{
    use HasFactory;

    protected $fillable = [
        'subject',
        'username',
        'email',
        'mobile',
        'message',
        'answer',
    ];

    public function scopeSearch($query){
        $search = trim(request()->get('search'));
        if($search != ''){
            $query = $query->where('subject','like',"%$search%")
                ->orWhere('username','like',"%$search%")
                ->orWhere('email','like',"%$search%")
                ->orWhere('mobile','like',"%$search%");
        }
        return $query;
    }
}
