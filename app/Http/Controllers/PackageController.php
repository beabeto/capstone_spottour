<?php

namespace App\Http\Controllers;

use App\Models\Food;
use App\Models\Hotel;
use App\Models\Package;
use App\Models\Spot;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PackageController extends Controller
{
    public function index()
    {
        $ownerId = Auth::id();
        $spots = Spot::where('user_id', $ownerId)->with('packages')->get();
        $hotels = Hotel::where('owner_id', $ownerId)->with('packages')->get();
        $foods = Food::where('owner_id', $ownerId)->with('packages')->get();

        return view('owner.packages', compact('spots', 'hotels', 'foods'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'place' => 'required|string',
            'name' => 'required|string|max:100',
            'kind' => 'required|in:guide,service,event',
            'description' => 'nullable|string|max:500',
            'price' => 'nullable|numeric|min:0',
        ]);

        [$type, $id] = array_pad(explode(':', (string) $request->place, 2), 2, null);
        $placeId = (int) $id;
        $ownerId = Auth::id();

        $owned = match ($type) {
            'spot' => Spot::where('user_id', $ownerId)->where('id', $placeId)->exists(),
            'hotel' => Hotel::where('owner_id', $ownerId)->where('id', $placeId)->exists(),
            'food' => Food::where('owner_id', $ownerId)->where('id', $placeId)->exists(),
            default => false,
        };

        abort_unless($owned, 403);

        Package::create([
            $type.'_id' => $placeId,
            'name' => $request->name,
            'kind' => $request->kind,
            'description' => $request->description,
            'price' => $request->filled('price') ? $request->price : null,
        ]);

        return back()->with('success', 'Package added.');
    }

    public function destroy($id)
    {
        $package = Package::with(['spot', 'hotel', 'food'])->findOrFail($id);
        $ownerId = Auth::id();
        $owned = ($package->spot && $package->spot->user_id === $ownerId)
            || ($package->hotel && $package->hotel->owner_id === $ownerId)
            || ($package->food && $package->food->owner_id === $ownerId);

        abort_unless($owned, 403);
        $package->delete();

        return back()->with('success', 'Package removed.');
    }
}
