<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class HotelReply extends Model
{
    protected $fillable = [

        'user_id',

        'comment_id',

        'reply',

        'parent_reply_id',
        'image'

    ];

    public function user()
    {
        return $this->belongsTo(
            User::class
        );
    }

    public function comment()
    {
        return $this->belongsTo(
            HotelComment::class,
            'comment_id'
        );
    }

    public function ratings()
    {
        return $this->hasMany(HotelReplyRating::class, 'reply_id');
    }
}
