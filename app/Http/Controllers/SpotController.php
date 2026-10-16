<?php

namespace App\Http\Controllers;
use App\Models\SpotRating;
use App\Models\SpotComment;


use App\Models\Spot;
use App\Models\User;
use Illuminate\Http\Request;
use App\Support\BookingNotice;
use Illuminate\Support\Facades\Auth;

class SpotController extends Controller
{
    // USER VIEW

    public function userIndex()
    {
        $spots = Spot::where('status', 'approved')
            ->withAvg('ratings', 'rating')
            ->latest()
            ->get();

        return view(
            'user.spots',
            compact('spots')
        );
    }

    // OWNER VIEW

    public function ownerIndex()
    {
        $spots = Spot::where(
            'user_id',
            Auth::id()
        )->get();

        return view(
            'owner.spots',
            compact('spots')
        );
    }

    // ADMIN VIEW

    public function adminIndex()
    {
          $spots = Spot::with('owner')->latest()->get();
        $deletedSpots = Spot::onlyTrashed()->latest('deleted_at')->get();

        return view(
            'admin.spots',
            compact('spots', 'deletedSpots')
        );
    }

    // SHOW ADD SPOT PAGE

    public function create()
    {
        abort_if(request()->is('admin/*') && Auth::user()->role !== 'admin', 403);
        $owners = Auth::user()->role === 'admin'
            ? User::where('role', 'owner')->orderBy('name')->get()
            : collect();

        return view('owner.create-spot', compact('owners'));
    }

    // SAVE SPOT

    public function store(Request $request)
    {
        $isAdmin = Auth::user()->role === 'admin';

        $request->validate([
            'name' => 'required',
            'location' => 'required',
            'description' => 'required',
            'gallery_images.*' => 'nullable|image|mimes:jpeg,png,webp|max:5120',
            'owner_id' => $isAdmin ? 'required|exists:users,id,role,owner' : 'nullable',
        ]);

        $imageName = null;

        if ($request->hasFile('image'))
        {
            $imageName =
                time() . '.' .
                $request->image->extension();

            $request->image->move(
                public_path('uploads/spots'),
                $imageName
            );
        }

        $gallery = [];
        foreach ($request->file('gallery_images', []) as $file) {
            $name = time().'_'.uniqid().'_'.$file->getClientOriginalName();
            $file->move(public_path('uploads/spots'), $name);
            $gallery[] = $name;
        }

        $coords = \App\Support\PlaceMap::locate(
            $request->latitude,
            $request->longitude,
            $request->map_link,
            (string) $request->name,
            (string) $request->location
        );

        $spot = Spot::create([

            'user_id' => $isAdmin ? $request->integer('owner_id') : Auth::id(),

            'name' => $request->name,

            'location' => $request->location,

            

            'description' => $request->description,

            'price' => $request->price,

            'daily_booking_limit' => $request->filled('daily_booking_limit') ? max(1, $request->integer('daily_booking_limit')) : null,

            'image' => $imageName,
            'gallery_images' => $gallery,

            'status' => $isAdmin ? 'approved' : 'pending',

            'booking_type' =>
    $request->booking_type,

'available_cottages' =>
    $request->available_cottages,

'cottage_price' =>
    $request->cottage_price,

    'map_link' => $request->map_link,
            'latitude' => $coords['latitude'],
            'longitude' => $coords['longitude'],

        ]);

        if (!$isAdmin) {
            BookingNotice::admins(
                Auth::user()->name.' submitted the tourist spot "'.$spot->name.'" for review.',
                ['spot_id' => $spot->id],
                [],
                'listing'
            );
        }

        return redirect($isAdmin ? '/admin/spots' : '/owner/spots')
            ->with(
                'success',
                'Spot added successfully!'
            );
    }

public function show($id)
{
    $spot = Spot::with([

        'ratings.user',

        'comments.user',

        'comments.replies.user',

        'comments.ratings',

        'comments.reactions'

    ])->findOrFail($id);

    return view(
        'user.spot-details',
        compact('spot')
    );
}

public function rate(Request $request)
{
    SpotRating::create([

        'spot_id' =>
        $request->spot_id,

        'user_id' =>
        Auth::id(),

        'rating' =>
        $request->rating

    ]);

    return back();
}

public function comment(Request $request)
{
    $request->validate(['comment' => 'required|string|max:1000', 'image' => 'nullable|image|mimes:jpeg,png,webp|max:5120']);
    $image = $request->hasFile('image') ? time().'_'.$request->file('image')->getClientOriginalName() : null;
    if ($image) $request->file('image')->move(public_path('uploads/comments'), $image);
    SpotComment::create([

        'spot_id' =>
        $request->spot_id,

        'user_id' =>
        Auth::id(),

        'comment' =>
        $request->comment,

        'image' => $image

    ]);

    return back()->with(
    'success',
    ' Comment posted.'
);
}

public function ownerShow($id)
{
    $spot = Spot::with([

        'ratings.user',

        'comments.user',

        'comments.replies.user',

        'comments.ratings',

        'comments.reactions'

    ])->findOrFail($id);

    return view(
        'owner.spot-details',
        compact('spot')
    );
}

public function edit($id)
{
    $spot = Spot::findOrFail($id);

    return view(
        'owner.edit-spot',
        compact('spot')
    );
}

public function update(
    Request $request,
    $id
)
{
    $spot = Spot::findOrFail($id);

    $imageName = $spot->image;

    if($request->hasFile('image'))
    {
        $imageName =
            time().'.'.
            $request->image->extension();

        $request->image->move(
            public_path('uploads/spots'),
            $imageName
        );
    }

    $coords = \App\Support\PlaceMap::locate(
        $request->latitude,
        $request->longitude,
        $request->map_link,
        (string) $request->name,
        (string) $request->location
    );

    $spot->update([

        'name' => $request->name,

        'location' => $request->location,

        'description' => $request->description,

        'price' => $request->price,

        'daily_booking_limit' => $request->filled('daily_booking_limit') ? max(1, $request->integer('daily_booking_limit')) : null,

        'map_link' => $request->map_link,

        'latitude' => $coords['latitude'],

        'longitude' => $coords['longitude'],

        'booking_type' => $request->booking_type,

        'available_cottages'
            => $request->available_cottages,

        'cottage_price'
            => $request->cottage_price,

        'image' => $imageName

    ]);

    return redirect('/owner/spots')
        ->with(
            'success',
            'Spot updated successfully!'
        );
}

public function delete($id)
{
    $spot = Spot::findOrFail($id);

    $spot->delete();

    return redirect('/owner/spots')
        ->with(
            'success',
            'Spot deleted successfully!'
        );
}

public function adminShow($id)
{
    $spot = Spot::with('ratings.user')->findOrFail($id);

    return view(
        'admin.spot-details',
        compact('spot')
    );
}

public function approve($id)
{
    $spot = Spot::findOrFail($id);

    $spot->update([
        'status' => 'approved'
    ]);

    BookingNotice::send(
        (int) $spot->user_id,
        'Your tourist spot "'.$spot->name.'" was approved.',
        ['spot_id' => $spot->id],
        'listing'
    );

    return back()->with(
        'success',
        'Spot approved successfully.'
    );
}

public function reject($id)
{
    $spot = Spot::findOrFail($id);

    $spot->update([
        'status' => 'rejected'
    ]);

    BookingNotice::send(
        (int) $spot->user_id,
        'Your tourist spot "'.$spot->name.'" was rejected.',
        ['spot_id' => $spot->id],
        'listing'
    );

    return back()->with(
        'success',
        'Spot rejected successfully.'
    );
}

public function adminDelete($id)
{
    $spot = Spot::findOrFail($id);

    $spot->delete();

    return back()->with(
        'success',
        'Spot deleted successfully.'
    );
}

public function restore($id)
{
    $spot = Spot::onlyTrashed()->findOrFail($id);
    abort_if($spot->deleted_at->lt(now()->subDays(30)), 410, 'This spot can no longer be restored.');
    $spot->restore();
    return redirect('/admin/spots')->with('success', 'Spot restored successfully.');
}


}
