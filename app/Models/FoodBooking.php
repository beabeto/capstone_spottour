<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class FoodBooking extends Model
{
    protected $fillable = [
    'user_id',
    'food_id',
    'reservation_date',
    'reservation_time',
    'guests',
    'special_request',
    'payer_name',
    'payer_number',
    'receipt',
    'total_price',
    'status'
];

    public function food()
    {
        return $this->belongsTo(Food::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}