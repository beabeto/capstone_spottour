<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Spot extends Model
{
    protected $fillable = [
        'user_id',
        'name',
        'location',
        'description',
        'price',
        'image',
        'status',
       

'booking_type',

'available_cottages',

'cottage_price',

'map_link',
    ];

    public function owner()
    {
        return $this->belongsTo(
            User::class,
            'user_id'
        );
    }

    public function ratings()
{
    return $this->hasMany(
        SpotRating::class
    );
}

public function comments()
{
    return $this->hasMany(
        SpotComment::class
    );
}

public function bookings()
{
    return $this->hasMany(Booking::class);
}

}