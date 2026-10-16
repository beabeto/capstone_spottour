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
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;

class AuthController extends Controller
{
    public function index()
    {
        return view('auth');
    }

    public function register(Request $request)
{
    $request->validate([
        'name' => 'required|string|max:100',
        'email' => 'required|email|unique:users',
        'gender' => 'required|in:male,female,prefer_not_to_say',
        'birth_date' => 'required|date|before:today',
        'password' => 'required|min:8|confirmed',
        'role' => 'required|in:user,owner',
        'business_type' => 'nullable|required_if:role,owner',
    ]);

    User::create([
        'name' => $request->name,
        'email' => $request->email,
        'gender' => $request->gender,
        'birth_date' => $request->birth_date,
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

    public function showForgotPassword()
    {
        return view('auth.password-reset');
    }

    public function sendPasswordResetCode(Request $request)
    {
        $request->validate(['email' => ['required', 'email']]);
        $email = Str::lower($request->email);
        $user = User::where('email', $email)->first();

        // Do not reveal whether an address has an account.
        if ($user) {
            $code = (string) random_int(100000, 999999);
            DB::table('password_reset_codes')->where('email', $email)->delete();
            DB::table('password_reset_codes')->insert([
                'email' => $email,
                'code_hash' => Hash::make($code),
                'expires_at' => now()->addMinutes(15),
                'attempts' => 0,
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            Mail::raw("Your SpotTour password reset code is {$code}. It expires in 15 minutes. Do not share this code.", function ($message) use ($email) {
                $message->to($email)->subject('Your SpotTour password reset code');
            });
        }

        return redirect('/password/reset?email='.urlencode($email))
            ->with('success', 'If that email has an account, we sent a six-digit reset code.');
    }

    public function resetPasswordWithCode(Request $request)
    {
        $request->validate([
            'email' => ['required', 'email'],
            'code' => ['required', 'digits:6'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
        ]);

        $email = Str::lower($request->email);
        $record = DB::table('password_reset_codes')->where('email', $email)->first();

        if (!$record || now()->greaterThan($record->expires_at) || $record->attempts >= 5 || !Hash::check($request->code, $record->code_hash)) {
            if ($record) DB::table('password_reset_codes')->where('id', $record->id)->increment('attempts');
            return back()->withInput($request->only('email'))->withErrors(['code' => 'The code is invalid or has expired. Request a new code and try again.']);
        }

        User::where('email', $email)->update(['password' => Hash::make($request->password)]);
        DB::table('password_reset_codes')->where('email', $email)->delete();

        return redirect('/')->with('success', 'Password changed. You can now sign in.');
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
        'name' => 'required|string|max:100',
        'email' => 'required|email|unique:users,email,'.$user->id,
        'gender' => 'nullable|in:male,female,prefer_not_to_say',
        'birth_date' => 'nullable|date|before:today',
        'profile_image' => 'nullable|image|mimes:jpeg,png,webp|max:5120',
        'gcash_number' => ['nullable', 'regex:/^09[0-9]{9}$/'],
        'gcash_name' => 'nullable|string|max:100',
        'gcash_qr' => 'nullable|image|mimes:jpeg,png,webp|max:5120',
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

    if($request->hasFile('gcash_qr'))
    {
        $qr = time().'_gcash_qr.'.$request->gcash_qr->extension();
        $request->gcash_qr->move(public_path('uploads/gcash-qr'), $qr);
        $user->gcash_qr = $qr;
    }

    $user->name = $request->name;

    $user->email = $request->email;
    $user->gender = $request->gender;
    $user->birth_date = $request->birth_date;

    $user->gcash_number =
        $request->gcash_number;

    $user->gcash_name =
        $request->gcash_name;

    $user->bank_name =
        $request->bank_name;

    $user->bank_account_number =
        $request->bank_account_number;

    $user->save();

    return back()->with(
        'success',
        'Profile Updated Successfully'
    );
}

}
