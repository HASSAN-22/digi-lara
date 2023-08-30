<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Ticketcategory extends Model
{
    use HasFactory;

    protected $table = 'ticketcategories';

    protected $fillable = ['title'];
}
