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

        'status'

    ];

    public function hotel()
    {
        return $this->belongsTo(
            Hotel::class,
            'hotel_id'
        );
    }

    public function user()
    {
        return $this->belongsTo(
            User::class,
            'user_id'
        );
    }
}