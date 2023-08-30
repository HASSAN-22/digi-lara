<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Faq extends Model
{
    use HasFactory;

    protected $table = 'faqs';

    protected $fillable = ['category_id','title','description'];

    public function scopeSearch($query){
        $search = trim(request()->get('search'));
        if($search != ''){
            $query = $query->whereRelation('category','title',$search)
                ->orWhere('title','like',"%$search%");
        }
        return $query;
    }

    public function category(){
        return $this->belongsTo(Category::class);
    }
}
