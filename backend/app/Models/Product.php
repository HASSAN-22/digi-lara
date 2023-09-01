<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'brand_id',
        'category_id',
        'guarantee_id',
        'ir_name',
        'en_name',
        'slug',
        'sku',
        'price',
        'amazing_offer_percent',
        'amazing_offer_status',
        'amazing_offer_expire',
        'amazing_price',
        'packing_length',
        'packing_width',
        'packing_height',
        'packing_weight',
        'physical_length',
        'physical_width',
        'physical_height',
        'physical_weight',
        'publish',
        'count',
        'guarantee_month',
        'image',
        'is_original',
        'strengths',
        'weak_points',
        'description',
        'more_description',
    ];

    public function scopeSearch($query){
        $search = trim(request()->get('search'));
        if($search != ''){
            $search = typeService($search)->publish('en')->amazingOfferSatus()->fixPriceFormat()->status()->get();
            $query = $query->whereRelation('user','name',$search)
                ->orWhere('ir_name','like',"%$search%")
                ->orWhere('en_name','like',"%$search%")
                ->orWhere('price',$search)
                ->orWhere('count',$search)
                ->orWhere('sku','like',"%$search%")
                ->orWhere('publish',$search);
        }
        return $query;
    }

    public function category(){
        return $this->belongsTo(Category::class);
    }

    public function brand(){
        return $this->belongsTo(Brand::class);
    }

    public function user(){
        return $this->belongsTo(User::class);
    }

    public function guarantee(){
        return $this->belongsTo(Guarantee::class);
    }

    public function productImages(){
        return $this->hasMany(Productimage::class);
    }

    public function productProperties(){
        return $this->hasMany(Productproperty::class);
    }

    public function productSpecifications(){
        return $this->hasMany(Productspecification::class);
    }

    public function wishlists(){
        return $this->hasMany(Wishlist::class);
    }

    public function amazingAlerts(){
        return $this->hasMany(Amazingalert::class);
    }

    public function productPriceHistories(){
        return $this->hasMany(Productpricehistory::class)->orderBy('created_at');
    }

    public function productComments(){
        return $this->hasMany(Productcomment::class);
    }

    public function productCommentImages()
    {
        return $this->hasManyThrough(Productcommentimage::class, Productcomment::class);
    }

    public function productQuestions(){
        return $this->hasMany(Productquestion::class);
    }

    public function reports()
    {
        return $this->morphMany(Report::class, 'reportable');
    }

    public function productNotifyExists(){
        return $this->hasMany(Productnotifyexists::class);
    }

    public function productColors(){
        return $this->hasMany(Productcolor::class);
    }

    public function productSizes(){
        return $this->hasMany(Productsize::class);
    }

    public function visits()
    {
        return $this->morphMany(Visit::class, 'visitable');
    }

    public function couponProducts(){
        return $this->hasMany(Couponproduct::class);
    }

    public function baskets(){
        return $this->hasMany(Basket::class);
    }

    public function orderDetails(){
        return $this->hasMany(Orderdetail::class);
    }
}
