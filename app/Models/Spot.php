<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Schema;

class Spot extends Model
{
    use SoftDeletes;

    public static function bootSoftDeletes(): void
    {
        if (! Schema::hasTable('spots') || ! Schema::hasColumn('spots', 'deleted_at')) {
            return;
        }

        static::addGlobalScope(new \Illuminate\Database\Eloquent\SoftDeletingScope);
    }
    protected $fillable = [
        'user_id',
        'owner_id',
        'name',
        'location',
        'description',
        'price',
        'image',
        'gallery_images',
        'status',
       

'booking_type',

'available_cottages',

'cottage_price',

'daily_booking_limit',

'map_link',
        'latitude',
        'longitude',
    ];

    protected function casts(): array { return ['gallery_images' => 'array']; }

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

public function packages()
{
    return $this->hasMany(Package::class);
}

}
