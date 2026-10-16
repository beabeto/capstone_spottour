<?php

namespace App\Support;

use App\Models\FoodMenu;
use App\Models\HotelRoom;
use App\Models\Package;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;

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

    public static function menus(Request $request, int $foodId): Collection
    {
        $quantities = collect($request->input('menu_qty', []));
        if ($quantities->isEmpty() && $request->filled('menu_items')) {
            $decoded = json_decode((string) $request->input('menu_items'), true);
            if (is_array($decoded)) {
                $quantities = collect($decoded)
                    ->mapWithKeys(fn ($row) => [(int) ($row['id'] ?? 0) => (int) ($row['qty'] ?? 0)]);
            }
        }

        $ids = $quantities
            ->filter(fn ($qty) => (int) $qty > 0)
            ->keys()
            ->map(fn ($id) => (int) $id)
            ->filter()
            ->values();

        if ($ids->isEmpty()) {
            return collect();
        }

        return FoodMenu::where('food_id', $foodId)
            ->whereIn('id', $ids)
            ->get()
            ->map(function (FoodMenu $menu) use ($quantities) {
                $qty = max(0, (int) $quantities->get($menu->id, $quantities->get((string) $menu->id, 0)));
                $price = (float) $menu->price;

                return [
                    'id' => $menu->id,
                    'name' => $menu->name,
                    'category' => $menu->category,
                    'price' => $price,
                    'qty' => $qty,
                    'line_total' => $price * $qty,
                    'image' => $menu->image,
                ];
            })
            ->filter(fn (array $row) => $row['qty'] > 0)
            ->values();
    }

    public static function menuTotal(Collection $menus): float
    {
        return (float) $menus->sum('line_total');
    }

    public static function addPrice(float $total, ?Package $package, float $extra = 0): float
    {
        return $total + (float) ($package->price ?? 0) + $extra;
    }
}
