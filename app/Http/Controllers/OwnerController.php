<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Spot;
use App\Models\Hotel;
use App\Models\Food;

use App\Models\Booking;
use App\Models\HotelBooking;
use App\Models\FoodBooking;

use App\Models\SpotComment;
use App\Models\HotelComment;
use App\Models\FoodComment;

use Illuminate\Support\Facades\Auth;
use App\Models\Notification;
use App\Support\UpcomingReservations;

class OwnerController extends Controller
{
    /** Data source for the revenue period picker on the owner dashboard. */
    public function revenuePeriod(Request $request)
    {
        $period = $request->string('period', 'year')->toString();
        abort_unless(in_array($period, ['today', 'week', 'month', 'year'], true), 422);
        $ownerId = Auth::id();
        $start = match ($period) {
            'today' => now()->startOfDay(),
            'week' => now()->startOfWeek(),
            'month' => now()->startOfMonth(),
            default => now()->startOfYear(),
        };

        $spot = Booking::where('status', 'approved')->where('created_at', '>=', $start)->whereHas('spot', fn ($q) => $q->where('user_id', $ownerId))->get(['created_at', 'total_price']);
        $hotel = HotelBooking::where('status', 'approved')->where('created_at', '>=', $start)->whereHas('hotel', fn ($q) => $q->where('owner_id', $ownerId))->get(['created_at', 'total_price']);
        $food = FoodBooking::where('status', 'approved')->where('created_at', '>=', $start)->whereHas('food', fn ($q) => $q->where('owner_id', $ownerId))->get(['created_at', 'total_price']);
        $all = $spot->concat($hotel)->concat($food);

        $label = match ($period) {
            'today' => 'Today\'s Revenue',
            'week' => 'This Week\'s Revenue',
            'month' => 'This Month\'s Revenue',
            default => 'This Year\'s Revenue',
        };

        if ($period === 'today') {
            $keys = range(0, 23);
            $labels = collect($keys)->map(fn ($hour) => sprintf('%02d:00', $hour))->all();
            $data = collect($keys)->map(fn ($hour) => (float) $all->filter(fn ($booking) => $booking->created_at->hour === $hour)->sum('total_price'))->all();
        } elseif ($period === 'week') {
            $keys = range(0, 6);
            $labels = collect($keys)->map(fn ($day) => now()->startOfWeek()->addDays($day)->format('D'))->all();
            $data = collect($keys)->map(fn ($day) => (float) $all->filter(fn ($booking) => $booking->created_at->isSameDay(now()->startOfWeek()->addDays($day)))->sum('total_price'))->all();
        } elseif ($period === 'month') {
            $keys = range(1, now()->daysInMonth);
            $labels = collect($keys)->map(fn ($day) => (string) $day)->all();
            $data = collect($keys)->map(fn ($day) => (float) $all->filter(fn ($booking) => $booking->created_at->day === $day)->sum('total_price'))->all();
        } else {
            $keys = range(1, 12);
            $labels = collect($keys)->map(fn ($month) => now()->setMonth($month)->format('M'))->all();
            $data = collect($keys)->map(fn ($month) => (float) $all->filter(fn ($booking) => $booking->created_at->month === $month)->sum('total_price'))->all();
        }
        return response()->json(['label' => $label, 'labels' => $labels, 'data' => $data]);
    }

    public function bookingPeriod(Request $request)
    {
        $period = $request->string('period', 'month')->toString();
        abort_unless(in_array($period, ['month', 'year'], true), 422);
        $ownerId = Auth::id();
        $start = $period === 'month' ? now()->startOfMonth() : now()->startOfYear();
        $spot = Booking::where('created_at', '>=', $start)->whereHas('spot', fn ($q) => $q->where('user_id', $ownerId))->get(['created_at']);
        $hotel = HotelBooking::where('created_at', '>=', $start)->whereHas('hotel', fn ($q) => $q->where('owner_id', $ownerId))->get(['created_at']);
        $food = FoodBooking::where('created_at', '>=', $start)->whereHas('food', fn ($q) => $q->where('owner_id', $ownerId))->get(['created_at']);
        $all = $spot->concat($hotel)->concat($food);
        if ($period === 'month') {
            $labels = collect(range(1, now()->daysInMonth))->map(fn ($day) => (string) $day)->all();
            $data = collect(range(1, now()->daysInMonth))->map(fn ($day) => $all->filter(fn ($booking) => $booking->created_at->day === $day)->count())->all();
            return response()->json(['label' => "This Month's Bookings", 'labels' => $labels, 'data' => $data]);
        }
        $labels = collect(range(1, 12))->map(fn ($month) => now()->setMonth($month)->format('M'))->all();
        $data = collect(range(1, 12))->map(fn ($month) => $all->filter(fn ($booking) => $booking->created_at->month === $month)->count())->all();
        return response()->json(['label' => "This Year's Bookings", 'labels' => $labels, 'data' => $data]);
    }

    public function dashboard()
{
    $ownerId = Auth::id();

        /*
    |--------------------------------------------------------------------------
    | TOTAL LISTINGS
    |--------------------------------------------------------------------------
    */

    $spotListings = Spot::where(
        'user_id',
        $ownerId
    )->count();

    $hotelListings = Hotel::where(
        'owner_id',
        $ownerId
    )->count();

    $foodListings = Food::where(
        'owner_id',
        $ownerId
    )->count();

    $listings =
        $spotListings +
        $hotelListings +
        $foodListings;
    
    /*
    |--------------------------------------------------------------------------
    | TOTAL BOOKINGS
    |--------------------------------------------------------------------------
    */

    $spotBookings = Booking::whereHas(
        'spot',
        function($query) use ($ownerId){

            $query->where(
                'user_id',
                $ownerId
            );

        }
    )->count();

    $hotelBookings = HotelBooking::whereHas(
        'hotel',
        function($query) use ($ownerId){

            $query->where(
                'owner_id',
                $ownerId
            );

        }
    )->count();

    $foodBookings = FoodBooking::whereHas(
        'food',
        function($query) use ($ownerId){

            $query->where(
                'owner_id',
                $ownerId
            );

        }
    )->count();

    $totalBookings =
        $spotBookings +
        $hotelBookings +
        $foodBookings;

    

        /*
    |--------------------------------------------------------------------------
    | PENDING BOOKINGS
    |--------------------------------------------------------------------------
    */

    $pendingSpot = Booking::where(
        'status',
        'pending'
    )
    ->whereHas(
        'spot',
        function($query) use ($ownerId){

            $query->where(
                'user_id',
                $ownerId
            );

        }
    )->count();

    $pendingHotel = HotelBooking::where(
        'status',
        'pending'
    )
    ->whereHas(
        'hotel',
        function($query) use ($ownerId){

            $query->where(
                'owner_id',
                $ownerId
            );

        }
    )->count();

    $pendingFood = FoodBooking::where(
        'status',
        'pending'
    )
    ->whereHas(
        'food',
        function($query) use ($ownerId){

            $query->where(
                'owner_id',
                $ownerId
            );

        }
    )->count();

    $pendingBookings =
        $pendingSpot +
        $pendingHotel +
        $pendingFood;

    /*
    |--------------------------------------------------------------------------
    | TOTAL REVIEWS
    |--------------------------------------------------------------------------
    */

    $spotReviews = SpotComment::whereHas(
        'spot',
        function($query) use ($ownerId){

            $query->where(
                'user_id',
                $ownerId
            );

        }
    )->count();

    $hotelReviews = HotelComment::whereHas(
        'hotel',
        function($query) use ($ownerId){

            $query->where(
                'owner_id',
                $ownerId
            );

        }
    )->count();

    $foodReviews = FoodComment::whereHas(
        'food',
        function($query) use ($ownerId){

            $query->where(
                'owner_id',
                $ownerId
            );

        }
    )->count();

    $totalReviews =
        $spotReviews +
        $hotelReviews +
        $foodReviews;

    
    /*
    |--------------------------------------------------------------------------
    | RECENT BOOKINGS
    |--------------------------------------------------------------------------
    */

    $latestSpotBookings = Booking::with(
    'user',
    'spot'
)
->whereHas('spot', function ($query) use ($ownerId) {

    $query->where(
        'user_id',
        $ownerId
    );

})
->latest()
->take(5)
->get();

$latestHotelBookings = HotelBooking::with(
    'user',
    'hotel'
)
->whereHas('hotel', function ($query) use ($ownerId) {

    $query->where(
        'owner_id',
        $ownerId
    );

})
->latest()
->take(5)
->get();

$latestFoodBookings = FoodBooking::with(
    'user',
    'food'
)
->whereHas('food', function ($query) use ($ownerId) {

    $query->where(
        'owner_id',
        $ownerId
    );

})
->latest()
->take(5)
->get();

$latestBookings = $latestSpotBookings
    ->concat($latestHotelBookings)
    ->concat($latestFoodBookings)
    ->sortByDesc('created_at')
    ->take(5);


   /*
|--------------------------------------------------------------------------
| RECENT REVIEWS
|--------------------------------------------------------------------------
*/

$latestSpotReviews = SpotComment::with(
    'user',
    'spot.ratings',
    'ratings'
)
->whereHas('spot', function ($query) use ($ownerId) {

    $query->where(
        'user_id',
        $ownerId
    );

})
->latest()
->take(5)
->get();

$latestHotelReviews = HotelComment::with(
    'user',
    'hotel.ratings',
    'ratings'
)
->whereHas('hotel', function ($query) use ($ownerId) {

    $query->where(
        'owner_id',
        $ownerId
    );

})
->latest()
->take(5)
->get();

$latestFoodReviews = FoodComment::with(
    'user',
    'food.ratings',
    'ratings'
)
->whereHas('food', function ($query) use ($ownerId) {

    $query->where(
        'owner_id',
        $ownerId
    );

})
->latest()
->take(5)
->get();

$latestReviews = $latestSpotReviews
    ->concat($latestHotelReviews)
    ->concat($latestFoodReviews)
    ->sortByDesc('created_at')
    ->take(5);

    /*
|--------------------------------------------------------------------------
| TOTAL REVENUE
|--------------------------------------------------------------------------
*/

$spotRevenue = \App\Models\Booking::where(
    'status',
    'approved'
)
->whereHas(
    'spot',
    function ($query) use ($ownerId) {

        $query->where(
            'user_id',
            $ownerId
        );

    }
)
->sum('total_price');

$hotelRevenue = \App\Models\HotelBooking::where(
    'status',
    'approved'
)
->whereHas(
    'hotel',
    function ($query) use ($ownerId) {

        $query->where(
            'owner_id',
            $ownerId
        );

    }
)
->sum('total_price');

$revenueLabels = [
    'Jan','Feb','Mar','Apr','May','Jun',
    'Jul','Aug','Sep','Oct','Nov','Dec'
];

$revenueData = [];

for($month = 1; $month <= 12; $month++)
{
    $spotRevenue = Booking::where('status', 'approved')
        ->whereMonth('created_at', $month)
        ->whereHas('spot', function($query) use ($ownerId){
            $query->where('user_id', $ownerId);
        })
        ->sum('total_price');

    $hotelRevenue = HotelBooking::where('status', 'approved')
        ->whereMonth('created_at', $month)
        ->whereHas('hotel', function($query) use ($ownerId){
            $query->where('owner_id', $ownerId);
        })
        ->sum('total_price');

    $foodRevenue = FoodBooking::where('status', 'approved')
        ->whereMonth('created_at', $month)
        ->whereHas('food', function($query) use ($ownerId){
            $query->where('owner_id', $ownerId);
        })
        ->sum('total_price');

    $revenueData[] =
        $spotRevenue +
        $hotelRevenue +
        $foodRevenue;
}

$totalRevenue =

Booking::where('status','approved')
->whereHas('spot',function($query) use($ownerId){

    $query->where('user_id',$ownerId);

})
->sum('total_price')

+

HotelBooking::where('status','approved')
->whereHas('hotel',function($query) use($ownerId){

    $query->where('owner_id',$ownerId);

})
->sum('total_price')

+

FoodBooking::where('status','approved')
->whereHas('food',function($query) use($ownerId){

    $query->where('owner_id',$ownerId);

})
->sum('total_price');

/*
|--------------------------------------------------------------------------
| MONTHLY BOOKINGS
|--------------------------------------------------------------------------
*/

$bookingLabels = [
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

$bookingData = [];

for($month = 1; $month <= 12; $month++)
{
    $spotCount = Booking::whereMonth(
        'created_at',
        $month
    )
    ->whereHas(
        'spot',
        function($query) use ($ownerId){

            $query->where(
                'user_id',
                $ownerId
            );

        }
    )
    ->count();

    $hotelCount = HotelBooking::whereMonth(
        'created_at',
        $month
    )
    ->whereHas(
        'hotel',
        function($query) use ($ownerId){

            $query->where(
                'owner_id',
                $ownerId
            );

        }
    )
    ->count();

    $foodCount = FoodBooking::whereMonth(
        'created_at',
        $month
    )
    ->whereHas(
        'food',
        function($query) use ($ownerId){

            $query->where(
                'owner_id',
                $ownerId
            );

        }
    )
    ->count();

    $bookingData[] =
        $spotCount +
        $hotelCount +
        $foodCount;
}

$topSpot = Spot::where(
    'user_id',
    $ownerId
)
->withCount('bookings')
->orderByDesc('bookings_count')
->first();

$topHotel = Hotel::where(
    'owner_id',
    $ownerId
)
->withCount('bookings')
->orderByDesc('bookings_count')
->first();

$topFood = Food::where(
    'owner_id',
    $ownerId
)
->withCount('bookings')
->orderByDesc('bookings_count')
->first();

$approvedBookings =

Booking::where('status','approved')
->whereHas('spot',function($q) use($ownerId){

    $q->where('user_id',$ownerId);

})
->count()

+

HotelBooking::where('status','approved')
->whereHas('hotel',function($q) use($ownerId){

    $q->where('owner_id',$ownerId);

})
->count()

+

FoodBooking::where('status','approved')
->whereHas('food',function($q) use($ownerId){

    $q->where('owner_id',$ownerId);

})
->count();

$completionRate =

$totalBookings > 0

?

round(

($approvedBookings/$totalBookings)*100

)

:

0;

/*
|--------------------------------------------------------------------------
| TOTAL CUSTOMERS
|--------------------------------------------------------------------------
*/

$totalCustomers = Booking::whereHas(
    'spot',
    function($query) use ($ownerId){

        $query->where(
            'user_id',
            $ownerId
        );

    }
)
->distinct('user_id')
->count('user_id')

+

HotelBooking::whereHas(
    'hotel',
    function($query) use ($ownerId){

        $query->where(
            'owner_id',
            $ownerId
        );

    }
)
->distinct('user_id')
->count('user_id')

+

FoodBooking::whereHas(
    'food',
    function($query) use ($ownerId){

        $query->where(
            'owner_id',
            $ownerId
        );

    }
)
->distinct('user_id')
->count('user_id');



$upcomingBookings = UpcomingReservations::list($ownerId);

$dayPlans = \App\Models\DayPlan::with(['user', 'spot', 'hotel', 'food'])
    ->whereDate('plan_date', '>=', now()->toDateString())
    ->where(function ($query) use ($ownerId) {
        $query->whereHas('spot', fn ($spot) => $spot->where('user_id', $ownerId))
            ->orWhereHas('hotel', fn ($hotel) => $hotel->where('owner_id', $ownerId))
            ->orWhereHas('food', fn ($food) => $food->where('owner_id', $ownerId));
    })
    ->orderBy('plan_date')
    ->take(6)
    ->get();

$notifications = Notification::where('user_id', $ownerId)->latest()->take(6)->get();
$unreadCount = Notification::where('user_id', $ownerId)->where('is_read', false)->count();

    
    return view(
    'owner',
    compact(
        'listings',
        'totalBookings',
        'pendingBookings',
        'totalReviews',
        'totalRevenue',
        'latestBookings',
        'latestReviews',
        'bookingLabels',
        'bookingData',
        'topSpot',
        'topHotel',
        'topFood',
        'completionRate',
        'totalCustomers',
        'revenueLabels',
        'revenueData',
        'upcomingBookings',
        'dayPlans',
        'notifications',
        'unreadCount'
    )
);
}

    public function reviews()
{
    $ownerId = Auth::id();

    $listings = collect()
        ->concat(Spot::with('ratings.user')->where('user_id', $ownerId)->get()->map(fn ($spot) => (object) [
            'kind' => 'spot',
            'type' => 'Tourist spot',
            'name' => $spot->name,
            'url' => '/owner/spots/'.$spot->id,
            'ratings' => $spot->ratings,
        ]))
        ->concat(Hotel::with('ratings.user')->where('owner_id', $ownerId)->get()->map(fn ($hotel) => (object) [
            'kind' => 'hotel',
            'type' => 'Hotel',
            'name' => $hotel->name,
            'url' => '/owner/hotels/'.$hotel->id,
            'ratings' => $hotel->ratings,
        ]))
        ->concat(Food::with('ratings.user')->where('owner_id', $ownerId)->get()->map(fn ($food) => (object) [
            'kind' => 'food',
            'type' => 'Restaurant',
            'name' => $food->name,
            'url' => '/owner/foods/'.$food->id,
            'ratings' => $food->ratings,
        ]))
        ->sortByDesc(fn ($listing) => optional($listing->ratings->max('created_at'))->timestamp ?? 0)
        ->values();

    return view('owner.reviews', compact('listings'));
}
       
}
