<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Food extends Model
{
    protected $table = 'foods';

    protected $fillable = [

        'owner_id',
        'name',
        'location',
        'description',
        'available_tables',
        'venue_capacity',
        'map_link',
        'image',
        'status'

    ];

    public function menus()
{
    return $this->hasMany(FoodMenu::class);
}

public function comments()
{
    return $this->hasMany(FoodComment::class);
}

public function ratings()
{
    return $this->hasMany(FoodRating::class);
}

public function favorites()
{
    return $this->hasMany(FoodFavorite::class);
}

public function bookmarks()
{
    return $this->hasMany(FoodBookmark::class);
}

public function checklists()
{
    return $this->hasMany(FoodChecklist::class);
}

public function bookings()
{
    return $this->hasMany(FoodBooking::class);
}

public function owner()
{
    return $this->belongsTo(User::class, 'owner_id');
}
}