<!DOCTYPE html>

<html>
<head>

<title>My Bookings</title>

<link rel="stylesheet" href="{{ asset('css/style.css') }}">
<link rel="stylesheet" href="{{ asset('css/user-dashboard.css') }}">
<link rel="stylesheet" href="{{ asset('css/account-pages.css') }}">

</head>


<body class="dashboard-body account-page">

@include('user.partials.account-bar')

<div class="favorites-container">

    <header class="account-hero">
        <p class="account-kicker">Your trips</p>
        <h1>My Bookings</h1>
        <p>Review spot, hotel, and restaurant reservations, then download a PDF confirmation.</p>
    </header>

    @if(session('success'))

    <div class="toast-success">
        {{ session('success') }}
    </div>

    @endif

    {{-- ================= SPOT BOOKINGS ================= --}}

    <h2 class="section-title">
         Spot Reservations
    </h2>

    @forelse($bookings as $booking)

    <div class="favorite-card">

        <div class="favorite-image">
            @if($booking->spot && $booking->spot->image)
                <img src="{{ asset('uploads/spots/'.$booking->spot->image) }}" alt="Tourist spot">
            @else
                <img src="https://via.placeholder.com/300x200?text=Tourist+Spot" alt="Tourist spot">
            @endif
        </div>

        <div class="booking-details">
            <div class="booking-info"><strong>Tourist spot</strong>{{ optional($booking->spot)->name ?? 'Removed tourist spot' }}</div>
            <div class="booking-info"><strong>Booking date</strong>{{ \Carbon\Carbon::parse($booking->booking_date)->format('F d, Y') }}</div>
            <div class="booking-info"><strong>{{ optional($booking->spot)->booking_type === 'cottage' ? 'Cottages reserved' : 'Guests' }}</strong>{{ $booking->quantity }}</div>
            <div class="booking-info"><strong>Total payment</strong>PHP {{ number_format($booking->total_price, 2) }}</div>
            @if($booking->needs_guide)<div class="booking-info"><strong>Tourist guide</strong>Requested</div>@endif
            @if($booking->package_name)<div class="booking-info"><strong>Package</strong>{{ $booking->package_name }}</div>@endif
            <div class="booking-info"><strong>Payment method</strong>{{ \App\Support\PaymentChoice::label($booking->payment_method) }}</div>
            @include('partials.arrival-code', ['booking' => $booking])
            <div class="booking-info"><strong>Booking email</strong>{{ $booking->contact_email ?? Auth::user()->email }}</div>
        </div>

        <div class="booking-status">
            @if($booking->status == 'pending_payment')
                <span class="badge-pending">Waiting for payment</span>
            @elseif(in_array($booking->status, ['pending', 'pending_verification']))
                <span class="badge-pending">Pending Verification</span>
            @elseif($booking->status == 'approved')
                <span class="badge-approved">Approved</span>
            @elseif($booking->status == 'cancelled')
                <span class="badge-cancelled">Cancelled</span>
            @else
                <span class="badge-rejected">Rejected</span>
            @endif

            <a href="/booking/confirmation/spot/{{ $booking->id }}" class="confirmation-link">Download PDF</a>
            @if(in_array($booking->status, ['pending', 'pending_payment', 'pending_verification']))
                <a href="/booking/cancel/{{ $booking->id }}" class="cancel-booking-btn" onclick="return confirm('Cancel this reservation?')">Cancel</a>
            @endif
        </div>

    </div>

    @empty

    <div class="empty-favorites">

        <h2>No Spot Bookings Yet</h2>

        <p>
            Start exploring spots and make your first booking.
        </p>

    </div>

    @endforelse


    {{-- ================= HOTEL BOOKINGS ================= --}}

    <h2 class="section-title">
         Hotel Reservations
    </h2>

    @forelse($hotelBookings as $booking)

    <div class="favorite-card">

        <div class="favorite-image">

            @if($booking->hotel && $booking->hotel->image)

            <img
            src="{{ asset('uploads/hotels/'.$booking->hotel->image) }}"
            alt="Hotel">

            @else

            <img
            src="https://via.placeholder.com/300x200">

            @endif

        </div>

        <div class="booking-details">

            <div class="booking-info">
                <strong> Hotel</strong>
                {{ optional($booking->hotel)->name ?? 'Removed hotel' }}
            </div>

            <div class="booking-info">
                <strong> Check In</strong>
                {{ \Carbon\Carbon::parse($booking->check_in)->format('F d, Y') }}
            </div>

            <div class="booking-info">
                <strong> Check Out</strong>
                {{ \Carbon\Carbon::parse($booking->check_out)->format('F d, Y') }}
            </div>

            <div class="booking-info">
                <strong> Rooms</strong>
                {{ $booking->rooms }}
            </div>

            <div class="booking-info">
                <strong> Total Payment</strong>
                PHP {{ number_format($booking->total_price,2) }}
            </div>

            @if($booking->room_name)<div class="booking-info"><strong>Room</strong>{{ $booking->room_name }}</div>@endif
            @if($booking->package_name)<div class="booking-info"><strong>Package</strong>{{ $booking->package_name }}</div>@endif
            <div class="booking-info"><strong>Payment method</strong>{{ \App\Support\PaymentChoice::label($booking->payment_method) }}</div>
            @include('partials.arrival-code', ['booking' => $booking])
            <div class="booking-info"><strong>Booking Email</strong>{{ $booking->contact_email ?? Auth::user()->email }}</div>

        </div>

        <div class="booking-status">

            @if($booking->status == 'pending_payment')

            <span class="badge-pending">
                Waiting for payment
            </span>

            @elseif(in_array($booking->status, ['pending', 'pending_verification']))

            <span class="badge-pending">
                Pending Verification
            </span>

            @elseif($booking->status == 'approved')

            <span class="badge-approved">
                Approved
            </span>

            @elseif($booking->status == 'cancelled')

            <span class="badge-cancelled">
                Cancelled
            </span>

            @else

            <span class="badge-rejected">
                Rejected
            </span>

            @endif

            <a href="/booking/confirmation/hotel/{{ $booking->id }}" class="confirmation-link">Download PDF</a>
            @if(in_array($booking->status, ['pending', 'pending_payment', 'pending_verification']))

            <a
            href="/hotel/booking/cancel/{{ $booking->id }}"
            class="cancel-booking-btn"
            onclick="return confirm('Cancel this reservation?')">

                Cancel

            </a>

            @endif

        </div>

    </div>

    @empty

    <div class="empty-favorites">

        <h2>No Hotel Reservations Yet</h2>

    </div>

    @endforelse

    {{-- ================= FOOD BOOKINGS ================= --}}

<h2 class="section-title">

 Restaurant Reservations

</h2>

@forelse($foodBookings as $booking)

<div class="favorite-card">

    <div class="favorite-image">

        @if($booking->food && $booking->food->image)

        <img
        src="{{ asset('uploads/foods/'.$booking->food->image) }}"
        alt="Food">

        @else

        <img
        src="https://via.placeholder.com/300x200">

        @endif

    </div>

    <div class="booking-details">

        <div class="booking-info">

            <strong>

                 Restaurant

            </strong>

            {{ optional($booking->food)->name ?? 'Removed restaurant' }}

        </div>

        <div class="booking-info">

            <strong>

                 Reservation Date

            </strong>

            {{ \Carbon\Carbon::parse($booking->reservation_date)->format('F d, Y') }}

        </div>

        <div class="booking-info">

            <strong>

                 Reservation Time

            </strong>

            {{ \Carbon\Carbon::parse($booking->reservation_time)->format('h:i A') }}

        </div>

        <div class="booking-info">

            <strong>

                 Guests

            </strong>

            {{ $booking->guests }}

        </div>

        @if($booking->special_request)

        <div class="booking-info">

            <strong>

                 Special Request

            </strong>

            {{ $booking->special_request }}

        </div>

        @endif

        @if(!empty($booking->menu_items))
            <div class="booking-info">
                <strong>Menu items</strong>
                {{ collect($booking->menu_items)->map(fn ($item) => ($item['name'] ?? 'Item').' × '.($item['qty'] ?? 0))->implode(', ') }}
            </div>
        @endif

        @if($booking->package_name)<div class="booking-info"><strong>Package</strong>{{ $booking->package_name }}</div>@endif
        <div class="booking-info"><strong>Payment method</strong>{{ \App\Support\PaymentChoice::label($booking->payment_method) }}</div>
            @include('partials.arrival-code', ['booking' => $booking])
        <div class="booking-info"><strong>Booking Email</strong>{{ $booking->contact_email ?? Auth::user()->email }}</div>

    </div>

    <div class="booking-status">

        @if($booking->status == 'pending_payment')

        <span class="badge-pending">

            Waiting for payment

        </span>

        @elseif(in_array($booking->status, ['pending', 'pending_verification']))

        <span class="badge-pending">

            Pending Verification

        </span>

        @elseif($booking->status == 'approved')

        <span class="badge-approved">

            Approved

        </span>

        @elseif($booking->status == 'cancelled')

        <span class="badge-cancelled">

            Cancelled

        </span>

        @else

        <span class="badge-rejected">

            Rejected

        </span>

        @endif

        <a href="/booking/confirmation/food/{{ $booking->id }}" class="confirmation-link">Download PDF</a>
        @if(in_array($booking->status, ['pending', 'pending_payment', 'pending_verification']))

        <a
        href="/food/booking/cancel/{{ $booking->id }}"
        class="cancel-booking-btn"
        onclick="return confirm('Cancel this reservation?')">

            Cancel

        </a>

        @endif

    </div>

</div>

@empty

<div class="empty-favorites">

    <h2>

        No Restaurant Reservations Yet

    </h2>

    <p>

        Reserve a table to see your bookings here.

    </p>

</div>

@endforelse

</div>



</body>
</html>
