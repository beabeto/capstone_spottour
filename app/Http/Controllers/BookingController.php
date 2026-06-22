<?php

namespace App\Http\Controllers;
use App\Models\Spot;
use Illuminate\Support\Facades\Auth;
use App\Models\Booking;
use App\Models\Notification;
use App\Models\Hotel;
use App\Models\HotelBooking;
use Illuminate\Http\Request;

class BookingController extends Controller
{

public function ownerBookings()
{
    $bookings = Booking::with(
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

    return view(
        'owner.bookings',
        compact('bookings')
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
    $bookings = Booking::with(
        'spot'
    )
    ->where(
        'user_id',
        Auth::id()
    )
    ->get();

    return view(
        'user.bookings',
        compact('bookings')
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

    return view(
        'owner.calendar',
        compact('bookings')
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

    $checkIn = \Carbon\Carbon::parse(
        $request->check_in
    );

    $checkOut = \Carbon\Carbon::parse(
        $request->check_out
    );

    $nights = $checkIn->diffInDays(
        $checkOut
    );

    $totalPrice =
        $hotel->price_per_night *
        $request->rooms *
        $nights;

    HotelBooking::create([

        'user_id' => Auth::id(),

        'hotel_id' => $hotel->id,

        'check_in' => $request->check_in,

        'check_out' => $request->check_out,

        'rooms' => $request->rooms,

        'total_price' => $totalPrice,

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

    return redirect(
        '/user/bookings'
    )->with(
        'success',
        'Hotel reservation submitted successfully.'
    );
}

}