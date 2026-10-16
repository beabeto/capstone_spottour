<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SpotComment extends Model
{
    protected $fillable = [

        'spot_id',

        'user_id',

        'comment',
        'image'

    ];

    public function user()
    {
        return $this->belongsTo(
            User::class
        );
    }

    public function spot()
    {
        return $this->belongsTo(
            Spot::class
        );
    }

    public function replies()
    {
        return $this->hasMany(
            CommentReply::class,
            'comment_id'
        );
    }

    public function reactions()
    {
        return $this->hasMany(
            CommentReaction::class,
            'comment_id'
        );
    }

    public function ratings()
    {
        return $this->hasMany(
            CommentRating::class,
            'comment_id'
        );
    }
}
