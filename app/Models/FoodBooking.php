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
    'contact_email',
    'payment_method',
    'payment_reference',
    'receipt',
    'total_price',
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

    public function food()
    {
        return $this->belongsTo(Food::class)->withTrashed();
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
