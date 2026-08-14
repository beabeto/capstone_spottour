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

class OwnerController extends Controller
{
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

$latestHotelReviews = HotelComment::with(
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

$latestFoodReviews = FoodComment::with(
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



/*
|--------------------------------------------------------------------------
| UPCOMING BOOKINGS
|--------------------------------------------------------------------------
*/

$upcomingSpotBookings = Booking::with('user','spot')
    ->where('booking_date', '>=', now()->toDateString())
    ->whereHas('spot', function ($query) use ($ownerId) {

        $query->where('user_id', $ownerId);

    })
    ->get();

$upcomingHotelBookings = HotelBooking::with('user','hotel')
    ->where('check_in', '>=', now()->toDateString())
    ->whereHas('hotel', function ($query) use ($ownerId) {

        $query->where('owner_id', $ownerId);

    })
    ->get();

$upcomingFoodBookings = FoodBooking::with('user','food')
    ->where('reservation_date', '>=', now()->toDateString())
    ->whereHas('food', function ($query) use ($ownerId) {

        $query->where('owner_id', $ownerId);

    })
    ->get();

$upcomingBookings = collect();

foreach($upcomingSpotBookings as $booking){

    $upcomingBookings->push((object)[

    'customer' => $booking->user->name,
    'listing' => optional($booking->spot)->name,
    'category' => 'Spot',
    'date' => $booking->booking_date,
    'status' => $booking->status

]);

}

foreach($upcomingHotelBookings as $booking){

   $upcomingBookings->push((object)[

        'customer' => $booking->user->name,

        'listing' => optional($booking->hotel)->name,

        'category' => 'Hotel',

        'date' => $booking->check_in,

        'status' => $booking->status

    ]);

}

foreach($upcomingFoodBookings as $booking){

    $upcomingBookings->push((object)[

        'customer' => $booking->user->name,

        'listing' => optional($booking->food)->name,

        'category' => 'Restaurant',

        'date' => $booking->reservation_date,

        'status' => $booking->status

    ]);

}

$upcomingBookings = $upcomingBookings
    ->sortBy('date')
    ->take(5);

    
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
        'upcomingBookings'
    )
);
}

    public function reviews()
{
    $ownerId = Auth::id();

    // SPOT COMMENTS
    $spotComments = SpotComment::with(
        'user',
        'spot',
        'spot.ratings',
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

    // HOTEL COMMENTS
    $hotelComments = HotelComment::with(
        'user',
        'hotel',
        'hotel.ratings',
        'ratings',
        'reactions'
    )
    ->whereHas('hotel', function ($query) use ($ownerId) {

        $query->where(
            'owner_id',
            $ownerId
        );

    })
    ->get();

    // FOOD COMMENTS
    $foodComments = FoodComment::with(
        'user',
        'food',
        'food.ratings',
        'ratings',
        'reactions'
    )
    ->whereHas('food', function ($query) use ($ownerId) {

        $query->where(
            'owner_id',
            $ownerId
        );

    })
    ->get();

    // COMBINE ALL COMMENTS
    $comments = $spotComments
        ->concat($hotelComments)
        ->concat($foodComments)
        ->sortByDesc('created_at');

    return view(
        'owner.reviews',
        compact('comments')
    );
}
       
}