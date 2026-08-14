<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Booking;
use App\Models\Favorite;
use App\Models\Bookmark;
use App\Models\HotelFavorite;
use App\Models\HotelBookmark;
use App\Models\HotelComment;
use App\Models\SpotComment;
use App\Models\User;
use App\Models\HotelBooking;
use App\Models\FoodBooking;
use App\Models\FoodFavorite;
use App\Models\FoodBookmark;
use App\Models\FoodComment;
use App\Models\Checklist;
use App\Models\HotelChecklist;
use App\Models\FoodChecklist;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    public function index()
    {
        return view('auth');
    }

    public function register(Request $request)
{
    $request->validate([
        'name' => 'required',
        'email' => 'required|email|unique:users',
        'password' => 'required|min:8|confirmed',
        'role' => 'required'
    ]);

    User::create([
        'name' => $request->name,
        'email' => $request->email,
        'password' => Hash::make($request->password),
        'role' => $request->role,
        'business_type' => $request->business_type
    ]);

    return redirect('/');
}

    public function login(Request $request)
{
    if (Auth::attempt([
        'email' => $request->email,
        'password' => $request->password
    ]))
    {
        $request->session()->regenerate();

        $user = Auth::user();

        $user->update([
            'is_online' => true
        ]);

        if($user->role == 'admin')
        {
            return redirect('/admin');
        }

        if($user->role == 'owner')
        {
            return redirect('/owner');
        }

        return redirect('/user');
    }

    return back()->with(
        'error',
        'Invalid Credentials'
    );
}

    public function logout()
{
    if (Auth::check()) {

        Auth::user()->update([
            'is_online' => false
        ]);

    }

    Auth::logout();

    request()->session()->invalidate();

    request()->session()->regenerateToken();

    return redirect('/');
}   

    public function profile()
{
    $userId = Auth::id();

    $bookings =
        Booking::where('user_id', $userId)->count() +
        HotelBooking::where('user_id', $userId)->count() +
        FoodBooking::where('user_id', $userId)->count();

    $favorites =
        Favorite::where('user_id', $userId)->count() +
        HotelFavorite::where('user_id', $userId)->count() +
        FoodFavorite::where('user_id', $userId)->count();

    $bookmarks =
        Bookmark::where('user_id', $userId)->count() +
        HotelBookmark::where('user_id', $userId)->count() +
        FoodBookmark::where('user_id', $userId)->count();

    $checklists =
        Checklist::where('user_id', $userId)->count() +
        HotelChecklist::where('user_id', $userId)->count() +
        FoodChecklist::where('user_id', $userId)->count();

    $reviews =
        SpotComment::where('user_id', $userId)->count() +
        HotelComment::where('user_id', $userId)->count() +
        FoodComment::where('user_id', $userId)->count();

    return view(
        'user.profile',
        compact(
            'bookings',
            'favorites',
            'bookmarks',
            'checklists',
            'reviews'
        )
    );
}

public function updateProfile(Request $request)
{
    $user = Auth::user();

    $request->validate([
        'name' => 'required',
        'email' => 'required|email',
    ]);

    if($request->hasFile('profile_image'))
    {
        $image =
            time().'.'.
            $request->profile_image->extension();

        $request->profile_image->move(
            public_path('uploads/profile'),
            $image
        );

        $user->profile_image = $image;
    }

    $user->name = $request->name;

    $user->email = $request->email;

    $user->gcash_number =
        $request->gcash_number;

    $user->gcash_name =
        $request->gcash_name;

    $user->bank_name =
        $request->bank_name;

    $user->bank_account_number =
        $request->bank_account_number;

    if($request->filled('password'))
    {
        $user->password =
            Hash::make(
                $request->password
            );
    }

    $user->save();

    return back()->with(
        'success',
        'Profile Updated Successfully'
    );
}

}