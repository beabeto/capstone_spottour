<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Spot;
use App\Models\Hotel;
use App\Models\Food;
use App\Models\Booking;
use App\Models\HotelBooking;
use App\Models\FoodBooking;
use App\Models\Feedback;


class AdminController extends Controller
{
    public function dashboard()
    {
        /*
        |--------------------------------------------------------------------------
        | DASHBOARD COUNTS
        |--------------------------------------------------------------------------
        */

        $totalUsers = User::where(
            'role',
            'user'
        )->count();

        $totalOwners = User::where(
            'role',
            'owner'
        )->count();

        $totalSpots = Spot::count();

        $totalHotels = Hotel::count();

        $totalFoods = Food::count();

        $totalBookings =
            Booking::count() +
            HotelBooking::count() +
            FoodBooking::count();

        $newFeedback = Feedback::count();

        /*
|--------------------------------------------------------------------------
| PENDING APPROVALS
|--------------------------------------------------------------------------
*/

$pendingSpots = Spot::where(
    'status',
    'pending'
)->count();

$pendingHotels = Hotel::where(
    'status',
    'pending'
)->count();

$pendingFoods = Food::where(
    'status',
    'pending'
)->count();

$pendingApprovals =
    $pendingSpots +
    $pendingHotels +
    $pendingFoods;


/*
|--------------------------------------------------------------------------
| MONTHLY REGISTRATIONS
|--------------------------------------------------------------------------
*/

$registrationLabels = [
    'Jan',
    'Feb',
    'Mar',
    'Apr',
    'May',
    'Jun',
    'Jul',
    'Aug',
    'Sep',
    'Oct',
    'Nov',
    'Dec'
];

$userRegistrationData = [];

$ownerRegistrationData = [];

for($month = 1; $month <= 12; $month++)
{
    $userRegistrationData[] = User::where(
        'role',
        'user'
    )
    ->whereMonth(
        'created_at',
        $month
    )
    ->count();

    $ownerRegistrationData[] = User::where(
        'role',
        'owner'
    )
    ->whereMonth(
        'created_at',
        $month
    )
    ->count();
}

/*
|--------------------------------------------------------------------------
| BOOKING ANALYTICS
|--------------------------------------------------------------------------
*/

$bookingLabels = [

    'Spot',

    'Hotel',

    'Restaurant'

];

$bookingData = [

    Booking::count(),

    HotelBooking::count(),

    FoodBooking::count()

];

/*
|--------------------------------------------------------------------------
| REVENUE
|--------------------------------------------------------------------------
*/

$todayRevenue =
    Booking::whereDate('created_at', today())
        ->where('status', 'approved')
        ->sum('total_price')

    +

    HotelBooking::whereDate('created_at', today())
        ->where('status', 'approved')
        ->sum('total_price');

$monthlyRevenue =
    Booking::whereMonth('created_at', now()->month)
        ->whereYear('created_at', now()->year)
        ->where('status', 'approved')
        ->sum('total_price')

    +

    HotelBooking::whereMonth('created_at', now()->month)
        ->whereYear('created_at', now()->year)
        ->where('status', 'approved')
        ->sum('total_price');

$totalRevenue =
    Booking::where('status','approved')
        ->sum('total_price')

    +

    HotelBooking::where('status','approved')
        ->sum('total_price');

/*
|--------------------------------------------------------------------------
| TOP TOURIST SPOT
|--------------------------------------------------------------------------
*/

$topSpot = Spot::withCount('bookings')
    ->orderByDesc('bookings_count')
    ->first();
    
/*
|--------------------------------------------------------------------------
| TOP HOTEL
|--------------------------------------------------------------------------
*/

$topHotel = Hotel::withCount('bookings')
    ->orderByDesc('bookings_count')
    ->first();
    
/*
|--------------------------------------------------------------------------
| TOP RESTAURANT
|--------------------------------------------------------------------------
*/

$topFood = Food::withCount('bookings')
    ->orderByDesc('bookings_count')
    ->first();    

    /*
|--------------------------------------------------------------------------
| MONTHLY REVENUE GRAPH
|--------------------------------------------------------------------------
*/

$revenueLabels = [
    'Jan',
    'Feb',
    'Mar',
    'Apr',
    'May',
    'Jun',
    'Jul',
    'Aug',
    'Sep',
    'Oct',
    'Nov',
    'Dec'
];

$revenueData = [];

for($month = 1; $month <= 12; $month++)
{
    $spotRevenue = Booking::where('status','approved')
        ->whereMonth('created_at',$month)
        ->whereYear('created_at',now()->year)
        ->sum('total_price');

    $hotelRevenue = HotelBooking::where('status','approved')
        ->whereMonth('created_at',$month)
        ->whereYear('created_at',now()->year)
        ->sum('total_price');

    $revenueData[] = $spotRevenue + $hotelRevenue;
}    

/*
|--------------------------------------------------------------------------
| RECENT ACTIVITIES
|--------------------------------------------------------------------------
*/

$recentUsers = User::latest()
    ->take(5)
    ->get();

$recentSpots = Spot::latest()
    ->take(5)
    ->get();

$recentHotels = Hotel::latest()
    ->take(5)
    ->get();

$recentFoods = Food::latest()
    ->take(5)
    ->get();

$recentBookings = Booking::with('user','spot')
    ->latest()
    ->take(5)
    ->get();

$recentHotelBookings = HotelBooking::with('user','hotel')
    ->latest()
    ->take(5)
    ->get();

$recentFoodBookings = FoodBooking::with('user','food')
    ->latest()
    ->take(5)
    ->get();

    $activities = collect();



   foreach($recentUsers as $user){

    $activities->push([

        'icon'=>'👤',

        'title'=>$user->name,

        'message'=>'Registered an account',

        'profile_image'=>$user->profile_image,

        'date'=>$user->created_at

    ]);

}



foreach($recentSpots as $spot){

    $activities->push([

        'icon'=>'📍',

        'title'=>$spot->name,

        'message'=>'New tourist spot submitted',

        'date'=>$spot->created_at

    ]);

}

foreach($recentHotels as $hotel){

    $activities->push([

        'icon'=>'🏨',

        'title'=>$hotel->name,

        'message'=>'New hotel added',

        'profile_image'=>null,

        'date'=>$hotel->created_at

    ]);

}

foreach($recentFoods as $food){

    $activities->push([

        'icon'=>'🍽',

        'title'=>$food->name,

        'message'=>'New restaurant added',

        'profile_image'=>null,

        'date'=>$food->created_at

    ]);

}

foreach($recentHotelBookings as $booking){

    $activities->push([

        'icon'=>'🏨',

        'title'=>optional($booking->user)->name,

        'message'=>'Booked '.optional($booking->hotel)->name,

        'profile_image'=>optional($booking->user)->profile_image,

        'date'=>$booking->created_at

    ]);

}

foreach($recentFoodBookings as $booking){

    $activities->push([

        'icon' => '🍽',

        'title' => optional($booking->user)->name ?? 'Unknown User',

        'message' => 'Reserved '.(optional($booking->food)->name ?? 'Unknown Restaurant'),

        'profile_image' => optional($booking->user)->profile_image,

        'date' => $booking->created_at

    ]);

}


    foreach($recentBookings as $booking){

    $activities->push([

        'icon'=>'📖',

        'title'=>optional($booking->user)->name,

        'message'=>'Booked '.optional($booking->spot)->name,

        'profile_image'=>optional($booking->user)->profile_image,

        'date'=>$booking->created_at

    ]);

}



$activities = $activities
    ->sortByDesc('date')
    ->take(3);


/*
|--------------------------------------------------------------------------
| ADMIN NOTIFICATIONS
|--------------------------------------------------------------------------
*/

$newUsers = User::whereDate(
    'created_at',
    today()
)->count();

$newSpots = Spot::where(
    'status',
    'pending'
)->count();

$newHotels = Hotel::where(
    'status',
    'pending'
)->count();

$newFoods = Food::where(
    'status',
    'pending'
)->count();

$newBookings =
    Booking::where(
        'status',
        'pending'
    )->count()

    +

    HotelBooking::where(
        'status',
        'pending'
    )->count()

    +

    FoodBooking::where(
        'status',
        'pending'
    )->count();

$totalNotifications =
    $newUsers +
    $newSpots +
    $newHotels +
    $newFoods +
    $newBookings +
    $newFeedback;    


        return view(
            'admin',
            compact(
                'totalUsers',
                'totalOwners',
                'totalSpots',
                'totalHotels',
                'totalFoods',
                'totalBookings',
                'newFeedback',
                'pendingApprovals',
                'pendingSpots',
                'pendingHotels',
                'pendingFoods',
                'registrationLabels',
                'userRegistrationData',
                'ownerRegistrationData',
                'bookingLabels',
                'bookingData',
                'todayRevenue',
                'monthlyRevenue',
                'totalRevenue',
                'activities',
                'revenueLabels',
                'revenueData',
                'topSpot',
                'topHotel',
                'topFood',
                'newUsers',
                'newSpots',
                'newHotels',
                'newFoods',
                'newBookings',
                'totalNotifications',
            )
        );
    }

    public function users()
    {
        $users = User::where(
            'role',
            'user'
        )->latest()->get();

        return view(
            'admin.users',
            compact('users')
        );
    }

    public function owners()
    {
        $owners = User::where(
            'role',
            'owner'
        )->latest()->get();

        return view(
            'admin.owners',
            compact('owners')
        );
    }

    public function activities()
{
    $recentUsers = User::latest()->take(20)->get();

    $recentSpots = Spot::latest()->take(20)->get();

    $recentHotels = Hotel::latest()->take(20)->get();

    $recentFoods = Food::latest()->take(20)->get();

    $recentBookings = Booking::with('user','spot')
        ->latest()
        ->take(20)
        ->get();

    $recentHotelBookings = HotelBooking::with('user','hotel')
        ->latest()
        ->take(20)
        ->get();

    $recentFoodBookings = FoodBooking::with('user','food')
        ->latest()
        ->take(20)
        ->get();

    $activities = collect();

    foreach($recentUsers as $user){

    $activities->push([

        'icon'=>'👤',

        'title'=>$user->name,

        'message'=>'Registered an account',

        'profile_image'=>$user->profile_image,

        'date'=>$user->created_at

    ]);

}

    foreach($recentBookings as $booking){

    $activities->push([

        'icon'=>'📖',

        'title'=>optional($booking->user)->name ?? 'Unknown User',

        'message'=>'Booked '.(optional($booking->spot)->name ?? 'Unknown Spot'),

        'profile_image'=>optional($booking->user)->profile_image,

        'date'=>$booking->created_at

    ]);

}

    foreach($recentHotelBookings as $booking){

    $activities->push([

        'icon'=>'🏨',

        'title'=>optional($booking->user)->name ?? 'Unknown User',

        'message'=>'Booked '.(optional($booking->hotel)->name ?? 'Unknown Hotel'),

        'profile_image'=>optional($booking->user)->profile_image,

        'date'=>$booking->created_at

    ]);

}

    foreach($recentFoodBookings as $booking){

    $activities->push([

        'icon'=>'🍽',

        'title'=>optional($booking->user)->name ?? 'Unknown User',

        'message'=>'Reserved '.(optional($booking->food)->name ?? 'Unknown Restaurant'),

        'profile_image'=>optional($booking->user)->profile_image,

        'date'=>$booking->created_at

    ]);

}

foreach($recentSpots as $spot){

    $activities->push([

        'icon'=>'📍',

        'title'=>$spot->name,

        'message'=>'New tourist spot submitted',

        'profile_image'=>null,

        'date'=>$spot->created_at

    ]);

}

foreach($recentHotels as $hotel){

    $activities->push([

        'icon'=>'🏨',

        'title'=>$hotel->name,

        'message'=>'New hotel added',

        'profile_image'=>null,

        'date'=>$hotel->created_at

    ]);

}

foreach($recentFoods as $food){

    $activities->push([

        'icon'=>'🍽',

        'title'=>$food->name,

        'message'=>'New restaurant added',

        'profile_image'=>null,

        'date'=>$food->created_at

    ]);

}

    

    $activities = $activities
        ->sortByDesc('date');

    return view(
        'admin.activities',
        compact('activities')
    );
}
}