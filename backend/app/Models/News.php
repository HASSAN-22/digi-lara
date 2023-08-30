<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Morilog\Jalali\Jalalian;

class News extends Model
{
    use HasFactory;

    protected $table = 'news';

    protected $appends = ['ir_created_at'];

    protected $fillable = [
        'category_id',
        'user_id',
        'title',
        'slug',
        'short_description',
        'description',
        'image',
        'publish',
    ];

    public function getIrCreatedAtAttribute()
    {
        return Jalalian::forge($this->created_at)->format('%d-%B-%Y');
    }

    public function scopeSearch($query){
        $search = trim(request()->get('search'));
        if($search != ''){
            $search = typeService($search)->publish('en')->get();
            $query = $query->where(fn($q)=>$q->whereRelation('user','name',$search)->orWhereRelation('category','title',$search))
                ->orWhere('title','like',"%$search%")
                ->orWhere('publish',$search);
        }
        return $query;
    }

    public function user(){
        return $this->belongsTo(User::class);
    }

    public function category(){
        return $this->belongsTo(Category::class);
    }
}
