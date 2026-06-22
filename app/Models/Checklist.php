<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Checklist extends Model
{
    protected $fillable = [
        'user_id',
        'spot_id'
    ];

    public function spot()
{
    return $this->belongsTo(Spot::class);
}
}