<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class FoodReply extends Model
{
    protected $fillable = [
        'food_comment_id',
        'user_id',
        'parent_reply_id',
        'reply',
        'image',
    ];

    public function comment()
    {
        return $this->belongsTo(FoodComment::class, 'food_comment_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function parentReply()
    {
        return $this->belongsTo(FoodReply::class, 'parent_reply_id');
    }

    public function replies()
    {
        return $this->hasMany(FoodReply::class, 'parent_reply_id');
    }

    public function ratings()
    {
        return $this->hasMany(FoodReplyRating::class, 'reply_id');
    }
}
