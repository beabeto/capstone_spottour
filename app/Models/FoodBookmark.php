<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class FoodBookmark extends Model
{
    protected $fillable = [
        'food_id',
        'user_id',
    ];

    public function food()
    {
        return $this->belongsTo(Food::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}