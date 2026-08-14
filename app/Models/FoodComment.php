<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class FoodComment extends Model
{
    protected $fillable = [
        'food_id',
        'user_id',
        'comment'
    ];

    public function user()
    {
        return $this->belongsTo(
            User::class
        );
    }

    public function food()
    {
        return $this->belongsTo(
            Food::class
        );
    }

    public function reactions()
{
    return $this->hasMany(FoodReaction::class, 'food_comment_id');
}

public function replies()
{
    return $this->hasMany(FoodReply::class, 'food_comment_id');
}

public function ratings()
{
    return $this->hasMany(FoodCommentRating::class, 'food_comment_id');
}

 
}