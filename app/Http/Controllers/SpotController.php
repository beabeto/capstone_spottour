<?php

namespace App\Http\Controllers;
use App\Models\SpotRating;
use App\Models\SpotComment;


use App\Models\Spot;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class SpotController extends Controller
{
    // USER VIEW

    public function userIndex()
    {
        $spots = Spot::where(
            'status',
            'approved'
        )->get();

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
        $spots = Spot::all();

        return view(
            'admin.spots',
            compact('spots')
        );
    }

    // SHOW ADD SPOT PAGE

    public function create()
    {
        return view('owner.create-spot');
    }

    // SAVE SPOT

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required',
            'location' => 'required',
            'description' => 'required',
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

        Spot::create([

            'user_id' => Auth::id(),

            'name' => $request->name,

            'location' => $request->location,

            

            'description' => $request->description,

            'price' => $request->price,

            'image' => $imageName,

            'status' => 'pending',

            'booking_type' =>
    $request->booking_type,

'available_cottages' =>
    $request->available_cottages,

'cottage_price' =>
    $request->cottage_price,

    'map_link' => $request->map_link,

        ]);

        return redirect('/owner/spots')
            ->with(
                'success',
                'Spot added successfully!'
            );
    }

public function show($id)
{
    $spot = Spot::with([

        'ratings',

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
    SpotComment::create([

        'spot_id' =>
        $request->spot_id,

        'user_id' =>
        Auth::id(),

        'comment' =>
        $request->comment

    ]);

    return back()->with(
    'success',
    '💬 Comment posted.'
);
}

public function ownerShow($id)
{
    $spot = Spot::with([

        'ratings',

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

    $spot->update([

        'name' => $request->name,

        'location' => $request->location,

        'description' => $request->description,

        'price' => $request->price,

        'map_link' => $request->map_link,

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
    $spot = Spot::findOrFail($id);

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


}