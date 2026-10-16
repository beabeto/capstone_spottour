<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Hotel extends Model
{
    use SoftDeletes;
    protected $fillable = [

        'owner_id',

        'name',

        'location',

        'description',

        'image',

        'price_per_night',

        'available_rooms',

        'daily_booking_limit',

        'room_types',

        'amenities',

        'map_link',

        'latitude',

        'longitude',

        'status'

    ];

    protected function casts(): array
    {
        return ['room_types' => 'array'];
    }

    public function rooms()
    {
        return $this->hasMany(HotelRoom::class);
    }

    public function packages()
    {
        return $this->hasMany(Package::class);
    }

    /*
    |--------------------------------------------------------------------------
    | Owner
    |--------------------------------------------------------------------------
    */

    public function owner()
    {
        return $this->belongsTo(
            User::class,
            'owner_id'
        );
    }

    /*
    |--------------------------------------------------------------------------
    | Ratings
    |--------------------------------------------------------------------------
    */

    public function ratings()
    {
        return $this->hasMany(
            HotelRating::class,
            'hotel_id'
        );
    }

    /*
    |--------------------------------------------------------------------------
    | Comments
    |--------------------------------------------------------------------------
    */

    public function comments()
    {
        return $this->hasMany(
            HotelComment::class,
            'hotel_id'
        );
    }

    /*
    |--------------------------------------------------------------------------
    | Favorites
    |--------------------------------------------------------------------------
    */

    public function favorites()
    {
        return $this->hasMany(
            HotelFavorite::class,
            'hotel_id'
        );
    }

    /*
    |--------------------------------------------------------------------------
    | Bookmarks
    |--------------------------------------------------------------------------
    */

    public function bookmarks()
    {
        return $this->hasMany(
            HotelBookmark::class,
            'hotel_id'
        );
    }

    /*
    |--------------------------------------------------------------------------
    | Checklists
    |--------------------------------------------------------------------------
    */

    public function checklists()
    {
        return $this->hasMany(
            HotelChecklist::class,
            'hotel_id'
        );
    }

    /*
    |--------------------------------------------------------------------------
    | Bookings
    |--------------------------------------------------------------------------
    */

    public function bookings()
    {
        return $this->hasMany(
            HotelBooking::class,
            'hotel_id'
        );
    }

    /*
    |--------------------------------------------------------------------------
    | Payments
    |--------------------------------------------------------------------------
    */

    public function payments()
    {
        return $this->hasMany(
            HotelPayment::class,
            'hotel_id'
        );
    }

    
}
