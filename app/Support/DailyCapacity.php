<?php

namespace App\Support;

use App\Models\Booking;
use App\Models\Food;
use App\Models\FoodBooking;
use App\Models\Hotel;
use App\Models\HotelBooking;
use App\Models\Spot;
use Carbon\Carbon;
use Carbon\CarbonPeriod;
use Illuminate\Validation\ValidationException;

class DailyCapacity
{
    public static function place(string $type, int $id): Spot|Hotel|Food
    {
        return match ($type) {
            'hotel' => Hotel::findOrFail($id),
            'food' => Food::findOrFail($id),
            default => Spot::findOrFail($id),
        };
    }

    public static function counts(string $type, int $id): array
    {
        $statuses = ['pending', 'pending_payment', 'pending_verification', 'approved'];
        $counts = [];

        if ($type === 'hotel') {
            $stays = HotelBooking::where('hotel_id', $id)->whereIn('status', $statuses)->get(['check_in', 'check_out']);
            foreach ($stays as $stay) {
                foreach (self::stayDates($stay->check_in, $stay->check_out) as $date) {
                    $counts[$date] = ($counts[$date] ?? 0) + 1;
                }
            }

            return $counts;
        }

        $column = $type === 'food' ? 'reservation_date' : 'booking_date';
        $query = $type === 'food'
            ? FoodBooking::where('food_id', $id)
            : Booking::where('spot_id', $id);

        foreach ($query->whereIn('status', $statuses)->pluck($column) as $date) {
            $key = Carbon::parse($date)->toDateString();
            $counts[$key] = ($counts[$key] ?? 0) + 1;
        }

        return $counts;
    }

    public static function snapshot(string $type, int $id): array
    {
        $place = self::place($type, $id);
        $limit = $place->daily_booking_limit ? (int) $place->daily_booking_limit : null;
        $dates = [];

        foreach (self::counts($type, $id) as $date => $booked) {
            $dates[] = [
                'date' => $date,
                'booked' => $booked,
                'full' => $limit !== null && $booked >= $limit,
            ];
        }

        return [
            'limit' => $limit,
            'dates' => $dates,
            'unavailable_dates' => collect($dates)->where('full', true)->pluck('date')->values(),
        ];
    }

    public static function ensure(string $type, Spot|Hotel|Food $place, array $dates): void
    {
        $limit = $place->daily_booking_limit ? (int) $place->daily_booking_limit : null;
        if (! $limit) {
            return;
        }

        $counts = self::counts($type, $place->id);
        foreach ($dates as $date) {
            $key = Carbon::parse($date)->toDateString();
            if (($counts[$key] ?? 0) >= $limit) {
                throw ValidationException::withMessages([
                    'booking_date' => Carbon::parse($key)->format('F j').' is fully booked. This place accepts '.$limit.' booking'.($limit === 1 ? '' : 's').' per day.',
                ]);
            }
        }
    }

    public static function stayDates($start, $end): array
    {
        $from = Carbon::parse($start)->startOfDay();
        $until = Carbon::parse($end)->startOfDay();
        if ($until->lte($from)) {
            return [$from->toDateString()];
        }

        return collect(CarbonPeriod::create($from, $until->copy()->subDay()))
            ->map(fn (Carbon $date) => $date->toDateString())
            ->all();
    }
}
