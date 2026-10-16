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
use App\Models\Notification;
use App\Support\BookingDocument;
use App\Support\UpcomingReservations;
use App\Support\VisitorReport;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;


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

        // Resolved feedback has already been handled, so it must not remain in
        // the dashboard warning badge.
        $newFeedback = Feedback::where('status', '!=', 'resolved')->count();

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

$activities = $this->recentActivities(6);


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


        $bookingPlaces = collect();

        foreach ([
            ['model' => Booking::class, 'key' => 'spot_id', 'place' => Spot::class, 'kind' => 'Tourist spot', 'units' => 'quantity'],
            ['model' => HotelBooking::class, 'key' => 'hotel_id', 'place' => Hotel::class, 'kind' => 'Hotel', 'units' => 'rooms'],
            ['model' => FoodBooking::class, 'key' => 'food_id', 'place' => Food::class, 'kind' => 'Restaurant', 'units' => 'guests'],
        ] as $source) {
            $rows = $source['model']::query()
                ->selectRaw($source['key'].' as place_id, COUNT(*) as bookings, COALESCE(SUM('.$source['units'].'),0) as units, COALESCE(SUM(CASE WHEN arrived_at IS NOT NULL THEN 1 ELSE 0 END),0) as arrived, COALESCE(SUM(CASE WHEN status = \'approved\' THEN total_price ELSE 0 END),0) as revenue')
                ->groupBy($source['key'])
                ->get();
            $names = $source['place']::withTrashed()->whereIn('id', $rows->pluck('place_id'))->pluck('name', 'id');
            foreach ($rows as $row) {
                $bookingPlaces->push([
                    'name' => $names->get($row->place_id) ?? 'Removed listing',
                    'kind' => $source['kind'],
                    'bookings' => (int) $row->bookings,
                    'units' => (int) $row->units,
                    'arrived' => (int) $row->arrived,
                    'revenue' => (float) $row->revenue,
                ]);
            }
        }

        $bookingPlaces = $bookingPlaces->sortByDesc('bookings')->values();
        $notifications = Notification::where('user_id', Auth::id())->latest()->take(6)->get();
        $unreadCount = Notification::where('user_id', Auth::id())->where('is_read', false)->count();
        $upcomingBookings = UpcomingReservations::list();
        $dayPlans = \App\Models\DayPlan::with(['user', 'spot', 'hotel', 'food'])
            ->whereDate('plan_date', '>=', now()->toDateString())
            ->orderBy('plan_date')
            ->take(6)
            ->get();

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
                'bookingPlaces',
                'notifications',
                'unreadCount',
                'upcomingBookings',
                'dayPlans',
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

    public function account(User $user)
    {
        abort_unless(Auth::check() && Auth::user()->role === 'admin', 403);
        abort_unless(in_array($user->role, ['user', 'owner'], true), 404);

        $listings = $user->role === 'owner'
            ? Spot::where('user_id', $user->id)->count() + Hotel::where('owner_id', $user->id)->count() + Food::where('owner_id', $user->id)->count()
            : 0;

        return view('admin.account', compact('user', 'listings'));
    }

    /** Allow an administrator to set a replacement password without exposing it. */
    public function resetAccountPassword(Request $request, User $user)
    {
        abort_unless(Auth::check() && Auth::user()->role === 'admin', 403);
        abort_unless(in_array($user->role, ['user', 'owner'], true), 422);

        $request->validate([
            'password' => ['required', 'string', 'min:8', 'confirmed'],
        ]);

        $user->update(['password' => Hash::make($request->password)]);

        return back()->with('success', "Password updated for {$user->name}.");
    }

    /** Update the contact and business details shown to an administrator. */
    public function updateAccount(Request $request, User $user)
    {
        abort_unless(Auth::check() && Auth::user()->role === 'admin', 403);
        abort_unless(in_array($user->role, ['user', 'owner'], true), 422);

        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', 'unique:users,email,'.$user->id],
            'business_type' => ['nullable', 'string', 'max:100'],
        ]);

        $user->update($request->only('name', 'email', 'business_type'));

        return back()->with('success', "Account details updated for {$user->name}.");
    }

    public function deleteAccount(User $user)
    {
        abort_unless(Auth::check() && Auth::user()->role === 'admin', 403);
        abort_if($user->id === Auth::id(), 422, 'You cannot delete your own administrator account.');
        $name = $user->name;
        $user->delete();
        return back()->with('success', "Account for {$name} was deleted.");
    }

    public function activities()
    {
        $activities = $this->recentActivities(24);

        return view('admin.activities', compact('activities'));
    }

    public function visitorReport(Request $request)
    {
        abort_unless(Auth::user()?->role === 'admin', 403);
        $month = $this->reportMonth($request);
        $rows = VisitorReport::month($month);

        return view('admin.visitor-report', compact('month', 'rows'));
    }

    public function visitorReportPdf(Request $request)
    {
        abort_unless(Auth::user()?->role === 'admin', 403);
        $month = $this->reportMonth($request);
        $rows = VisitorReport::month($month)->map(fn ($row) => [
            $row['name'],
            $row['kind'],
            (string) $row['bookings'],
            (string) $row['arrived'],
            'PHP '.number_format($row['revenue'], 2),
        ])->all();

        $pdf = BookingDocument::report(
            'Visitor report',
            $month->format('F Y'),
            ['Establishment', 'Type', 'Booked', 'Arrived', 'Approved'],
            $rows
        );

        return response($pdf, 200, [
            'Content-Type' => 'application/pdf',
            'Content-Disposition' => 'attachment; filename="spottour-visitors-'.$month->format('Y-m').'.pdf"',
        ]);
    }

    private function recentActivities(int $limit)
    {
        $items = [];
        $push = function (array $item) use (&$items) {
            $items[$item['key']] ??= $item;
        };

        foreach (User::latest()->take(20)->get() as $user) {
            $push($this->activityItem('user-'.$user->id, $user->name, 'Registered an account', 'account', $user->created_at, $user->profile_image));
        }

        foreach (Spot::latest()->take(20)->get() as $spot) {
            $push($this->activityItem('spot-'.$spot->id, $spot->name, 'New tourist spot submitted', 'spot', $spot->created_at));
        }

        foreach (Hotel::latest()->take(20)->get() as $hotel) {
            $push($this->activityItem('hotel-'.$hotel->id, $hotel->name, 'New hotel added', 'hotel', $hotel->created_at));
        }

        foreach (Food::latest()->take(20)->get() as $food) {
            $push($this->activityItem('food-'.$food->id, $food->name, 'New restaurant added', 'restaurant', $food->created_at));
        }

        foreach (Booking::with('user', 'spot')->latest()->take(40)->get() as $booking) {
            $place = optional($booking->spot)->name ?? 'a removed spot';
            $when = $booking->booking_date ? Carbon::parse($booking->booking_date)->format('M j, Y') : 'an open date';
            $push($this->activityItem(
                'spot-booking-'.$booking->user_id.'-'.$booking->spot_id.'-'.$booking->booking_date,
                optional($booking->user)->name ?? 'Unknown traveller',
                'Booked '.$place.' · '.$when.' · '.$this->statusLabel($booking->status),
                'booking',
                $booking->created_at,
                optional($booking->user)->profile_image
            ));
        }

        foreach (HotelBooking::with('user', 'hotel')->latest()->take(40)->get() as $booking) {
            $place = optional($booking->hotel)->name ?? 'a removed hotel';
            $stay = ($booking->check_in && $booking->check_out)
                ? Carbon::parse($booking->check_in)->format('M j').' – '.Carbon::parse($booking->check_out)->format('M j, Y')
                : 'dates not set';
            $push($this->activityItem(
                'hotel-booking-'.$booking->user_id.'-'.$booking->hotel_id.'-'.$booking->check_in.'-'.$booking->check_out,
                optional($booking->user)->name ?? 'Unknown traveller',
                'Booked '.$place.' · '.$stay.' · '.$this->statusLabel($booking->status),
                'booking',
                $booking->created_at,
                optional($booking->user)->profile_image
            ));
        }

        foreach (FoodBooking::with('user', 'food')->latest()->take(40)->get() as $booking) {
            $place = optional($booking->food)->name ?? 'a removed restaurant';
            $when = $booking->reservation_date ? Carbon::parse($booking->reservation_date)->format('M j, Y') : 'an open date';
            $push($this->activityItem(
                'food-booking-'.$booking->user_id.'-'.$booking->food_id.'-'.$booking->reservation_date,
                optional($booking->user)->name ?? 'Unknown traveller',
                'Reserved '.$place.' · '.$when.' · '.$this->statusLabel($booking->status),
                'booking',
                $booking->created_at,
                optional($booking->user)->profile_image
            ));
        }

        return collect($items)->sortByDesc('date')->take($limit)->values();
    }

    private function activityItem(string $key, ?string $title, string $message, string $kind, $date, ?string $image = null): array
    {
        $title = trim((string) $title) !== '' ? $title : 'Unknown';

        return [
            'key' => $key,
            'icon' => strtoupper(mb_substr($title, 0, 1)),
            'title' => $title,
            'message' => $message,
            'kind' => $kind,
            'profile_image' => $image,
            'date' => $date,
        ];
    }

    private function statusLabel(?string $status): string
    {
        return ucfirst(str_replace('_', ' ', $status ?: 'pending'));
    }

    private function reportMonth(Request $request): Carbon
    {
        $value = (string) $request->query('month', now()->format('Y-m'));

        return Carbon::createFromFormat('Y-m', preg_match('/^\d{4}-\d{2}$/', $value) ? $value : now()->format('Y-m'))->startOfMonth();
    }
}
