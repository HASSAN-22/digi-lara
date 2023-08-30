<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Legalinfo extends Model
{
    use HasFactory;

    protected $table = 'legalinfos';

    protected $fillable = [
        'user_id',
        'province_id',
        'city_id',
        'organization_name',
        'economic_code',
        'national_id',
        'registration_id',
        'phone',
    ];

    public function user(){
        return $this->belongsTo(User::class);
    }

    public function province(){
        return $this->belongsTo(Province::class);
    }

    public function city(){
        return $this->belongsTo(City::class);
    }
}
