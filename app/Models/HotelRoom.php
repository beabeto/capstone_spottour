<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
class HotelRoom extends Model
{
    public static function types(): array
    {
        return [
            'Single Room (1 pax)' => 1,
            'Twin Room (2 pax)' => 2,
            'Double Room (2 pax)' => 2,
            'Deluxe Room (2 pax)' => 2,
            'Triple Room (3 pax)' => 3,
            'Family Room (4 pax)' => 4,
            'Suite (4 pax)' => 4,
            'Dormitory (6 pax)' => 6,
        ];
    }

    protected $fillable = ['hotel_id', 'name', 'price_per_night', 'available_rooms', 'cover_image', 'gallery_images'];
    protected function casts(): array { return ['gallery_images' => 'array']; }
    public function hotel() { return $this->belongsTo(Hotel::class); }
}
