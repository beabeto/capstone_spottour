<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Hotel;
use App\Models\User;
use App\Models\HotelFavorite;
use App\Models\HotelBookmark;
use App\Models\HotelChecklist;
use App\Models\HotelRating;
use App\Models\HotelComment;
use App\Models\HotelReply;
use App\Models\HotelReaction;
use App\Models\HotelCommentRating;
use App\Models\HotelReplyRating;
use App\Models\HotelRoom;
use App\Models\Notification;
use App\Support\BookingNotice;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

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
        abort_if(request()->is('admin/*') && Auth::user()->role !== 'admin', 403);
        $owners = Auth::user()->role === 'admin'
            ? User::where('role', 'owner')->orderBy('name')->get()
            : collect();

        return view('owner.create-hotel', compact('owners'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required',
            'location' => 'required',
            'description' => 'required',
            'daily_booking_limit' => 'nullable|integer|min:1',
            'owner_id' => Auth::user()->role === 'admin' ? 'required|exists:users,id,role,owner' : 'nullable',
            'rooms' => 'required|array|min:1',
            'rooms.*.type' => ['required', Rule::in(array_keys(HotelRoom::types()))],
            'rooms.*.price' => 'required|numeric|min:0',
            'rooms.*.available' => 'required|integer|min:1',
            'rooms.*.cover' => 'nullable|image|max:5120',
            'rooms.*.gallery.*' => 'nullable|image|max:5120',
        ]);

        $isAdmin = Auth::user()->role === 'admin';

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

        $coords = \App\Support\PlaceMap::locate(
            null,
            null,
            $request->map_link,
            (string) $request->name,
            (string) $request->location
        );

        $hotel = Hotel::create([

            'owner_id' => $isAdmin ? $request->integer('owner_id') : Auth::id(),

            'status' => $isAdmin ? 'approved' : 'pending',

            'name' => $request->name,

            'location' => $request->location,

            'description' => $request->description,

            'price_per_night' => collect($request->input('rooms'))->min(fn ($room) => (float) $room['price']),

            'available_rooms' => collect($request->input('rooms'))->sum(fn ($room) => (int) $room['available']),

            'daily_booking_limit' => $request->filled('daily_booking_limit') ? $request->integer('daily_booking_limit') : null,

            'amenities' =>
                $request->amenities,

            'map_link' =>
                $request->map_link,

            'latitude' => $coords['latitude'],

            'longitude' => $coords['longitude'],

            'image' => $image

        ]);

        foreach ($request->input('rooms') as $index => $room) {
            $this->storeRoomType($hotel, $request, (string) $index, $room);
        }

        if (!$isAdmin) {
            BookingNotice::admins(
                Auth::user()->name.' submitted the hotel "'.$hotel->name.'" for review.',
                ['hotel_id' => $hotel->id],
                [],
                'listing'
            );
        }

        return redirect($isAdmin ? '/admin/hotels' : '/owner/hotels')->with(
            'success',
            'Hotel saved with its room types.'
        );
    }

    public function ownerShow($id)
    {
        $hotel = Hotel::with(['rooms', 'ratings.user', 'comments.user', 'comments.ratings', 'comments.reactions', 'comments.replies.user'])->findOrFail($id);

        return view(
            'owner.hotel-details',
            compact('hotel')
        );
    }

    public function adminIndex()
    {
        $hotels = Hotel::with('owner')->latest()->get();
        $deletedHotels = Hotel::onlyTrashed()->latest('deleted_at')->get();

        return view(
            'admin.hotels',
            compact('hotels', 'deletedHotels')
        );
    }

    public function adminShow($id)
    {
        $hotel = Hotel::with(['ratings.user', 'comments.user', 'comments.replies.user'])->findOrFail($id);

        return view(
            'admin.hotel-details',
            compact('hotel')
        );
    }

    public function userIndex()
    {
        $hotels = Hotel::where('status', 'approved')
            ->withAvg('ratings', 'rating')
            ->latest()
            ->get();

        return view(
            'user.hotels',
            compact('hotels')
        );
    }

    public function show($id)
{
    $hotel = Hotel::with(['rooms', 'ratings.user'])->findOrFail($id);

    return view(
        'user.hotel-details',
        compact('hotel')
    );
}

public function rooms($id)
{
    $hotel = Hotel::with('rooms')->findOrFail($id);
    abort_unless($hotel->owner_id === Auth::id() || Auth::user()->role === 'admin', 403);
    return view('owner.hotel-rooms', compact('hotel'));
}

public function storeRoom(Request $request, $id)
{
    $hotel = Hotel::findOrFail($id);
    abort_unless($hotel->owner_id === Auth::id() || Auth::user()->role === 'admin', 403);
    $request->validate([
        'name' => ['required', Rule::in(array_keys(HotelRoom::types()))],
        'price_per_night' => 'required|numeric|min:0',
        'available_rooms' => 'required|integer|min:1',
        'cover_image' => 'nullable|image|max:5120',
        'gallery_images.*' => 'nullable|image|max:5120',
    ]);
    $cover = null;
    if ($request->hasFile('cover_image')) {
        $cover = time().'_'.uniqid().'_'.$request->file('cover_image')->getClientOriginalName();
        $request->file('cover_image')->move(public_path('uploads/hotel-rooms'), $cover);
    }
    $gallery = [];
    foreach ($request->file('gallery_images', []) as $file) {
        $name = time().'_'.uniqid().'_'.$file->getClientOriginalName();
        $file->move(public_path('uploads/hotel-rooms'), $name);
        $gallery[] = $name;
    }
    HotelRoom::create(['hotel_id'=>$hotel->id, 'name'=>$request->name, 'price_per_night'=>$request->price_per_night, 'available_rooms'=>$request->available_rooms, 'cover_image'=>$cover, 'gallery_images'=>$gallery]);
    $this->refreshHotelRooms($hotel);
    return back()->with('success', 'Room type added.');
}

private function storeRoomType(Hotel $hotel, Request $request, string $index, array $room): void
{
    $cover = null;
    $coverFile = $request->file("rooms.{$index}.cover");
    if ($coverFile) {
        $cover = time().'_'.uniqid().'_'.$coverFile->getClientOriginalName();
        $coverFile->move(public_path('uploads/hotel-rooms'), $cover);
    }

    $gallery = [];
    foreach ($request->file("rooms.{$index}.gallery", []) ?: [] as $file) {
        if (! $file) {
            continue;
        }
        $name = time().'_'.uniqid().'_'.$file->getClientOriginalName();
        $file->move(public_path('uploads/hotel-rooms'), $name);
        $gallery[] = $name;
    }

    HotelRoom::create([
        'hotel_id' => $hotel->id,
        'name' => $room['type'],
        'price_per_night' => $room['price'],
        'available_rooms' => $room['available'],
        'cover_image' => $cover,
        'gallery_images' => $gallery,
    ]);
}

private function refreshHotelRooms(Hotel $hotel): void
{
    $rooms = $hotel->rooms()->get();
    $hotel->update([
        'price_per_night' => $rooms->min('price_per_night') ?? $hotel->price_per_night,
        'available_rooms' => $rooms->sum('available_rooms'),
    ]);
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

    $coords = \App\Support\PlaceMap::locate(
        null,
        null,
        $request->map_link,
        (string) $request->name,
        (string) $request->location
    );

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

        'daily_booking_limit' => $request->filled('daily_booking_limit') ? $request->integer('daily_booking_limit') : null,

        'amenities' =>
        $request->amenities,

        'map_link' =>
        $request->map_link,

        'latitude' =>
        $coords['latitude'],

        'longitude' =>
        $coords['longitude'],

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

    $hotel->delete();

    return redirect(
        '/owner/hotels'
    )->with(
        'success',
        'Hotel deleted successfully.'
    );
}

public function restore($id)
{
    $hotel = Hotel::onlyTrashed()->findOrFail($id);
    abort_if($hotel->deleted_at->lt(now()->subDays(30)), 410, 'This hotel can no longer be restored.');
    $hotel->restore();
    return redirect('/admin/hotels')->with('success', 'Hotel restored successfully.');
}

public function approve($id)
{
    $hotel = Hotel::findOrFail($id);
    $hotel->update([
        'status' => 'approved'
    ]);

    BookingNotice::send(
        (int) $hotel->owner_id,
        'Your hotel "'.$hotel->name.'" was approved.',
        ['hotel_id' => $hotel->id],
        'listing'
    );

    return back()->with(
        'success',
        'Hotel approved.'
    );
}

public function reject($id)
{
    $hotel = Hotel::findOrFail($id);
    $hotel->update([
        'status' => 'rejected'
    ]);

    BookingNotice::send(
        (int) $hotel->owner_id,
        'Your hotel "'.$hotel->name.'" was rejected.',
        ['hotel_id' => $hotel->id],
        'listing'
    );

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
    $request->validate(['comment' => 'required|string|max:1000', 'image' => 'nullable|image|mimes:jpeg,png,webp|max:5120']);
    $image = $request->hasFile('image') ? time().'_'.$request->file('image')->getClientOriginalName() : null;
    if ($image) $request->file('image')->move(public_path('uploads/comments'), $image);
    $comment = HotelComment::create([

        'user_id' => Auth::id(),

        'hotel_id' => $request->hotel_id,

        'comment' => $request->comment,
        'image' => $image

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
    $request->validate(['reply' => 'required|string|max:1000', 'image' => 'nullable|image|mimes:jpeg,png,webp|max:5120']);
    $image = $request->hasFile('image') ? time().'_'.$request->file('image')->getClientOriginalName() : null;
    if ($image) $request->file('image')->move(public_path('uploads/comments'), $image);
    HotelReply::create([
        'user_id' => Auth::id(),
        'comment_id' => $request->comment_id,
        'reply' => $request->reply,
        'image' => $image
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

public function rateReply(Request $request)
{
    $request->validate(['reply_id' => 'required|exists:hotel_replies,id', 'rating' => 'required|integer|between:1,5']);
    HotelReplyRating::updateOrCreate(['reply_id' => $request->reply_id, 'user_id' => Auth::id()], ['rating' => $request->rating]);
    return back()->with('success', 'Reply rating saved.');
}
}
