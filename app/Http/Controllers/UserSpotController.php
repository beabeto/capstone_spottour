<?php

namespace App\Http\Controllers;

use App\Models\Favorite;
use App\Models\Bookmark;
use App\Models\Checklist;
use App\Models\Notification;
use App\Models\HotelFavorite;
use App\Models\HotelBookmark;
use App\Models\HotelChecklist;
use Illuminate\Support\Facades\Auth;

class UserSpotController extends Controller
{
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

        return view(
            'user.favorites',
            compact(
                'favorites',
                'hotelFavorites'
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

        return view(
            'user.bookmarks',
            compact(
                'bookmarks',
                'hotelBookmarks'
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

        return view(
            'user.checklists',
            compact(
                'checklists',
                'hotelChecklists'
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
}
