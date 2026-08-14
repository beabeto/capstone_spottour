<?php

namespace App\Http\Controllers;
use App\Models\Spot;
use Illuminate\Support\Facades\Auth;
use App\Models\Booking;
use App\Models\Notification;
use App\Models\Hotel;
use App\Models\HotelBooking;
use App\Models\Food;
use App\Models\FoodBooking;
use Illuminate\Http\Request;

class BookingController extends Controller
{


public function ownerBookings()
{
    $spotBookings = Booking::with(
        'user',
        'spot'
    )
    ->whereHas('spot', function ($query) {

        $query->where(
            'user_id',
            Auth::id()
        );

    })
    ->latest()
    ->get();

    $hotelBookings = HotelBooking::with(
        'user',
        'hotel'
    )
    ->whereHas('hotel', function ($query) {

        $query->where(
            'owner_id',
            Auth::id()
        );

    })
    ->latest()
    ->get();

    $foodBookings = FoodBooking::with(
    'user',
    'food'
)
->whereHas('food', function ($query) {

    $query->where(
        'owner_id',
        Auth::id()
    );

})
->latest()
->get();

    return view(
    'owner.bookings',
    compact(
        'spotBookings',
        'hotelBookings',
        'foodBookings'
    )
);
}



    public function approve($id)
{
    $booking = Booking::findOrFail($id);

    $booking->update([
        'status' => 'approved'
    ]);

    Notification::create([

    'user_id' => $booking->user_id,

    'sender_id' => Auth::id(),

    'spot_id' => $booking->spot_id,

    'message' => 'BOOKING APPROVED TEST',

    'is_read' => 0

]);

    return back()->with(
        'success',
        'Booking approved.'
    );
}

    public function reject($id)
{
    $booking = Booking::findOrFail($id);

    $booking->update([
        'status' => 'rejected'
    ]);

    Notification::create([

        'user_id' => $booking->user_id,

        'sender_id' => Auth::id(),

        'spot_id' => $booking->spot_id,

        'message' =>
            '❌ Your booking for "' .
            $booking->spot->name .
            '" has been rejected.',

        'is_read' => 0

    ]);

    return back()->with(
        'success',
        'Booking rejected.'
    );
}

    public function store(Request $request)
{
    $spot = Spot::findOrFail(
        $request->spot_id
    );

    if($spot->booking_type == 'cottage')
    {
        $total =
            $spot->cottage_price *
            $request->quantity;
    }
    else
    {
        $total = $spot->price;
    }

    $receipt = null;

    if($request->hasFile('receipt'))
    {
        $file = $request->file('receipt');

        $receipt =
            time().'_'.
            $file->getClientOriginalName();

        $file->move(
            public_path('uploads/receipts'),
            $receipt
        );
    }

    Booking::create([

        'user_id' => Auth::id(),

        'spot_id' => $request->spot_id,

        'booking_date' => $request->booking_date,

        'quantity' => $request->quantity,

        'total_price' => $total,

        'receipt' => $receipt,

        'payer_name' => $request->payer_name,

        'payer_number' => $request->payer_number,

        'status' => 'pending'

    ]);

    return redirect(
        '/user/bookings'
    )->with(
        'success',
        'Booking created successfully.'
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

public function confirm(Request $request)
{
    $spot = Spot::findOrFail(
        $request->spot_id
    );

    return view(
        'user.booking-confirm',
        compact('spot')
    );
}

public function paymentPage(Request $request)
{
    $spot = Spot::findOrFail(
        $request->spot_id
    );

    return view(
        'user.payment',
        [
            'spot' => $spot,
            'booking_date' =>
            $request->booking_date,
            'quantity' =>
            $request->quantity
        ]
    );
}

public function calendar()
{
    $bookings = Booking::with(
        'user',
        'spot'
    )->get();

    $hotelBookings = HotelBooking::with(
        'user',
        'hotel'
    )->get();

    $foodBookings = FoodBooking::with(
        'user',
        'food'
    )->get();

    return view(
        'owner.calendar',
        compact(
            'bookings',
            'hotelBookings',
            'foodBookings'
        )
    );
}

public function cancel($id)
{
    $booking = Booking::findOrFail($id);

    if($booking->user_id != Auth::id())
    {
        abort(403);
    }

    if($booking->status != 'pending')
    {
        return back()->with(
            'success',
            'Only pending bookings can be cancelled.'
        );
    }

    $booking->update([

        'status' => 'cancelled'

    ]);

    Notification::create([

        'user_id' => $booking->spot->owner_id,

        'sender_id' => Auth::id(),

        'spot_id' => $booking->spot_id,

        'message' =>
            Auth::user()->name .
            ' cancelled a booking for "' .
            $booking->spot->name .
            '".',

        'is_read' => 0

    ]);

    return back()->with(
        'success',
        'Booking cancelled successfully.'
    );
}

public function hotelConfirm(Request $request)
{
    $hotel = Hotel::findOrFail(
        $request->hotel_id
    );

    return view(
        'user.hotel-booking-confirm',
        compact('hotel')
    );
}

public function hotelStore(Request $request)
{
    $hotel = Hotel::findOrFail(
        $request->hotel_id
    );

    $receipt = null;

    if($request->hasFile('receipt'))
    {
        $file = $request->file('receipt');

        $receipt =
            time().'_'.
            $file->getClientOriginalName();

        $file->move(
            public_path('uploads/receipts'),
            $receipt
        );
    }

    HotelBooking::create([

        'user_id' => Auth::id(),

        'hotel_id' => $hotel->id,

        'check_in' => $request->check_in,

        'check_out' => $request->check_out,

        'rooms' => $request->rooms,

        'total_price' => $request->total_price,

        'payer_name' => $request->payer_name,

        'payer_number' => $request->payer_number,

        'receipt' => $receipt,

        'status' => 'pending'

    ]);

    Notification::create([

        'user_id' => $hotel->owner_id,

        'sender_id' => Auth::id(),

        'hotel_id' => $hotel->id,

        'message' =>
            Auth::user()->name .
            ' submitted a hotel reservation.',

        'is_read' => 0

    ]);

    return redirect('/user/bookings')
        ->with(
            'success',
            'Hotel reservation submitted successfully.'
        );
}


public function hotelPaymentPage(Request $request)
{
    if (!$request->hotel_id) {
        return redirect('/hotels');
    }

    $hotel = Hotel::findOrFail(
        $request->hotel_id
    );

    return view(
        'user.hotel-payment',
        [
            'hotel' => $hotel,
            'check_in' => $request->check_in,
            'check_out' => $request->check_out,
            'rooms' => $request->rooms
        ]
    );
}


public function approveHotel($id)
{
    $booking = HotelBooking::findOrFail($id);

    $booking->update([
        'status' => 'approved'
    ]);

    Notification::create([

        'user_id' => $booking->user_id,

        'sender_id' => Auth::id(),

        'hotel_id' => $booking->hotel_id,

        'message' =>
            '✅ Your hotel reservation for "' .
            $booking->hotel->name .
            '" has been approved.',

        'is_read' => 0

    ]);

    return back()->with(
        'success',
        'Hotel booking approved.'
    );
}

public function rejectHotel($id)
{
    $booking = HotelBooking::findOrFail($id);

    $booking->update([
        'status' => 'rejected'
    ]);

    Notification::create([

        'user_id' => $booking->user_id,

        'sender_id' => Auth::id(),

        'hotel_id' => $booking->hotel_id,

        'message' =>
            '❌ Your hotel reservation for "' .
            $booking->hotel->name .
            '" has been rejected.',

        'is_read' => 0

    ]);

    return back()->with(
        'success',
        'Hotel booking rejected.'
    );
}

public function adminBookings()
{
    $spotBookings = Booking::with(
        'user',
        'spot'
    )->latest()->get();

    $hotelBookings = HotelBooking::with(
        'user',
        'hotel'
    )->latest()->get();

    $foodBookings = FoodBooking::with(
    'user',
    'food'
)->latest()->get();

    $totalBookings =
    $spotBookings->count() +
    $hotelBookings->count() +
    $foodBookings->count();

    $approvedBookings =
    Booking::where('status','approved')->count() +
    HotelBooking::where('status','approved')->count() +
    FoodBooking::where('status','approved')->count();

    $pendingBookings =
    Booking::where('status','pending')->count() +
    HotelBooking::where('status','pending')->count() +
    FoodBooking::where('status','pending')->count();

    $rejectedBookings =
    Booking::where('status','rejected')->count() +
    HotelBooking::where('status','rejected')->count() +
    FoodBooking::where('status','rejected')->count();

    $cancelledBookings =
    Booking::where('status','cancelled')->count() +
    HotelBooking::where('status','cancelled')->count() +
    FoodBooking::where('status','cancelled')->count();

    return view(
        'admin.bookings',
        compact(
    'spotBookings',
    'hotelBookings',
    'foodBookings',
    'totalBookings',
    'approvedBookings',
    'pendingBookings',
    'rejectedBookings',
    'cancelledBookings'
)
    );
}


public function foodConfirm(Request $request)
{
    $food = Food::findOrFail(
        $request->food_id
    );

    return view(
        'user.food-booking-confirm',
        compact('food')
    );
}

public function foodPaymentPage(Request $request)
{
    if (!$request->food_id)
    {
        return redirect('/foods');
    }

    $food = Food::findOrFail(
        $request->food_id
    );

    $total = $food->price_per_guest * $request->guests;

    return view(
        'user.food-payment',
        [
            'food' => $food,
            'reservation_date' => $request->reservation_date,
            'reservation_time' => $request->reservation_time,
            'guests' => $request->guests,
            'total' => $total
        ]
    );
}

public function foodStore(Request $request)
{
    $request->validate([

    'food_id'=>'required',

    'reservation_date'=>'required',

    'reservation_time'=>'required',

    'guests'=>'required',

    'payer_name'=>'required',

    'payer_number'=>'required',

    'receipt'=>'required|image'

]);

    $food = Food::findOrFail(
        $request->food_id
    );

    $receipt = null;

    if($request->hasFile('receipt'))
    {
        $file = $request->file('receipt');

        $receipt =
            time().'_'.
            $file->getClientOriginalName();

        $file->move(
            public_path('uploads/receipts'),
            $receipt
        );
    }

    $total = $food->price_per_guest * $request->guests;

FoodBooking::create([

    'user_id' => Auth::id(),

    'food_id' => $request->food_id,

    'reservation_date' => $request->reservation_date,

    'reservation_time' => $request->reservation_time,

    'guests' => $request->guests,

    'special_request' => $request->special_request,

    'payer_name' => $request->payer_name,

    'payer_number' => $request->payer_number,

    'receipt' => $receipt,

    'total_price' => $total,

    'status' => 'pending'

]);

    Notification::create([

        'user_id' => $food->owner_id,

        'sender_id' => Auth::id(),

        'food_id' => $food->id,

        'message' =>
            Auth::user()->name .
            ' submitted a restaurant reservation.',

        'is_read' => 0

    ]);

    return redirect('/user/bookings')
        ->with(
            'success',
            'Restaurant reservation submitted successfully.'
        );
}

public function approveFood($id)
{
    $booking = FoodBooking::findOrFail($id);

    $booking->update([
        'status' => 'approved'
    ]);

    Notification::create([

        'user_id' => $booking->user_id,

        'sender_id' => Auth::id(),

        'food_id' => $booking->food_id,

        'message' =>
            '✅ Your restaurant reservation for "' .
            $booking->food->name .
            '" has been approved.',

        'is_read' => 0

    ]);

    return back()->with(
        'success',
        'Restaurant booking approved.'
    );
}

public function rejectFood($id)
{
    $booking = FoodBooking::findOrFail($id);

    $booking->update([
        'status' => 'rejected'
    ]);

    Notification::create([

        'user_id' => $booking->user_id,

        'sender_id' => Auth::id(),

        'food_id' => $booking->food_id,

        'message' =>
            '❌ Your restaurant reservation for "' .
            $booking->food->name .
            '" has been rejected.',

        'is_read' => 0

    ]);

    return back()->with(
        'success',
        'Restaurant booking rejected.'
    );
}

public function cancelFood($id)
{
    $booking = FoodBooking::findOrFail($id);

    if($booking->user_id != Auth::id())
    {
        abort(403);
    }

    if($booking->status != 'pending')
    {
        return back()->with(
            'success',
            'Only pending reservations can be cancelled.'
        );
    }

    $booking->update([

        'status' => 'cancelled'

    ]);

    Notification::create([

        'user_id' => $booking->food->owner_id,

        'sender_id' => Auth::id(),

        'food_id' => $booking->food_id,

        'message' =>
            Auth::user()->name .
            ' cancelled a restaurant reservation for "' .
            $booking->food->name .
            '".',

        'is_read' => 0

    ]);

    return back()->with(
        'success',
        'Restaurant reservation cancelled successfully.'
    );
}
}