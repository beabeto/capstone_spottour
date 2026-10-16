<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Package extends Model
{
    protected $fillable = [
        'spot_id',
        'hotel_id',
        'food_id',
        'name',
        'kind',
        'description',
        'price',
    ];

    public function spot()
    {
        return $this->belongsTo(Spot::class);
    }

    public function hotel()
    {
        return $this->belongsTo(Hotel::class);
    }

    public function food()
    {
        return $this->belongsTo(Food::class);
    }

    public function placeName(): string
    {
        return $this->spot->name ?? $this->hotel->name ?? $this->food->name ?? 'Listing';
    }
}
