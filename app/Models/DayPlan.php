<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DayPlan extends Model
{
    protected $fillable = [
        'user_id',
        'plan_date',
        'spot_id',
        'spot_time',
        'hotel_id',
        'hotel_time',
        'food_id',
        'food_time',
        'note',
    ];

    protected function casts(): array
    {
        return ['plan_date' => 'date'];
    }

    public function timeLabel(?string $field): string
    {
        $value = $this->{$field} ?? null;
        if (! $value) {
            return '';
        }

        try {
            return \Carbon\Carbon::parse($value)->format('g:i A');
        } catch (\Throwable) {
            return (string) $value;
        }
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function spot()
    {
        return $this->belongsTo(Spot::class)->withTrashed();
    }

    public function hotel()
    {
        return $this->belongsTo(Hotel::class)->withTrashed();
    }

    public function food()
    {
        return $this->belongsTo(Food::class)->withTrashed();
    }
}
