<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Morilog\Jalali\Jalalian;

class Productcomment extends Model
{
    use HasFactory;

    protected $table = 'productcomments';

    protected $appends = ['ir_created_at'];

    protected $fillable = [
      'product_id',
      'user_id',
      'rating',
      'suggest',
      'comment',
      'weak_points',
      'strengths',
      'status',
    ];

    public function scopeSearch($query){
        $search = trim(request()->get('search'));
        if($search != ''){
            $search = typeService($search)->statusConfirm('en')->get();
            $query = $query->where(fn($q)=>$q->whereRelation('user','name',$search)->orWhereRelation('product','ir_name',$search))
                ->orWhere('status',$search);
        }
        return $query;
    }

    public function getIrCreatedAtAttribute()
    {
        return Jalalian::forge($this->created_at)->format('%d %B %Y');
    }

    public function user(){
        return $this->belongsTo(User::class);
    }

    public function product(){
        return $this->belongsTo(Product::class);
    }

    public function productCommentImages(){
        return $this->hasMany(Productcommentimage::class);
    }

    public function likes(): MorphMany
    {
        return $this->morphMany(Like::class, 'likable');
    }

    public function reports()
    {
        return $this->morphMany(Report::class, 'reportable');
    }
}
