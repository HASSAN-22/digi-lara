<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Morilog\Jalali\Jalalian;

class Productquestionanswer extends Model
{
    use HasFactory;

    protected $table = 'productquestionanswers';

    protected $appends = ['ir_created_at'];

    protected $fillable = [
      'productquestion_id',
      'user_id',
      'answer',
      'status',
    ];

    public function scopeSearch($query){
        $search = trim(request()->get('search'));
        if($search != ''){
            $search = typeService($search)->statusConfirm('en')->get();
            $query = $query->whereRelation('user','name',$search)
                ->orWhere('status',$search);
        }
        return $query;
    }

    public function getIrCreatedAtAttribute()
    {
        return Jalalian::forge($this->created_at)->format('%d %B %Y');
    }

    public function productQuestion(){
        return $this->belongsTo(Productquestion::class, 'productquestion_id','id');
    }

    public function user(){
        return $this->belongsTo(User::class);
    }

    public function likes(): MorphMany
    {
        return $this->morphMany(Like::class, 'likable');
    }
}
