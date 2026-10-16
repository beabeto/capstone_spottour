<?php

namespace App\Http\Controllers;

use App\Models\DayPlan;
use App\Models\Food;
use App\Models\Hotel;
use App\Models\Spot;
use App\Support\PlaceMap;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DayPlanController extends Controller
{
    public function index()
    {
        if (! Auth::check()) {
            return redirect('/');
        }

        $plans = DayPlan::with(['spot', 'hotel', 'food'])
            ->where('user_id', Auth::id())
            ->latest()
            ->take(6)
            ->get();

        return view('user.day-plan', [
            'spots' => Spot::where('status', 'approved')->orderBy('name')->get(),
            'hotels' => Hotel::where('status', 'approved')->orderBy('name')->get(),
            'foods' => Food::where('status', 'approved')->orderBy('name')->get(),
            'plans' => $plans,
            'plan' => $plans->first(),
            'pins' => $plans->first() ? $this->pins($plans->first()) : [],
        ]);
    }

    public function store(Request $request)
    {
        if (! Auth::check()) {
            return redirect('/');
        }

        $data = $request->validate([
            'plan_date' => 'required|date',
            'spot_id' => 'nullable|exists:spots,id',
            'spot_time' => 'nullable|date_format:H:i',
            'hotel_id' => 'nullable|exists:hotels,id',
            'hotel_time' => 'nullable|date_format:H:i',
            'food_id' => 'nullable|exists:foods,id',
            'food_time' => 'nullable|date_format:H:i',
            'note' => 'nullable|string|max:200',
        ]);

        if (! $request->filled('spot_id') && ! $request->filled('hotel_id') && ! $request->filled('food_id')) {
            return back()->withErrors(['plan' => 'Choose at least a spot, a stay, or a restaurant.'])->withInput();
        }

        DayPlan::create([
            'user_id' => Auth::id(),
            'plan_date' => $data['plan_date'],
            'spot_id' => $data['spot_id'] ?? null,
            'spot_time' => $request->filled('spot_id') ? ($data['spot_time'] ?? '08:00') : null,
            'hotel_id' => $data['hotel_id'] ?? null,
            'hotel_time' => $request->filled('hotel_id') ? ($data['hotel_time'] ?? '14:00') : null,
            'food_id' => $data['food_id'] ?? null,
            'food_time' => $request->filled('food_id') ? ($data['food_time'] ?? '12:00') : null,
            'note' => $data['note'] ?? null,
        ]);

        return redirect('/user/plan')->with('success', 'Your Surigao day plan is ready. Book each stop below to pay.');
    }

    public function adminIndex()
    {
        abort_unless(Auth::user()?->role === 'admin', 403);

        $plans = DayPlan::with(['user', 'spot', 'hotel', 'food'])
            ->whereDate('plan_date', '>=', now()->toDateString())
            ->orderBy('plan_date')
            ->latest()
            ->paginate(20);

        return view('admin.day-plans', compact('plans'));
    }

    public function ownerIndex()
    {
        abort_unless(Auth::check() && in_array(Auth::user()->role, ['owner', 'admin'], true), 403);

        $ownerId = Auth::id();
        $plans = DayPlan::with(['user', 'spot', 'hotel', 'food'])
            ->whereDate('plan_date', '>=', now()->toDateString())
            ->where(function ($query) use ($ownerId) {
                $query->whereHas('spot', fn ($spot) => $spot->where('user_id', $ownerId))
                    ->orWhereHas('hotel', fn ($hotel) => $hotel->where('owner_id', $ownerId))
                    ->orWhereHas('food', fn ($food) => $food->where('owner_id', $ownerId));
            })
            ->orderBy('plan_date')
            ->latest()
            ->paginate(20);

        return view('owner.day-plans', compact('plans'));
    }

    private function pins(DayPlan $plan): array
    {
        $pins = [];

        foreach ([
            ['Morning', $plan->spot, $plan->timeLabel('spot_time'), '/spots/'],
            ['Meal', $plan->food, $plan->timeLabel('food_time'), '/foods/'],
            ['Stay', $plan->hotel, $plan->timeLabel('hotel_time'), '/hotels/'],
        ] as [$label, $place, $time, $prefix]) {
            if (! $place) {
                continue;
            }
            $coords = PlaceMap::coordinates($place->latitude, $place->longitude, $place->map_link);
            $pins[] = [
                'label' => trim($label.($time ? ' '.$time : '').': '.$place->name),
                'url' => $prefix.$place->id,
                'lat' => $coords[0] ?? null,
                'lng' => $coords[1] ?? null,
            ];
        }

        return $pins;
    }
}
