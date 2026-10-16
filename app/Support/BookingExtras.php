<?php

namespace App\Support;

use App\Models\HotelRoom;
use App\Models\Package;
use Illuminate\Http\Request;

class BookingExtras
{
    public static function package(Request $request, string $column, int $placeId): ?Package
    {
        if (! $request->filled('package_id')) {
            return null;
        }

        return Package::where('id', $request->integer('package_id'))
            ->where($column, $placeId)
            ->first();
    }

    public static function room(Request $request, int $hotelId): ?HotelRoom
    {
        if (! $request->filled('hotel_room_id')) {
            return null;
        }

        return HotelRoom::where('hotel_id', $hotelId)
            ->find($request->integer('hotel_room_id'));
    }

    public static function addPrice(float $total, ?Package $package): float
    {
        return $total + (float) ($package->price ?? 0);
    }
}
