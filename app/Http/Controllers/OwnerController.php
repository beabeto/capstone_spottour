<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\SpotComment;
use App\Models\HotelComment;

class OwnerController extends Controller
{
    public function reviews()
    {
        $ownerId = Auth::id();

        $spotComments = SpotComment::with(
            'user',
            'spot',
            'ratings',
            'reactions'
        )
        ->whereHas('spot', function ($query) use ($ownerId) {

            $query->where(
    'user_id',
    $ownerId
);

        })
        ->get();

        $hotelComments = HotelComment::with([
    'user',
    'hotel',
    'ratings',
    'reactions'
])
->whereHas('hotel', function ($query) use ($ownerId) {

    $query->where(
        'owner_id',
        $ownerId
    );

})
->latest()
->get();

        $comments = $spotComments
            ->concat($hotelComments)
            ->sortByDesc('created_at');

        return view(
            'owner.reviews',
            compact('comments')
        );
    }
}