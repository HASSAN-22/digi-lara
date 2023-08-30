<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Property extends Model
{
    use HasFactory;

    protected $fillable = ['property'];

    public function scopeSearch($query){
        $search = trim(request()->get('search'));
        if($search != ''){
            $query = $query->where('property','like',"%$search%");
        }
        return $query;
    }

    public function categoryProperties(){
        return $this->hasMany(Categoryproperty::class);
    }

    public function categoryProperty(){
        return $this->hasOne(Categoryproperty::class);
    }

    public function propertyTypes(){
        return $this->hasMany(Propertytype::class);
    }
}
