<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Ticketmessage extends Model
{
    use HasFactory;

    protected $table = 'ticketmessages';

    protected $fillable = ['user_id','ticket_id','message'];

    public function user(){
        return $this->belongsTo(User::class);
    }
}
