<?php

namespace App\Support;

use App\Models\Booking;
use App\Models\Food;
use App\Models\FoodBooking;
use App\Models\Hotel;
use App\Models\HotelBooking;
use App\Models\Spot;
use Carbon\Carbon;
use Illuminate\Support\Collection;

class VisitorReport
{
    public static function month(Carbon $month): Collection
    {
        $rows = collect();

        $sources = [
            [Booking::class, 'spot_id', 'booking_date', Spot::class, 'Tourist spot', 'quantity'],
            [HotelBooking::class, 'hotel_id', 'check_in', Hotel::class, 'Hotel', 'rooms'],
            [FoodBooking::class, 'food_id', 'reservation_date', Food::class, 'Restaurant', 'guests'],
        ];

        foreach ($sources as [$model, $key, $date, $place, $kind, $units]) {
            $grouped = $model::query()
                ->whereYear($date, $month->year)
                ->whereMonth($date, $month->month)
                ->selectRaw($key.' as place_id, COUNT(*) as bookings, COALESCE(SUM('.$units.'),0) as units, COALESCE(SUM(CASE WHEN arrived_at IS NOT NULL THEN 1 ELSE 0 END),0) as arrived, COALESCE(SUM(CASE WHEN status = \'approved\' THEN total_price ELSE 0 END),0) as revenue')
                ->groupBy($key)
                ->get();

            $names = $place::withTrashed()->whereIn('id', $grouped->pluck('place_id'))->pluck('name', 'id');

            foreach ($grouped as $row) {
                $rows->push([
                    'name' => $names->get($row->place_id) ?? 'Removed listing',
                    'kind' => $kind,
                    'bookings' => (int) $row->bookings,
                    'units' => (int) $row->units,
                    'arrived' => (int) $row->arrived,
                    'revenue' => (float) $row->revenue,
                ]);
            }
        }

        return $rows->sortByDesc('bookings')->values();
    }
}
