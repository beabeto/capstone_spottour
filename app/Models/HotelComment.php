<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class HotelComment extends Model
{
    protected $fillable = [

        'user_id',

        'hotel_id',

        'comment'

    ];

    public function user()
{
    return $this->belongsTo(
        User::class,
        'user_id'
    );
}

public function hotel()
{
    return $this->belongsTo(
        Hotel::class,
        'hotel_id'
    );
}

    public function replies()
    {
        return $this->hasMany(
            HotelReply::class,
            'comment_id'
        );
    }

    public function ratings()
    {
        return $this->hasMany(
            HotelCommentRating::class,
            'comment_id'
        );
    }

    public function reactions()
    {
        return $this->hasMany(
            HotelReaction::class,
            'comment_id'
        );
    }
}