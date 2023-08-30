<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Page extends Model
{
    use HasFactory;

    protected $fillable = [
      'user_id',
      'title',
      'description'
    ];

    public function scopeSearch($query){
        $search = trim(request()->get('search'));
        if($search != ''){
            $query = $query->where(fn($q)=>$q->whereRelation('user','name',$search))
                ->orWhere('title','like',"%$search%");
        }
        return $query;
    }

    public function user(){
        return $this->belongsTo(User::class);
    }
}
