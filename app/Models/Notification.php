<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Notification extends Model
{
    protected $fillable = [

    'user_id',

    'sender_id',

    'spot_id',

     'hotel_id',

    'food_id',

    'comment_id',

    'message',

    'is_read',

    'category',

    'chat_thread_id',

];

    protected static function booted(): void
    {
        static::creating(function (Notification $notification) {
            if (filled($notification->category)) {
                return;
            }

            $message = strtolower((string) $notification->message);

            if (str_contains($message, 'booking')
                || str_contains($message, 'reservation')
                || str_contains($message, 'payment')
                || str_contains($message, 'paid')) {
                $notification->category = 'booking';
            }
        });
    }

    public function receiver()
    {
        return $this->belongsTo(
            User::class,
            'user_id'
        );
    }

    public function sender()
{
    return $this->belongsTo(
        User::class,
        'sender_id'
    );
}

public function spot()
{
    return $this->belongsTo(
        Spot::class,
        'spot_id'
    );
}

public function comment()
{
    return $this->belongsTo(
        SpotComment::class,
        'comment_id'
    );
}

public function hotel()
{
    return $this->belongsTo(
        Hotel::class,
        'hotel_id'
    );
}

public function food()
{
    return $this->belongsTo(Food::class, 'food_id');
}
}
