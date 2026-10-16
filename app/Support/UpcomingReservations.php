<?php

namespace App\Support;

use App\Models\Booking;
use App\Models\FoodBooking;
use App\Models\HotelBooking;
use Illuminate\Support\Collection;

class UpcomingReservations
{
    private static function statuses(): array
    {
        return ['pending', 'pending_payment', 'pending_verification', 'approved'];
    }

    public static function list(?int $ownerId = null): Collection
    {
        return self::collect($ownerId, null)->take(6)->values();
    }

    public static function forUser(int $userId): ?object
    {
        return self::collect(null, $userId)->first();
    }

    public static function forUserList(int $userId, int $limit = 6): Collection
    {
        return self::collect(null, $userId)->take($limit)->values();
    }

    private static function collect(?int $ownerId, ?int $userId): Collection
    {
        $statuses = self::statuses();
        $today = now()->toDateString();
        $items = collect();

        $spots = Booking::with(['user', 'spot'])
            ->whereDate('booking_date', '>=', $today)
            ->whereIn('status', $statuses)
            ->when($ownerId, fn ($query) => $query->whereHas('spot', fn ($spot) => $spot->where('user_id', $ownerId)))
            ->when($userId, fn ($query) => $query->where('user_id', $userId))
            ->get();

        foreach ($spots as $booking) {
            if (! $booking->spot) {
                continue;
            }
            $items->push((object) [
                'id' => $booking->id,
                'type' => 'spot',
                'customer' => $booking->user?->name ?? 'Guest',
                'listing' => $booking->spot->name,
                'category' => 'Spot',
                'date' => $booking->booking_date,
                'status' => $booking->status,
                'spot' => $booking->spot,
                'hotel' => null,
                'food' => null,
                'url' => $userId ? '/user/bookings' : ($ownerId ? '/owner/bookings' : '/admin/bookings'),
            ]);
        }

        $hotels = HotelBooking::with(['user', 'hotel'])
            ->whereDate('check_in', '>=', $today)
            ->whereIn('status', $statuses)
            ->when($ownerId, fn ($query) => $query->whereHas('hotel', fn ($hotel) => $hotel->where('owner_id', $ownerId)))
            ->when($userId, fn ($query) => $query->where('user_id', $userId))
            ->get();

        foreach ($hotels as $booking) {
            if (! $booking->hotel) {
                continue;
            }
            $items->push((object) [
                'id' => $booking->id,
                'type' => 'hotel',
                'customer' => $booking->user?->name ?? 'Guest',
                'listing' => $booking->hotel->name,
                'category' => 'Hotel',
                'date' => $booking->check_in,
                'status' => $booking->status,
                'spot' => null,
                'hotel' => $booking->hotel,
                'food' => null,
                'url' => $userId ? '/user/bookings' : ($ownerId ? '/owner/bookings' : '/admin/bookings'),
            ]);
        }

        $foods = FoodBooking::with(['user', 'food'])
            ->whereDate('reservation_date', '>=', $today)
            ->whereIn('status', $statuses)
            ->when($ownerId, fn ($query) => $query->whereHas('food', fn ($food) => $food->where('owner_id', $ownerId)))
            ->when($userId, fn ($query) => $query->where('user_id', $userId))
            ->get();

        foreach ($foods as $booking) {
            if (! $booking->food) {
                continue;
            }
            $items->push((object) [
                'id' => $booking->id,
                'type' => 'food',
                'customer' => $booking->user?->name ?? 'Guest',
                'listing' => $booking->food->name,
                'category' => 'Restaurant',
                'date' => $booking->reservation_date,
                'status' => $booking->status,
                'spot' => null,
                'hotel' => null,
                'food' => $booking->food,
                'url' => $userId ? '/user/bookings' : ($ownerId ? '/owner/bookings' : '/admin/bookings'),
            ]);
        }

        return $items->sortBy(fn ($item) => (string) $item->date)->values();
    }
}
