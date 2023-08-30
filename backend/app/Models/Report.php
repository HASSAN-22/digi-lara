<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Log;
use Morilog\Jalali\Jalalian;

class Report extends Model
{
    use HasFactory;

    protected $appends = ['ir_created_at'];

    protected $fillable = [
      'user_id',
      'description',
      'reportable_id',
      'reportable_type',
    ];

    public function getIrCreatedAtAttribute()
    {
        return dateToPersian($this->created_at);
    }

    public function scopeSearch($query,string $type){
        $search = trim(request()->get('search'));
        if($search != ''){
            $query = $query->where('reportable_type',$type)
                ->where(fn($q)=>$q->whereRelation('user','name',$search)
                ->orWhereRelation('user','mobile',$search)
                ->orWhereRelation('user.profile','email',$search));
            if($type === 'App\Models\Product'){
                $query = $query->orWhereHasMorph('reportable',[$type],fn($q)=>$q->where('ir_name','like',"%$search%"));
            }else{
                $query = $query->where(
                    fn($q)=>$q->whereHasMorph('reportable',[$type],fn($q)=>$q->where('comment','like',"%$search%"))
                    ->whereHasMorph('reportable',[$type],fn($q)=>$q->where('comment.product','like',"%$search%")));
            }
        }
        return $query;
    }

    public function reportable()
    {
        return $this->morphTo();
    }

    public function user(){
        return $this->belongsTo(User::class);
    }
}
