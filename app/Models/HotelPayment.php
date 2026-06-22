<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class HotelPayment extends Model
{
    protected $fillable = [

        'hotel_id',

        'booking_id',

        'receipt',

        'amount',

        'status'

    ];

    public function hotel()
    {
        return $this->belongsTo(
            Hotel::class,
            'hotel_id'
        );
    }

    public function booking()
    {
        return $this->belongsTo(
            HotelBooking::class,
            'booking_id'
        );
    }
}