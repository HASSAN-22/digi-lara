<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Shopconfig extends Model
{
    use HasFactory;

    public $timestamps = false;

    protected $table = 'shopconfigs';

    protected $fillable = [
      'option',
      'value'
    ];

    public static function getValue(string $option){
        return self::where('option',$option)->first();
    }
}
