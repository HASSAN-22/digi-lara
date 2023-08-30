<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Profile extends Model
{
    use HasFactory;

    protected $appends = ['ir_birth_day'];

    protected $fillable = [
        'user_id',
        'work_id',
        'name',
        'national_id',
        'email',
        'refund_method',
        'birth_day',
        'shaba',
    ];

    public function getIrBirthDayAttribute(){
        return dateToPersian($this->birth_day);
    }

    public function user(){
        return $this->belongsTo(User::class);
    }

    public function work(){
        return $this->belongsTo(Work::class);
    }
}
