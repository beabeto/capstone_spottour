<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SpotRating extends Model
{
    protected $fillable = [
        'spot_id',
        'user_id',
        'rating'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function spot()
    {
        return $this->belongsTo(Spot::class);
    }
}