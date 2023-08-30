<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Becomesellerlegal extends Model
{
    use HasFactory;

    protected $table = 'becomesellerlegals';

    protected $fillable = [
        'becomeseller_id',
        'company_name',
        'company_type',
        'registration_number',
        'national_identity_number',
        'economic_number',
        'authorized_representative',
    ];

    public function becomeSeller(){
        return $this->belongsTo(Becomeseller::class);
    }
}
