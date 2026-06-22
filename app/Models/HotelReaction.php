<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class HotelReaction extends Model
{
    protected $fillable = [

        'user_id',

        'comment_id',

        'reaction'

    ];

    public function comment()
    {
        return $this->belongsTo(
            HotelComment::class,
            'comment_id'
        );
    }

    public function user()
    {
        return $this->belongsTo(
            User::class,
            'user_id'
        );
    }
}