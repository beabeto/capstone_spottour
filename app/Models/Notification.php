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

    'comment_id',

    'message',

    'is_read'

];

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
}