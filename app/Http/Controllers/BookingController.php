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
use App\Support\BookingDocument;
use App\Support\PaymentChoice;
use App\Support\PayMongoCheckout;
use App\Support\BookingExtras;
use App\Support\ArrivalCode;
use App\Support\BookingNotice;
use App\Support\DailyCapacity;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;

class BookingController extends Controller
{
    public function downloadConfirmation(string $type, $id)
    {
        abort_unless(in_array($type, ['spot', 'hotel', 'food'], true), 404);
        abort_unless(Auth::check(), 403);

        $booking = $this->findBooking($type, $id);
        $payload = $this->confirmationPayload($booking, $type, $booking->status === 'approved');

        abort_unless(
            Auth::id() === $booking->user_id
            || Auth::id() === $payload['ownerId']
            || Auth::user()->role === 'admin',
            403
        );

        $pdf = BookingDocument::pdf($payload['title'], $payload['rows']);

        return response($pdf, 200, [
            'Content-Type' => 'application/pdf',
            'Content-Disposition' => 'attachment; filename="'.$payload['reference'].'.pdf"',
        ]);
    }

    private function findBooking(string $type, $id)
    {
        return match ($type) {
            'spot' => Booking::findOrFail($id),
            'hotel' => HotelBooking::findOrFail($id),
            'food' => FoodBooking::findOrFail($id),
        };
    }

    private function mailBooking($booking, string $type, bool $confirmed): void
    {
        $payload = $this->confirmationPayload($booking, $type, $confirmed);

        if (! filter_var($payload['email'], FILTER_VALIDATE_EMAIL)) {
            return;
        }

        $pdf = BookingDocument::pdf($payload['title'], $payload['rows']);

        try {
            Mail::send('emails.booking-confirmation', $payload, function ($message) use ($payload, $pdf) {
                $message->to($payload['email'])
                    ->subject($payload['subject'])
                    ->attachData($pdf, $payload['reference'].'.pdf', ['mime' => 'application/pdf']);
            });
        } catch (\Throwable $exception) {
            report($exception);
        }
    }

    private function confirmationPayload($booking, string $type, bool $confirmed): array
    {
        $booking->loadMissing('user');
        $guest = $booking->payer_name ?: ($booking->user->name ?? 'Guest');
        $email = $booking->contact_email ?: ($booking->user->email ?? null);
        $paid = filled($booking->receipt);
        $reference = strtoupper($type).'-'.$booking->id;

        if ($type === 'spot') {
            $booking->loadMissing('spot');
            $place = $booking->spot->name ?? 'Removed tourist spot';
            $ownerId = $booking->spot->user_id ?? 0;
            $details = [
                'Booking date' => \Carbon\Carbon::parse($booking->booking_date)->format('F d, Y'),
                'Quantity' => (string) $booking->quantity,
            ];
        } elseif ($type === 'hotel') {
            $booking->loadMissing('hotel');
            $place = $booking->hotel->name ?? 'Removed hotel';
            $ownerId = $booking->hotel->owner_id ?? 0;
            $details = [
                'Check in' => \Carbon\Carbon::parse($booking->check_in)->format('F d, Y'),
                'Check out' => \Carbon\Carbon::parse($booking->check_out)->format('F d, Y'),
                'Rooms' => (string) $booking->rooms,
            ];
        } else {
            $booking->loadMissing('food');
            $place = $booking->food->name ?? 'Removed restaurant';
            $ownerId = $booking->food->owner_id ?? 0;
            $details = [
                'Reservation date' => \Carbon\Carbon::parse($booking->reservation_date)->format('F d, Y'),
                'Reservation time' => \Carbon\Carbon::parse($booking->reservation_time)->format('h:i A'),
                'Guests' => (string) $booking->guests,
            ];
        }

        $rows = array_merge([
            'Reference' => $reference,
            'Place' => $place,
            'Guest' => $guest,
            'Email' => $email ?: 'Not provided',
        ], $details, [
            'Total' => 'PHP '.number_format((float) $booking->total_price, 2),
            'Payment' => $this->paymentLine($booking),
            'Arrival code' => $confirmed ? (ArrivalCode::ensure($booking) ?: 'Assigned after confirmation') : 'Assigned after confirmation',
            'Status' => $confirmed ? 'Confirmed by the owner' : 'Pending owner verification',
        ]);

        return [
            'ownerId' => $ownerId,
            'guestName' => $guest,
            'email' => $email,
            'place' => $place,
            'reference' => $reference,
            'rows' => $rows,
            'paid' => $paid,
            'confirmed' => $confirmed,
            'title' => ($confirmed ? 'SpotTour booking confirmed' : 'SpotTour booking request').' '.$reference,
            'subject' => ($confirmed ? 'SpotTour booking confirmed' : 'SpotTour booking received')." — {$place}",
            'statusPhrase' => $confirmed
                ? 'confirmed. The owner checked your payment and approved this reservation.'
                : 'received and waiting for the owner to verify your payment.',
        ];
    }

    private function paymentLine($booking): string
    {
        $label = PaymentChoice::label($booking->payment_method);

        if ($booking->payment_method === 'cash') {
            return 'Cash — pay at the place, waiting for the owner to verify';
        }

        if (PaymentChoice::paidOnline($booking)) {
            return $label.' — paid through PayMongo';
        }

        if (PaymentChoice::hosted($booking->payment_method)) {
            return $label.' — waiting for the guest to finish PayMongo checkout';
        }

        if (filled($booking->receipt)) {
            $reference = filled($booking->payment_reference) ? ' · Ref '.$booking->payment_reference : '';

            return $label.' receipt uploaded for owner verification'.$reference;
        }

        return 'No receipt uploaded';
    }

    private function requirePaymentReceipt($booking)
    {
        if (PaymentChoice::verified($booking)) {
            return null;
        }

        return back()->with('error', 'This payment still needs a receipt and reference number before it can be verified.');
    }

    private function alert(array $data): void
    {
        $data['sender_id'] = $data['sender_id'] ?? Auth::id();
        $data['is_read'] = 0;
        $data['category'] = 'booking';
        Notification::create($data);

        $links = array_filter([
            'spot_id' => $data['spot_id'] ?? null,
            'hotel_id' => $data['hotel_id'] ?? null,
            'food_id' => $data['food_id'] ?? null,
        ], fn ($value) => filled($value));

        BookingNotice::admins($data['message'], $links, [(int) $data['user_id']]);
    }

    private function finishStoredBooking($booking, string $type, int $ownerId, string $place, array $extra)
    {
        if (PaymentChoice::hosted($booking->payment_method)) {
            return $this->startPayMongo($booking, $type, $place);
        }

        $this->alert(array_merge([
            'user_id' => $ownerId,
            'sender_id' => Auth::id(),
            'message' => PaymentChoice::notice(Auth::user()->name, $place, $booking->payment_method),
            'is_read' => 0,
        ], $extra));

        if ((int) Auth::id() !== (int) $ownerId) {
            BookingNotice::send(
                (int) Auth::id(),
                'Booking confirmed for "'.$place.'". It is saved in your bookings and waiting for the owner to approve it.',
                $extra
            );
        }

        $this->mailBooking($booking, $type, false);

        $success = match ($type) {
            'hotel' => 'Hotel reservation submitted. The owner will verify your '.PaymentChoice::label($booking->payment_method).' payment. A PDF was sent to '.($booking->contact_email ?: Auth::user()->email).'.',
            'food' => 'Restaurant reservation submitted. The owner will verify your '.PaymentChoice::label($booking->payment_method).' payment. A PDF was sent to '.($booking->contact_email ?: Auth::user()->email).'.',
            default => 'Booking submitted. The owner will verify your '.PaymentChoice::label($booking->payment_method).' payment. A PDF was sent to '.($booking->contact_email ?: Auth::user()->email).'.',
        };

        return redirect('/user/bookings')->with('success', $success);
    }

    private function rejectCheapWallet(float $total)
    {
        if ($total >= 100) {
            return null;
        }

        return back()->withInput()->withErrors([
            'booking' => 'GCash and Maya need a total of at least ₱100.00. Choose cash or bank transfer, or ask the owner to set a price.',
        ]);
    }

    private function startPayMongo($booking, string $type, string $place)
    {
        try {
            $session = PayMongoCheckout::create(
                $place,
                (int) round(((float) $booking->total_price) * 100),
                $booking->payment_method,
                strtoupper($type).'-'.$booking->id,
                url('/booking/paymongo/return/'.$type.'/'.$booking->id),
                $booking->contact_email ?: Auth::user()->email,
            );
        } catch (\Throwable $e) {
            $retryUrl = $this->paymentRetryUrl($type, $booking);
            $method = PaymentChoice::label($booking->payment_method);
            $message = $e->getMessage();
            if (str_contains(strtolower($message), 'not allowed')) {
                $message = $method.' is not enabled on this SpotTour PayMongo account yet. Choose cash or bank transfer, or ask the admin to turn on '.$method.' in PayMongo.';
            }
            $input = $this->paymentRetryInput($type, $booking);
            $booking->delete();

            return redirect($retryUrl)->withInput($input)->withErrors([
                'booking' => $message,
            ]);
        }

        $booking->update(['payment_reference' => $session['id']]);

        return redirect()->away($session['url']);
    }

    private function paymentRetryUrl(string $type, $booking): string
    {
        return match ($type) {
            'hotel' => '/hotel/payment?'.http_build_query([
                'hotel_id' => $booking->hotel_id,
                'check_in' => $this->dateValue($booking->check_in),
                'check_out' => $this->dateValue($booking->check_out),
                'rooms' => $booking->rooms,
                'hotel_room_id' => $booking->hotel_room_id,
                'package_id' => $booking->package_id,
            ]),
            'food' => '/food/payment?'.http_build_query([
                'food_id' => $booking->food_id,
                'reservation_date' => $this->dateValue($booking->reservation_date),
                'reservation_time' => $booking->reservation_time,
                'guests' => $booking->guests,
                'special_request' => $booking->special_request,
                'package_id' => $booking->package_id,
            ]),
            default => '/booking/payment?'.http_build_query([
                'spot_id' => $booking->spot_id,
                'booking_date' => $this->dateValue($booking->booking_date),
                'quantity' => $booking->quantity,
                'needs_guide' => $booking->needs_guide ? 1 : 0,
                'package_id' => $booking->package_id,
            ]),
        };
    }

    private function paymentRetryInput(string $type, $booking): array
    {
        $shared = [
            'payment_method' => $booking->payment_method,
            'contact_email' => $booking->contact_email,
        ];

        return match ($type) {
            'hotel' => array_merge($shared, [
                'hotel_id' => $booking->hotel_id,
                'check_in' => $this->dateValue($booking->check_in),
                'check_out' => $this->dateValue($booking->check_out),
                'rooms' => $booking->rooms,
                'hotel_room_id' => $booking->hotel_room_id,
                'package_id' => $booking->package_id,
            ]),
            'food' => array_merge($shared, [
                'food_id' => $booking->food_id,
                'reservation_date' => $this->dateValue($booking->reservation_date),
                'reservation_time' => $booking->reservation_time,
                'guests' => $booking->guests,
                'special_request' => $booking->special_request,
                'package_id' => $booking->package_id,
            ]),
            default => array_merge($shared, [
                'spot_id' => $booking->spot_id,
                'booking_date' => $this->dateValue($booking->booking_date),
                'quantity' => $booking->quantity,
                'needs_guide' => $booking->needs_guide ? 1 : 0,
                'package_id' => $booking->package_id,
            ]),
        };
    }

    private function dateValue($value): string
    {
        if ($value instanceof \Carbon\CarbonInterface) {
            return $value->toDateString();
        }

        return (string) $value;
    }

    public function paymongoResult(string $result, string $type, $id)
    {
        abort_unless(in_array($result, ['return', 'cancel'], true), 404);
        abort_unless(in_array($type, ['spot', 'hotel', 'food'], true), 404);
        abort_unless(Auth::check(), 403);

        $booking = $this->findBooking($type, $id);
        abort_unless(Auth::id() === $booking->user_id, 403);

        if ($result === 'cancel') {
            return redirect('/user/bookings')->with('error', 'Payment was not completed. You can book again and choose GCash or Maya.');
        }

        if (! PaymentChoice::paidOnline($booking)) {
            $payment = PayMongoCheckout::payment((string) $booking->payment_reference);

            if (! $payment) {
                return redirect('/user/bookings')->with('error', 'PayMongo has not confirmed this payment yet.');
            }

            $booking->update([
                'payment_reference' => $payment['id'],
                'payer_name' => $booking->payer_name,
                'status' => 'pending_verification',
            ]);

            [$ownerId, $place, $foreignKey, $foreignId] = $this->bookingTarget($type, $booking);

            $this->alert([
                'user_id' => $ownerId,
                'sender_id' => Auth::id(),
                $foreignKey => $foreignId,
                'message' => PaymentChoice::notice(Auth::user()->name, $place, $booking->payment_method),
                'is_read' => 0,
            ]);

            BookingNotice::send(
                (int) Auth::id(),
                'Booking confirmed for "'.$place.'". Your '.PaymentChoice::label($booking->payment_method).' payment was received and is waiting for the owner to approve it.',
                [$foreignKey => $foreignId]
            );

            $this->mailBooking($booking, $type, false);
        }

        [, $place] = $this->bookingTarget($type, $booking);

        return view('user.booking-paid', [
            'booking' => $booking,
            'type' => $type,
            'place' => $place,
            'method' => PaymentChoice::label($booking->payment_method),
            'total' => (float) $booking->total_price,
        ]);
    }

    private function bookingTarget(string $type, $booking): array
    {
        if ($type === 'spot') {
            $booking->loadMissing('spot');

            return [$booking->spot->user_id, $booking->spot->name ?? 'Removed tourist spot', 'spot_id', $booking->spot_id];
        }

        if ($type === 'hotel') {
            $booking->loadMissing('hotel');

            return [$booking->hotel->owner_id, $booking->hotel->name ?? 'Removed hotel', 'hotel_id', $booking->hotel_id];
        }

        $booking->loadMissing('food');

        return [$booking->food->owner_id, $booking->food->name ?? 'Removed restaurant', 'food_id', $booking->food_id];
    }


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
    $booking = Booking::with('spot')->findOrFail($id);
    abort_unless($booking->spot->user_id === Auth::id(), 403);

    if ($response = $this->requirePaymentReceipt($booking)) {
        return $response;
    }

    $booking->update([
        'status' => 'approved'
    ]);

    $this->mailBooking($booking, 'spot', true);

    $this->alert([

    'user_id' => $booking->user_id,

    'sender_id' => Auth::id(),

    'spot_id' => $booking->spot_id,

    'message' => 'Your booking for "'.$booking->spot->name.'" is confirmed. Arrival code: '.ArrivalCode::ensure($booking).'. A PDF copy was sent to your email.',

    'is_read' => 0

]);

    return back()->with(
        'success',
        'Booking approved.'
    );
}

public function reject($id)
{
    $booking = Booking::with('spot')->findOrFail($id);
    abort_unless($booking->spot->user_id === Auth::id(), 403);

    $booking->update([
        'status' => 'rejected'
    ]);

    $this->alert([

        'user_id' => $booking->user_id,

        'sender_id' => Auth::id(),

        'spot_id' => $booking->spot_id,

        'message' =>
            ' Your booking for "' .
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
    $validator = Validator::make($request->all(), array_merge([
        'spot_id' => 'required|exists:spots,id',
        'booking_date' => 'required|date',
        'quantity' => 'required|integer|min:1',
    ], PaymentChoice::rules()));
    PaymentChoice::validateNumbers($request, $validator);
    $validator->validate();

    $spot = Spot::findOrFail(
        $request->spot_id
    );

    DailyCapacity::ensure('spot', $spot, [$request->booking_date]);

    $unitPrice = $spot->booking_type === 'cottage'
        ? $spot->cottage_price
        : $spot->price;

    if ($unitPrice === null || !is_numeric($unitPrice)) {
        return back()->withInput()->withErrors([
            'booking' => 'This spot does not have a booking price yet. Please contact the listing owner.'
        ]);
    }

    $package = BookingExtras::package($request, 'spot_id', $spot->id);
    $total = BookingExtras::addPrice(
        (float) $unitPrice * ($spot->booking_type === 'cottage' ? $request->integer('quantity', 1) : 1),
        $package
    );

    if (PaymentChoice::hosted($request->payment_method) && ($response = $this->rejectCheapWallet($total))) {
        return $response;
    }

    $booking = Booking::create(array_merge([
        'user_id' => Auth::id(),
        'spot_id' => $request->spot_id,
        'booking_date' => $request->booking_date,
        'quantity' => $request->quantity,
        'total_price' => $total,
        'needs_guide' => $request->boolean('needs_guide'),
        'package_id' => $package?->id,
        'package_name' => $package?->name,
    ], PaymentChoice::store($request)));

    return $this->finishStoredBooking($booking, 'spot', $spot->user_id, $spot->name, [
        'spot_id' => $spot->id,
    ]);
}

public function userBookings()
{
    $bookings = Booking::with('spot')
        ->where('user_id', Auth::id())
        ->whereHas('spot')
        ->get();

    $hotelBookings = HotelBooking::with('hotel')
        ->where('user_id', Auth::id())
        ->whereHas('hotel')
        ->get();

    $foodBookings = FoodBooking::with('food')
    ->where('user_id', Auth::id())
    ->whereHas('food')
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
    $request->validate([
        'spot_id' => 'required|exists:spots,id',
        'booking_date' => 'required|date|after_or_equal:today',
        'quantity' => 'nullable|integer|min:1',
    ]);

    $spot = Spot::findOrFail(
        $request->spot_id
    );

    DailyCapacity::ensure('spot', $spot, [$request->booking_date]);

    return view(
        'user.booking-confirm',
        [
            'spot' => $spot,
            'bookingDate' => $request->booking_date,
            'quantity' => $request->integer('quantity', 1),
        ]
    );
}

public function spotBookingPage($id)
{
    $spot = Spot::with('packages')->findOrFail($id);

    return view('user.spot-booking', compact('spot'));
}

public function hotelBookingPage($id)
{
    $hotel = Hotel::with(['rooms', 'packages'])->findOrFail($id);

    return view('user.hotel-booking', compact('hotel'));
}

public function foodBookingPage($id)
{
    $food = Food::with(['packages', 'menus'])->findOrFail($id);

    return view('user.food-booking', compact('food'));
}

public function paymentPage(Request $request)
{
    $spotId = $request->input('spot_id', old('spot_id'));
    if (! $spotId) {
        return redirect('/user')->with('error', 'Your booking details expired. Please start the reservation again.');
    }

    $spot = Spot::with('owner')->findOrFail($spotId);

    return view('user.payment', [
        'spot' => $spot,
        'booking_date' => $request->input('booking_date', old('booking_date')),
        'quantity' => $request->input('quantity', old('quantity')),
    ]);
}

public function calendar()
{
    $ownerId = Auth::id();
    $isAdmin = Auth::user()?->role === 'admin';
    $bookings = Booking::with(
        'user',
        'spot'
    )->when(!$isAdmin, fn($query) => $query->whereHas('spot', fn($spot) => $spot->where('user_id', $ownerId)))->get();

    $hotelBookings = HotelBooking::with(
        'user',
        'hotel'
    )->when(!$isAdmin, fn($query) => $query->whereHas('hotel', fn($hotel) => $hotel->where('owner_id', $ownerId)))->get();

    $foodBookings = FoodBooking::with(
        'user',
        'food'
    )->when(!$isAdmin, fn($query) => $query->whereHas('food', fn($food) => $food->where('owner_id', $ownerId)))->get();

    return view(
        'owner.calendar',
        compact(
            'bookings',
            'hotelBookings',
            'foodBookings'
            ,'isAdmin'
        )
    );
}

public function availability(Request $request)
{
    $request->validate(['type' => 'required|in:spot,hotel,food', 'id' => 'required|integer']);

    return response()->json(DailyCapacity::snapshot($request->type, (int) $request->id));
}

public function cancel($id)
{
    $booking = Booking::with('spot')->findOrFail($id);

    if($booking->user_id != Auth::id())
    {
        abort(403);
    }

    if(!in_array($booking->status, ['pending', 'pending_payment', 'pending_verification'], true))
    {
        return back()->with(
            'success',
            'Only bookings that are still waiting can be cancelled.'
        );
    }

    $booking->update([

        'status' => 'cancelled'

    ]);

    $this->alert([

        'user_id' => $booking->spot->user_id,

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
    $request->validate([
        'hotel_id' => 'required|exists:hotels,id',
        'check_in' => 'required|date|after_or_equal:today',
        'check_out' => 'required|date|after:check_in',
        'rooms' => 'required|integer|min:1',
    ]);

    $hotel = Hotel::with(['rooms', 'packages'])->findOrFail(
        $request->hotel_id
    );

    DailyCapacity::ensure('hotel', $hotel, DailyCapacity::stayDates($request->check_in, $request->check_out));

    return view(
        'user.hotel-booking-confirm',
        compact('hotel')
    );
}

public function hotelStore(Request $request)
{
    $validator = Validator::make($request->all(), array_merge([
        'hotel_id' => 'required|exists:hotels,id',
        'check_in' => 'required|date',
        'check_out' => 'required|date|after:check_in',
        'rooms' => 'required|integer|min:1',
    ], PaymentChoice::rules()));
    PaymentChoice::validateNumbers($request, $validator);
    $validator->validate();

    $hotel = Hotel::findOrFail(
        $request->hotel_id
    );

    DailyCapacity::ensure('hotel', $hotel, DailyCapacity::stayDates($request->check_in, $request->check_out));

    $nights = \Carbon\Carbon::parse($request->check_in)
        ->diffInDays(\Carbon\Carbon::parse($request->check_out));
    $room = BookingExtras::room($request, $hotel->id);
    $package = BookingExtras::package($request, 'hotel_id', $hotel->id);
    $nightly = $room?->price_per_night ?? $hotel->price_per_night;
    $total = BookingExtras::addPrice((float) $nightly * $request->rooms * $nights, $package);

    if (PaymentChoice::hosted($request->payment_method) && ($response = $this->rejectCheapWallet($total))) {
        return $response;
    }

    $booking = HotelBooking::create(array_merge([
        'user_id' => Auth::id(),
        'hotel_id' => $hotel->id,
        'check_in' => $request->check_in,
        'check_out' => $request->check_out,
        'rooms' => $request->rooms,
        'hotel_room_id' => $room?->id,
        'room_name' => $room?->name ?? 'Standard room',
        'package_id' => $package?->id,
        'package_name' => $package?->name,
        'total_price' => $total,
    ], PaymentChoice::store($request)));

    return $this->finishStoredBooking($booking, 'hotel', $hotel->owner_id, $hotel->name, [
        'hotel_id' => $hotel->id,
    ]);
}


public function hotelPaymentPage(Request $request)
{
    $hotelId = $request->input('hotel_id', old('hotel_id'));
    if (! $hotelId) {
        return redirect('/hotels')->with('error', 'Your hotel booking details expired. Please reserve again.');
    }

    $hotel = Hotel::with(['rooms', 'packages', 'owner'])->findOrFail($hotelId);

    return view('user.hotel-payment', [
        'hotel' => $hotel,
        'check_in' => $request->input('check_in', old('check_in')),
        'check_out' => $request->input('check_out', old('check_out')),
        'rooms' => $request->input('rooms', old('rooms')),
    ]);
}


public function approveHotel($id)
{
    $booking = HotelBooking::with('hotel')->findOrFail($id);
    abort_unless($booking->hotel->owner_id === Auth::id(), 403);

    if ($response = $this->requirePaymentReceipt($booking)) {
        return $response;
    }

    $booking->update([
        'status' => 'approved'
    ]);

    $this->mailBooking($booking, 'hotel', true);

    $this->alert([

        'user_id' => $booking->user_id,

        'sender_id' => Auth::id(),

        'hotel_id' => $booking->hotel_id,

        'message' =>
            ' Your hotel reservation for "' .
            $booking->hotel->name .
            '" is confirmed. Arrival code: '.ArrivalCode::ensure($booking).'. A PDF copy was sent to your email.',

        'is_read' => 0

    ]);

    return back()->with(
        'success',
        'Hotel booking approved.'
    );
}

public function rejectHotel($id)
{
    $booking = HotelBooking::with('hotel')->findOrFail($id);
    abort_unless($booking->hotel->owner_id === Auth::id(), 403);

    $booking->update([
        'status' => 'rejected'
    ]);

    $this->alert([

        'user_id' => $booking->user_id,

        'sender_id' => Auth::id(),

        'hotel_id' => $booking->hotel_id,

        'message' =>
            ' Your hotel reservation for "' .
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
    $request->validate([
        'food_id' => 'required|exists:foods,id',
        'reservation_date' => 'required|date|after_or_equal:today',
        'reservation_time' => 'required|date_format:H:i',
        'guests' => 'required|integer|min:1',
        'menu_qty' => 'nullable|array',
        'menu_qty.*' => 'nullable|integer|min:0|max:50',
    ]);

    $food = Food::with(['packages', 'menus'])->findOrFail(
        $request->food_id
    );

    DailyCapacity::ensure('food', $food, [$request->reservation_date]);

    $menus = BookingExtras::menus($request, $food->id);

    return view(
        'user.food-booking-confirm',
        [
            'food' => $food,
            'reservationDate' => $request->reservation_date,
            'reservationTime' => $request->reservation_time,
            'guests' => $request->integer('guests'),
            'specialRequest' => $request->input('special_request'),
            'menus' => $menus,
            'menuTotal' => BookingExtras::menuTotal($menus),
        ]
    );
}

public function foodPaymentPage(Request $request)
{
    $foodId = $request->input('food_id', old('food_id'));
    if (! $foodId) {
        return redirect('/foods')->with('error', 'Your restaurant booking details expired. Please reserve again.');
    }

    $food = Food::with('owner')->findOrFail($foodId);
    $guests = (int) $request->input('guests', old('guests', 1));
    $package = BookingExtras::package($request, 'food_id', $food->id);
    $menus = BookingExtras::menus($request, $food->id);
    $menuTotal = BookingExtras::menuTotal($menus);
    $total = BookingExtras::addPrice((float) $food->price_per_guest * $guests, $package, $menuTotal);

    return view('user.food-payment', [
        'food' => $food,
        'reservation_date' => $request->input('reservation_date', old('reservation_date')),
        'reservation_time' => $request->input('reservation_time', old('reservation_time')),
        'guests' => $guests,
        'special_request' => $request->input('special_request', old('special_request')),
        'menus' => $menus,
        'menuTotal' => $menuTotal,
        'total' => $total,
    ]);
}

public function foodStore(Request $request)
{
    $validator = Validator::make($request->all(), array_merge([
        'food_id' => 'required|exists:foods,id',
        'reservation_date' => 'required|date',
        'reservation_time' => 'required|date_format:H:i',
        'guests' => 'required|integer|min:1',
        'menu_items' => 'nullable|string',
        'menu_qty' => 'nullable|array',
        'menu_qty.*' => 'nullable|integer|min:0|max:50',
    ], PaymentChoice::rules()));
    PaymentChoice::validateNumbers($request, $validator);
    $validator->validate();

    $food = Food::findOrFail(
        $request->food_id
    );

    DailyCapacity::ensure('food', $food, [$request->reservation_date]);

    $package = BookingExtras::package($request, 'food_id', $food->id);
    $menus = BookingExtras::menus($request, $food->id);
    $menuTotal = BookingExtras::menuTotal($menus);
    $total = BookingExtras::addPrice((float) $food->price_per_guest * $request->guests, $package, $menuTotal);

    if (PaymentChoice::hosted($request->payment_method) && ($response = $this->rejectCheapWallet($total))) {
        return $response;
    }

    $booking = FoodBooking::create(array_merge([
        'user_id' => Auth::id(),
        'food_id' => $request->food_id,
        'reservation_date' => $request->reservation_date,
        'reservation_time' => $request->reservation_time,
        'guests' => $request->guests,
        'special_request' => $request->special_request,
        'menu_items' => $menus->all(),
        'package_id' => $package?->id,
        'package_name' => $package?->name,
        'total_price' => $total,
    ], PaymentChoice::store($request)));

    return $this->finishStoredBooking($booking, 'food', $food->owner_id, $food->name, [
        'food_id' => $food->id,
    ]);
}

public function approveFood($id)
{
    $booking = FoodBooking::with('food')->findOrFail($id);
    abort_unless($booking->food->owner_id === Auth::id(), 403);

    if ($response = $this->requirePaymentReceipt($booking)) {
        return $response;
    }

    $booking->update([
        'status' => 'approved'
    ]);

    $this->mailBooking($booking, 'food', true);

    $this->alert([

        'user_id' => $booking->user_id,

        'sender_id' => Auth::id(),

        'food_id' => $booking->food_id,

        'message' =>
            ' Your restaurant reservation for "' .
            $booking->food->name .
            '" is confirmed. Arrival code: '.ArrivalCode::ensure($booking).'. A PDF copy was sent to your email.',

        'is_read' => 0

    ]);

    return back()->with(
        'success',
        'Restaurant booking approved.'
    );
}

public function rejectFood($id)
{
    $booking = FoodBooking::with('food')->findOrFail($id);
    abort_unless($booking->food->owner_id === Auth::id(), 403);

    $booking->update([
        'status' => 'rejected'
    ]);

    $this->alert([

        'user_id' => $booking->user_id,

        'sender_id' => Auth::id(),

        'food_id' => $booking->food_id,

        'message' =>
            ' Your restaurant reservation for "' .
            $booking->food->name .
            '" has been rejected.',

        'is_read' => 0

    ]);

    return back()->with(
        'success',
        'Restaurant booking rejected.'
    );
}

public function cancelHotel($id)
{
    $booking = HotelBooking::with('hotel')->findOrFail($id);
    abort_unless($booking->user_id === Auth::id(), 403);

    if (!in_array($booking->status, ['pending', 'pending_payment', 'pending_verification'], true)) {
        return back()->with('success', 'Only reservations that are still waiting can be cancelled.');
    }

    $booking->update(['status' => 'cancelled']);

    $this->alert([
        'user_id' => $booking->hotel->owner_id,
        'sender_id' => Auth::id(),
        'hotel_id' => $booking->hotel_id,
        'message' => Auth::user()->name.' cancelled a hotel reservation for "'.$booking->hotel->name.'".',
        'is_read' => 0,
    ]);

    return back()->with('success', 'Booking cancelled successfully.');
}

public function cancelFood($id)
{
    $booking = FoodBooking::findOrFail($id);

    if($booking->user_id != Auth::id())
    {
        abort(403);
    }

    if(!in_array($booking->status, ['pending', 'pending_payment', 'pending_verification'], true))
    {
        return back()->with(
            'success',
            'Only reservations that are still waiting can be cancelled.'
        );
    }

    $booking->update([

        'status' => 'cancelled'

    ]);

    $this->alert([

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

    public function arrive(string $type, $id)
    {
        abort_unless(in_array($type, ['spot', 'hotel', 'food'], true), 404);
        $found = $this->ownedArrival($type, $id);
        abort_unless($found !== null, 403);

        return $this->markArrived($found[0], $found[1], $found[2], $found[3], $found[4]);
    }

    public function arriveByCode(Request $request)
    {
        $request->validate(['arrival_code' => 'required|string|max:12']);
        $found = ArrivalCode::find($request->arrival_code);

        if (! $found || $found[2] !== (int) Auth::id()) {
            return back()->with('error', 'That arrival code is not for one of your listings.');
        }

        [$type, $booking, $ownerId, $place] = $found;
        $key = $type === 'spot' ? 'spot_id' : ($type === 'hotel' ? 'hotel_id' : 'food_id');

        return $this->markArrived($type, $booking, $key, $booking->{$key}, $place);
    }

    private function ownedArrival(string $type, $id): ?array
    {
        if ($type === 'spot') {
            $booking = Booking::with('spot')->findOrFail($id);
            if ((int) ($booking->spot->user_id ?? 0) !== (int) Auth::id()) {
                return null;
            }

            return ['spot', $booking, 'spot_id', $booking->spot_id, $booking->spot->name ?? 'Removed tourist spot'];
        }

        if ($type === 'hotel') {
            $booking = HotelBooking::with('hotel')->findOrFail($id);
            if ((int) ($booking->hotel->owner_id ?? 0) !== (int) Auth::id()) {
                return null;
            }

            return ['hotel', $booking, 'hotel_id', $booking->hotel_id, $booking->hotel->name ?? 'Removed hotel'];
        }

        $booking = FoodBooking::with('food')->findOrFail($id);
        if ((int) ($booking->food->owner_id ?? 0) !== (int) Auth::id()) {
            return null;
        }

        return ['food', $booking, 'food_id', $booking->food_id, $booking->food->name ?? 'Removed restaurant'];
    }

    private function markArrived(string $type, $booking, string $foreignKey, $foreignId, ?string $place = null)
    {
        if ($booking->status !== 'approved') {
            return back()->with('error', 'Only a confirmed booking can be checked in.');
        }

        if ($booking->arrived_at) {
            return back()->with('success', 'This guest is already checked in.');
        }

        $booking->update(['arrived_at' => now()]);
        $placeName = $place ?: 'the establishment';

        $this->alert([
            'user_id' => $booking->user_id,
            'sender_id' => Auth::id(),
            $foreignKey => $foreignId,
            'message' => 'You are checked in at "'.$placeName.'".',
            'is_read' => 0,
        ]);

        return back()->with('success', 'Guest checked in at '.$placeName.'.');
    }
}
