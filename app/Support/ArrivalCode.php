<?php

namespace App\Support;

use App\Models\Booking;
use App\Models\FoodBooking;
use App\Models\HotelBooking;
use Illuminate\Database\Eloquent\Model;

class ArrivalCode
{
    public static function ensure(Model $booking): ?string
    {
        if ($booking->status !== 'approved') {
            return $booking->arrival_code;
        }

        if (filled($booking->arrival_code)) {
            return $booking->arrival_code;
        }

        do {
            $code = 'ST-'.strtoupper(substr(bin2hex(random_bytes(3)), 0, 6));
        } while (self::taken($code));

        $booking->forceFill(['arrival_code' => $code])->save();

        return $code;
    }

    public static function find(string $code): ?array
    {
        $code = strtoupper(trim($code));
        if ($code === '') {
            return null;
        }

        $spot = Booking::with('spot')->where('arrival_code', $code)->first();
        if ($spot) {
            return ['spot', $spot, (int) ($spot->spot->user_id ?? 0), $spot->spot->name ?? 'Removed tourist spot'];
        }

        $hotel = HotelBooking::with('hotel')->where('arrival_code', $code)->first();
        if ($hotel) {
            return ['hotel', $hotel, (int) ($hotel->hotel->owner_id ?? 0), $hotel->hotel->name ?? 'Removed hotel'];
        }

        $food = FoodBooking::with('food')->where('arrival_code', $code)->first();
        if ($food) {
            return ['food', $food, (int) ($food->food->owner_id ?? 0), $food->food->name ?? 'Removed restaurant'];
        }

        return null;
    }

    private static function taken(string $code): bool
    {
        return Booking::where('arrival_code', $code)->exists()
            || HotelBooking::where('arrival_code', $code)->exists()
            || FoodBooking::where('arrival_code', $code)->exists();
    }
}
