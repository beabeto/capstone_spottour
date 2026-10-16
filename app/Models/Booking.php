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
    'contact_email',
    'payment_method',
    'payment_reference',
    'needs_guide',
    'package_id',
    'package_name',
    'arrival_code',
    'arrived_at',
    'status'

];

    protected function casts(): array
    {
        return [
            'arrived_at' => 'datetime',
            'needs_guide' => 'boolean',
        ];
    }

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
        )->withTrashed();
    }

    public function payment()
    {
        return $this->hasOne(
            Payment::class
        );
    }
}
