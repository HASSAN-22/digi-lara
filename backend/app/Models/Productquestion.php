<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Productquestion extends Model
{
    use HasFactory;

    protected $table = 'productquestions';

    protected $fillable = [
      'user_id',
      'product_id',
      'question',
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

    public function productQuestionAnswers(){
        return $this->hasMany(Productquestionanswer::class);
    }

    public function user(){
        return $this->belongsTo(User::class);
    }

    public function product(){
        return $this->belongsTo(Product::class);
    }

}
