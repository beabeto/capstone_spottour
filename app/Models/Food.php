<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Food extends Model
{
    use SoftDeletes;
    protected $table = 'foods';

    protected $fillable = [

        'owner_id',
        'name',
        'location',
        'description',
        'available_tables',
        'venue_capacity',
        'daily_booking_limit',
        'map_link',
        'latitude',
        'longitude',
        'image',
        'gallery_images',
        'status'

    ];

    protected function casts(): array
    {
        return ['gallery_images' => 'array'];
    }

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

public function packages()
{
    return $this->hasMany(Package::class);
}

public function owner()
{
    return $this->belongsTo(User::class, 'owner_id');
}
}
