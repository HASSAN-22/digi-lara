<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Categoryproperty extends Model
{
    use HasFactory;

    public $timestamps = false;

    protected $table = 'categoryproperties';

    protected $fillable = ['category_id','property_id'];

    public function category(){
        return $this->belongsTo(Category::class);
    }

    public function property(){
        return $this->belongsTo(Property::class);
    }

}
