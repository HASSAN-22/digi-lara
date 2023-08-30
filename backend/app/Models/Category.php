<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
    use HasFactory;

    protected $fillable = [
        'parent_id',
        'title',
        'slug',
        'type',
        'image',
        'icon',
        'status',
        'weight_type',
        'commission',
    ];


    public function scopeSearch($query){
        $search = trim(request()->get('search'));
        if($search != ''){
            $search = typeService($search)->categoryType('en')->status('en')->get();
            $query = $query->whereRelation('category','title',$search)->orWhere('title','like',"%$search%")->orWhere('type',$search)
                ->orWhere('status',$search);
        }
        return $query;
    }

    public function category(){
        return $this->belongsTo(Category::class,'parent_id','id')->with('category');
    }

    public function children()
    {
        return $this->hasMany(Category::class, 'parent_id', 'id')->with('children');
    }

    public function categoryProperties()
    {
        return $this->hasMany(Categoryproperty::class);
    }

    public function syncCategoryProperties()
    {
        return $this->belongsToMany(Categoryproperty::class,'categoryproperties','category_id','property_id');
    }

    public function products(){
        return $this->hasMany(Product::class);
    }

    public function couponCategories(){
        return $this->hasMany(Couponcategory::class);
    }

    public function faqs(){
        return $this->hasMany(Faq::class,'category_id','id');
    }

    public function news(){
        return $this->hasMany(News::class,'category_id','id');
    }
}
