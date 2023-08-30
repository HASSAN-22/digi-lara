<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Productproperty extends Model
{
    use HasFactory;

    public $timestamps = false;

    protected $table = 'productproperties';

    protected $fillable = [
        'product_id',
        'property_id',
        'propertytype_id'
    ];

    public function product(){
        return $this->belongsTo(Product::class);
    }

    public function property(){
        return $this->belongsTo(Property::class);
    }

    public function propertyType(){
        return $this->belongsTo(Propertytype::class,'propertytype_id','id');
    }
}
