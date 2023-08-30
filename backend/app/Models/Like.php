<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class Like extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'is_like',
        'likable_id',
        'likable_type',
    ];

    public function likable(): MorphTo
    {
        return $this->morphTo();
    }
}
