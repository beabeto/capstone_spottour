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
    if(Auth::attempt([
        'email' => $request->email,
        'password' => $request->password
    ]))
    {
        $request->session()->regenerate();

        $user = Auth::user();

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

    return back()->with('error','Invalid Credentials');
}

    public function logout()
    {
        Auth::logout();

        return redirect('/');
    }

    public function profile()
{
    $bookings = Booking::where(
        'user_id',
        Auth::id()
    )->count();

    $favorites = Favorite::where(
        'user_id',
        Auth::id()
    )->count();

    $bookmarks = Bookmark::where(
        'user_id',
        Auth::id()
    )->count();

    $reviews = SpotComment::where(
        'user_id',
        Auth::id()
    )->count();

    return view(
        'user.profile',
        compact(
            'bookings',
            'favorites',
            'bookmarks',
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
        $image = time().'.'.$request
            ->profile_image
            ->extension();

        $request
            ->profile_image
            ->move(
                public_path('uploads/profile'),
                $image
            );

        $user->profile_image = $image;
    }

    $user->name = $request->name;
    $user->email = $request->email;

    if($request->password)
    {
        $user->password =
            Hash::make($request->password);
    }

    $user->save();

    return back()
        ->with('success','Profile Updated');
}


}