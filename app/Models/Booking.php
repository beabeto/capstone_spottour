<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Booking extends Model
{
    protected $fillable = [

    'user_id',
    'spot_id',
    'booking_date',
    'quantity',
    'total_price',
    'receipt',
    'payer_name',
    'payer_number',
    'status'

];

    public function user()
    {
        return $this->belongsTo(
            User::class
        );
    }

    public function spot()
    {
        return $this->belongsTo(
            Spot::class
        );
    }

    public function payment()
    {
        return $this->hasOne(
            Payment::class
        );
    }
}