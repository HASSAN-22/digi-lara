<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Propertytype extends Model
{
    use HasFactory;

    public $timestamps = false;

    protected $table = 'propertytypes';

    protected $fillable = ['property_id','name'];

    public function property(){
        return $this->belongsTo(Property::class);
    }
}
