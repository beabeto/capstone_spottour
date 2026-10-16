<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CommentReply extends Model
{
    protected $fillable = [

        'comment_id',

        'user_id',

        'reply',

        'parent_reply_id',
        'image'

    ];

    public function comment()
    {
        return $this->belongsTo(
            SpotComment::class,
            'comment_id'
        );
    }

    public function user()
    {
        return $this->belongsTo(
            User::class
        );
    }

    public function parent()
{
    return $this->belongsTo(
        CommentReply::class,
        'parent_reply_id'
    );
}

public function children()
{
    return $this->hasMany(
        CommentReply::class,
        'parent_reply_id'
    );
}

public function ratings()
{
    return $this->hasMany(CommentReplyRating::class, 'reply_id');
}
}
