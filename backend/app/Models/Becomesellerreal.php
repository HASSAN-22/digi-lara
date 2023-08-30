<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Becomesellerreal extends Model
{
    use HasFactory;

    protected $table = 'becomesellerreals';

    protected $fillable = [
        'becomeseller_id',
        'name',
        'last_name',
        'birth_day',
        'gender',
        'identity_card_number',
        'national_identity_number',
    ];

    public function becomeSeller(){
        return $this->belongsTo(Becomeseller::class);
    }
}
