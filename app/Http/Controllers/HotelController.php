<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Hotel;
use App\Models\HotelFavorite;
use App\Models\HotelBookmark;
use App\Models\HotelChecklist;
use App\Models\HotelRating;
use App\Models\HotelComment;
use App\Models\HotelReply;
use App\Models\HotelReaction;
use App\Models\HotelCommentRating;
use App\Models\Notification;
use Illuminate\Support\Facades\Auth;

class HotelController extends Controller
{
    public function ownerIndex()
    {
        $hotels = Hotel::where(
            'owner_id',
            Auth::id()
        )->latest()->get();

        return view(
            'owner.hotels',
            compact('hotels')
        );
    }

    public function create()
    {
        return view(
            'owner.create-hotel'
        );
    }

    public function store(Request $request)
    {
        $request->validate([

            'name' => 'required',

            'location' => 'required',

            'description' => 'required',

            'price_per_night' => 'required',

            'available_rooms' => 'required'

        ]);

        $image = null;

        if($request->hasFile('image'))
        {
            $file = $request->file('image');

            $image =
                time().'_'.
                $file->getClientOriginalName();

            $file->move(
                public_path(
                    'uploads/hotels'
                ),
                $image
            );
        }

        Hotel::create([

            'owner_id' => Auth::id(),

            'name' => $request->name,

            'location' => $request->location,

            'description' => $request->description,

            'price_per_night' =>
                $request->price_per_night,

            'available_rooms' =>
                $request->available_rooms,

            'amenities' =>
                $request->amenities,

            'map_link' =>
                $request->map_link,

            'image' => $image

        ]);

        return redirect(
            '/owner/hotels'
        )->with(
            'success',
            'Hotel added successfully.'
        );
    }

    public function ownerShow($id)
    {
        $hotel = Hotel::findOrFail($id);

        return view(
            'owner.hotel-details',
            compact('hotel')
        );
    }

    public function adminIndex()
    {
        $hotels = Hotel::latest()->get();

        return view(
            'admin.hotels',
            compact('hotels')
        );
    }

    public function adminShow($id)
    {
        $hotel = Hotel::findOrFail($id);

        return view(
            'admin.hotel-details',
            compact('hotel')
        );
    }

    public function userIndex()
    {
        $hotels = Hotel::where(
            'status',
            'approved'
        )->latest()->get();

        return view(
            'user.hotels',
            compact('hotels')
        );
    }

    public function show($id)
{
    $hotel = Hotel::findOrFail($id);

    return view(
        'user.hotel-details',
        compact('hotel')
    );
}

    public function edit($id)
{
    $hotel = Hotel::findOrFail($id);

    return view(
        'owner.edit-hotel',
        compact('hotel')
    );
}

public function update(
    Request $request,
    $id
)
{
    $hotel = Hotel::findOrFail($id);

    $image = $hotel->image;

    if($request->hasFile('image'))
    {
        $file = $request->file('image');

        $image =
            time().'_'.
            $file->getClientOriginalName();

        $file->move(
            public_path(
                'uploads/hotels'
            ),
            $image
        );
    }

    $hotel->update([

        'name' =>
        $request->name,

        'location' =>
        $request->location,

        'description' =>
        $request->description,

        'price_per_night' =>
        $request->price_per_night,

        'available_rooms' =>
        $request->available_rooms,

        'amenities' =>
        $request->amenities,

        'map_link' =>
        $request->map_link,

        'image' =>
        $image

    ]);

    return redirect(
        '/owner/hotels'
    )->with(
        'success',
        'Hotel updated successfully.'
    );
}

public function delete($id)
{
    $hotel = Hotel::findOrFail($id);

    if(
        $hotel->owner_id != Auth::id()
    )
    {
        abort(403);
    }

    if(
        $hotel->image &&
        file_exists(
            public_path(
                'uploads/hotels/' .
                $hotel->image
            )
        )
    )
    {
        unlink(
            public_path(
                'uploads/hotels/' .
                $hotel->image
            )
        );
    }

    $hotel->delete();

    return redirect(
        '/owner/hotels'
    )->with(
        'success',
        'Hotel deleted successfully.'
    );
}

public function approve($id)
{
    Hotel::findOrFail($id)
    ->update([

        'status' => 'approved'

    ]);

    return back()->with(
        'success',
        'Hotel approved.'
    );
}

public function reject($id)
{
    Hotel::findOrFail($id)
    ->update([

        'status' => 'rejected'

    ]);

    return back()->with(
        'success',
        'Hotel rejected.'
    );
}

public function adminDelete($id)
{
    $hotel = Hotel::findOrFail($id);

    $hotel->delete();

    return back()->with(
        'success',
        'Hotel deleted.'
    );
}

public function favorite($hotel)
{
    HotelFavorite::firstOrCreate([

        'user_id' => Auth::id(),

        'hotel_id' => $hotel

    ]);

    return back()->with(
        'success',
        'Added to favorites.'
    );
}

public function bookmark($hotel)
{
    HotelBookmark::firstOrCreate([

        'user_id' => Auth::id(),

        'hotel_id' => $hotel

    ]);

    return back()->with(
        'success',
        'Added to bookmarks.'
    );
}

public function checklist($hotel)
{
    HotelChecklist::firstOrCreate([

        'user_id' => Auth::id(),

        'hotel_id' => $hotel

    ]);

    return back()->with(
        'success',
        'Added to checklist.'
    );
}

public function rate(Request $request)
{
    HotelRating::updateOrCreate(

        [

            'user_id' => Auth::id(),

            'hotel_id' => $request->hotel_id

        ],

        [

            'rating' => $request->rating

        ]

    );

    return back()->with(
        'success',
        'Rating submitted.'
    );
}

public function comment(Request $request)
{
    $comment = HotelComment::create([

        'user_id' => Auth::id(),

        'hotel_id' => $request->hotel_id,

        'comment' => $request->comment

    ]);

    $hotel = Hotel::findOrFail(
        $request->hotel_id
    );

    if($hotel->owner_id != Auth::id())
    {
        Notification::create([

    'user_id' => $hotel->owner_id,

    'sender_id' => Auth::id(),

    'hotel_id' => $hotel->id,

    'comment_id' => $comment->id,

    'message' =>
        Auth::user()->name .
        ' commented on your hotel.'

]);
    }

    return back()->with(
        'success',
        'Comment submitted.'
    );
}

public function reply(Request $request)
{
    HotelReply::create([
        'user_id' => Auth::id(),
        'comment_id' => $request->comment_id,
        'reply' => $request->reply
    ]);

    $comment = HotelComment::findOrFail(
        $request->comment_id
    );

    Notification::create([
        'user_id' => $comment->user_id,
        'sender_id' => Auth::id(),
        'hotel_id' => $comment->hotel_id,
        'comment_id' => $comment->id,
        'message' => Auth::user()->name .
                     ' replied to your comment.'
    ]);

    return back();
}

public function rateComment(Request $request)
{
    HotelCommentRating::updateOrCreate(

        [

            'user_id' => Auth::id(),

            'comment_id' => $request->comment_id

        ],

        [

            'rating' => $request->rating

        ]

    );

    return back();
}

public function like($id)
{
    HotelReaction::updateOrCreate(

        [
            'user_id' => Auth::id(),
            'comment_id' => $id
        ],

        [
            'reaction' => 'like'
        ]

    );

    $comment = HotelComment::findOrFail($id);

    if($comment->user_id != Auth::id())
    {
        Notification::create([

            'user_id' => $comment->user_id,

            'sender_id' => Auth::id(),

            'hotel_id' => $comment->hotel_id,

            'comment_id' => $comment->id,

            'message' =>
                Auth::user()->name .
                ' liked your comment.'

        ]);
    }

    return back();
}

public function dislike($id)
{
    HotelReaction::updateOrCreate(

        [
            'user_id' => Auth::id(),
            'comment_id' => $id
        ],

        [
            'reaction' => 'dislike'
        ]

    );

    $comment = HotelComment::findOrFail($id);

    if($comment->user_id != Auth::id())
    {
        Notification::create([

            'user_id' => $comment->user_id,

            'sender_id' => Auth::id(),

            'hotel_id' => $comment->hotel_id,

            'comment_id' => $comment->id,

            'message' =>
                Auth::user()->name .
                ' disliked your comment.'

        ]);
    }

    return back();
}

public function deleteComment($id)
{
    $comment = HotelComment::findOrFail($id);

    if(
        $comment->user_id ==
        Auth::id()
    )
    {
        $comment->delete();
    }

    return back();
}

public function editComment($id)
{
    $comment = HotelComment::findOrFail($id);

    if($comment->user_id != Auth::id())
    {
        abort(403);
    }

    return view(
        'user.edit-hotel-comment',
        compact('comment')
    );
}

public function updateComment(
    Request $request,
    $id
)
{
    $comment = HotelComment::findOrFail($id);

    if($comment->user_id != Auth::id())
    {
        abort(403);
    }

    $comment->update([

        'comment' => $request->comment

    ]);

    return redirect()->back()
        ->with(
            'success',
            'Comment updated successfully.'
        );
}
}