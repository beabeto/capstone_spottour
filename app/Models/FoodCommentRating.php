<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class FoodCommentRating extends Model
{
    protected $fillable = [
        'food_comment_id',
        'user_id',
        'rating',
    ];

    public function comment()
    {
        return $this->belongsTo(FoodComment::class, 'food_comment_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}