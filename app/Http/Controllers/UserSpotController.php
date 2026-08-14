<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

use App\Models\Spot;
use App\Models\Hotel;
use App\Models\Food;

use App\Models\Booking;
use App\Models\HotelBooking;
use App\Models\FoodBooking;

use App\Models\Favorite;
use App\Models\Bookmark;
use App\Models\Checklist;

use App\Models\HotelFavorite;
use App\Models\HotelBookmark;
use App\Models\HotelChecklist;

use App\Models\FoodFavorite;
use App\Models\FoodBookmark;
use App\Models\FoodChecklist;

use App\Models\Notification;

use App\Models\SpotComment;
use App\Models\HotelComment;
use App\Models\FoodComment;

class UserSpotController extends Controller
{

    public function dashboard()
{
    $spots = Spot::with('ratings')
        ->latest()
        ->take(6)
        ->get();

    $hotels = Hotel::with('ratings')
        ->latest()
        ->take(6)
        ->get();

    $foods = Food::with('ratings')
        ->latest()
        ->take(6)
        ->get();

    $bookings =
        Booking::where('user_id', Auth::id())->count()
        + HotelBooking::where('user_id', Auth::id())->count()
        + FoodBooking::where('user_id', Auth::id())->count();

    $favorites =
        Favorite::where('user_id', Auth::id())->count()
        + HotelFavorite::where('user_id', Auth::id())->count()
        + FoodFavorite::where('user_id', Auth::id())->count();

    $bookmarks =
        Bookmark::where('user_id', Auth::id())->count()
        + HotelBookmark::where('user_id', Auth::id())->count()
        + FoodBookmark::where('user_id', Auth::id())->count();

    $checklists =
        Checklist::where('user_id', Auth::id())->count()
        + HotelChecklist::where('user_id', Auth::id())->count()
        + FoodChecklist::where('user_id', Auth::id())->count();

    $reviews =
        SpotComment::where('user_id', Auth::id())->count()
        + HotelComment::where('user_id', Auth::id())->count()
        + FoodComment::where('user_id', Auth::id())->count();

    $favoritePreview = Favorite::with('spot')
        ->where('user_id', Auth::id())
        ->latest()
        ->take(3)
        ->get();

    $bookmarkPreview = Bookmark::with('spot')
        ->where('user_id', Auth::id())
        ->latest()
        ->take(3)
        ->get();

    $checklistPreview = Checklist::with('spot')
        ->where('user_id', Auth::id())
        ->latest()
        ->take(3)
        ->get();

    $recentReviews = SpotComment::with('user')
        ->latest()
        ->take(5)
        ->get();

    $notifications = Notification::where(
            'user_id',
            Auth::id()
        )
        ->latest()
        ->take(5)
        ->get();

    $nextBooking = Booking::with('spot')
        ->where('user_id', Auth::id())
        ->where('status', 'approved')
        ->orderBy('booking_date')
        ->first();

    $recentNotifications = Notification::where(
    'user_id',
    Auth::id()
)
->latest()
->take(3)
->get();    

    return view(
        'user',
        compact(
            'spots',
            'hotels',
            'foods',
            'bookings',
            'favorites',
            'bookmarks',
            'checklists',
            'reviews',
            'favoritePreview',
            'bookmarkPreview',
            'checklistPreview',
            'recentReviews',
            'notifications',
            'nextBooking',
            'recentNotifications'
        )
    );
}

    public function search(Request $request)
{
    $keyword = $request->keyword;

    $spots = Spot::where('name','like',"%{$keyword}%")
        ->orWhere('location','like',"%{$keyword}%")
        ->get();

    $hotels = Hotel::where('name','like',"%{$keyword}%")
        ->orWhere('location','like',"%{$keyword}%")
        ->get();

    $foods = Food::where('name','like',"%{$keyword}%")
        ->orWhere('location','like',"%{$keyword}%")
        ->get();

    return view(
        'user.search',
        compact(
            'spots',
            'hotels',
            'foods',
            'keyword'
        )
    );
}

    public function favorite($spotId)
    {
        $favorite = Favorite::where(
            'user_id',
            Auth::id()
        )->where(
            'spot_id',
            $spotId
        )->first();

        if($favorite)
        {
            $favorite->delete();

            return back()->with(
                'success',
                '💔 Removed from favorites.'
            );
        }

        Favorite::create([
            'user_id' => Auth::id(),
            'spot_id' => $spotId
        ]);

        return back()->with(
            'success',
            '❤️ Added to favorites.'
        );
    }

    public function bookmark($spotId)
    {
        $bookmark = Bookmark::where(
            'user_id',
            Auth::id()
        )->where(
            'spot_id',
            $spotId
        )->first();

        if($bookmark)
        {
            $bookmark->delete();

            return back()->with(
                'success',
                'Removed from bookmarks.'
            );
        }

        Bookmark::create([
            'user_id' => Auth::id(),
            'spot_id' => $spotId
        ]);

        return back()->with(
            'success',
            '🔖 Added to bookmarks.'
        );
    }

    public function checklist($spotId)
    {
        $checklist = Checklist::where(
            'user_id',
            Auth::id()
        )->where(
            'spot_id',
            $spotId
        )->first();

        if($checklist)
        {
            $checklist->delete();

            return back()->with(
                'success',
                'Removed from checklist.'
            );
        }

        Checklist::create([
            'user_id' => Auth::id(),
            'spot_id' => $spotId
        ]);

        return back()->with(
            'success',
            '✅ Added to checklist.'
        );
    }

    public function favoritesPage()
{
    $favorites = Favorite::with('spot')
        ->where('user_id', Auth::id())
        ->get();

    $hotelFavorites = HotelFavorite::with('hotel')
        ->where('user_id', Auth::id())
        ->get();

    $foodFavorites = FoodFavorite::with('food')
        ->where('user_id', Auth::id())
        ->get();

    return view(
        'user.favorites',
        compact(
            'favorites',
            'hotelFavorites',
            'foodFavorites'
        )
    );
}

    public function bookmarksPage()
{
    $bookmarks = Bookmark::with('spot')
        ->where('user_id', Auth::id())
        ->get();

    $hotelBookmarks = HotelBookmark::with('hotel')
        ->where('user_id', Auth::id())
        ->get();

    $foodBookmarks = FoodBookmark::with('food')
        ->where('user_id', Auth::id())
        ->get();

    return view(
        'user.bookmarks',
        compact(
            'bookmarks',
            'hotelBookmarks',
            'foodBookmarks'
        )
    );
}

    public function checklistsPage()
{
    $checklists = Checklist::with('spot')
        ->where('user_id', Auth::id())
        ->get();

    $hotelChecklists = HotelChecklist::with('hotel')
        ->where('user_id', Auth::id())
        ->get();

    $foodChecklists = FoodChecklist::with('food')
        ->where('user_id', Auth::id())
        ->get();

    return view(
        'user.checklists',
        compact(
            'checklists',
            'hotelChecklists',
            'foodChecklists'
        )
    );
}

    public function notifications()
    {
        $notifications = Notification::where(
            'user_id',
            Auth::id()
        )
        ->latest()
        ->get();

        Notification::where(
            'user_id',
            Auth::id()
        )
        ->update([
            'is_read' => 1
        ]);

        return view(
            'user.notifications',
            compact('notifications')
        );
    }

    public function userBookings()
{
    $bookings = Booking::with('spot')
        ->where('user_id', Auth::id())
        ->get();

    $hotelBookings = HotelBooking::with('hotel')
        ->where('user_id', Auth::id())
        ->get();

    $foodBookings = FoodBooking::with('food')
        ->where('user_id', Auth::id())
        ->get();

    return view(
        'user.bookings',
        compact(
            'bookings',
            'hotelBookings',
            'foodBookings'
        )
    );
}

public function foodFavorite($foodId)
{
    $favorite = FoodFavorite::where(
        'user_id',
        Auth::id()
    )->where(
        'food_id',
        $foodId
    )->first();

    if($favorite)
    {
        $favorite->delete();

        return back()->with(
            'success',
            '💔 Removed from favorites.'
        );
    }

    FoodFavorite::create([

        'user_id' => Auth::id(),

        'food_id' => $foodId

    ]);

    return back()->with(
        'success',
        '❤️ Added to favorites.'
    );
}

public function foodBookmark($foodId)
{
    $bookmark = FoodBookmark::where(
        'user_id',
        Auth::id()
    )->where(
        'food_id',
        $foodId
    )->first();

    if($bookmark)
    {
        $bookmark->delete();

        return back()->with(
            'success',
            '🔖 Removed from bookmarks.'
        );
    }

    FoodBookmark::create([

        'user_id' => Auth::id(),

        'food_id' => $foodId

    ]);

    return back()->with(
        'success',
        '🔖 Added to bookmarks.'
    );
}

public function foodChecklist($foodId)
{
    $checklist = FoodChecklist::where(
        'user_id',
        Auth::id()
    )->where(
        'food_id',
        $foodId
    )->first();

    if($checklist)
    {
        $checklist->delete();

        return back()->with(
            'success',
            '✅ Removed from checklist.'
        );
    }

    FoodChecklist::create([

        'user_id' => Auth::id(),

        'food_id' => $foodId

    ]);

    return back()->with(
        'success',
        '✅ Added to checklist.'
    );
}

public function hotelFavorite($hotelId)
{
    $favorite = HotelFavorite::where(
        'user_id',
        Auth::id()
    )->where(
        'hotel_id',
        $hotelId
    )->first();

    if($favorite)
    {
        $favorite->delete();

        return back()->with(
            'success',
            '💔 Removed from favorites.'
        );
    }

    HotelFavorite::create([

        'user_id' => Auth::id(),

        'hotel_id' => $hotelId

    ]);

    return back()->with(
        'success',
        '❤️ Added to favorites.'
    );
}

public function hotelBookmark($hotelId)
{
    $bookmark = HotelBookmark::where(
        'user_id',
        Auth::id()
    )->where(
        'hotel_id',
        $hotelId
    )->first();

    if($bookmark)
    {
        $bookmark->delete();

        return back()->with(
            'success',
            '🔖 Removed from bookmarks.'
        );
    }

    HotelBookmark::create([

        'user_id' => Auth::id(),

        'hotel_id' => $hotelId

    ]);

    return back()->with(
        'success',
        '🔖 Added to bookmarks.'
    );
}

public function hotelChecklist($hotelId)
{
    $checklist = HotelChecklist::where(
        'user_id',
        Auth::id()
    )->where(
        'hotel_id',
        $hotelId
    )->first();

    if($checklist)
    {
        $checklist->delete();

        return back()->with(
            'success',
            '✅ Removed from checklist.'
        );
    }

    HotelChecklist::create([

        'user_id' => Auth::id(),

        'hotel_id' => $hotelId

    ]);

    return back()->with(
        'success',
        '✅ Added to checklist.'
    );
}

}
