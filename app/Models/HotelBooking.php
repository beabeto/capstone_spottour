<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class HotelBooking extends Model
{
    protected $fillable = [

        'user_id',

        'hotel_id',

        'check_in',

        'check_out',

        'rooms',

        'total_price',

        'reference_no',

        'receipt',

        'payer_name',

        'payer_number',

        'contact_email',

        'payment_method',

        'payment_reference',

        'hotel_room_id',

        'room_name',

        'package_id',

        'package_name',

        'arrival_code',

        'arrived_at',

        'status'

    ];

    protected function casts(): array
    {
        return ['arrived_at' => 'datetime'];
    }

    public function hotel()
    {
        return $this->belongsTo(
            Hotel::class,
            'hotel_id'
        )->withTrashed();
    }

    public function user()
    {
        return $this->belongsTo(
            User::class,
            'user_id'
        );
    }
}
