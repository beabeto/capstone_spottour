<!DOCTYPE html>

<html>
<head>

<title>My Bookings</title>

<link rel="stylesheet"
href="{{ asset('css/style.css') }}">

</head>


<body class="dashboard-body">

<div class="back-btn">
    <button onclick="history.back()">
        ←
    </button>
</div>

<div class="favorites-container">

    <h1>
        📅 My Bookings
    </h1>

    @if(session('success'))

    <div class="toast-success">
        {{ session('success') }}
    </div>

    @endif

    {{-- ================= SPOT BOOKINGS ================= --}}

    <h2 class="section-title">
        📍 Spot Reservations
    </h2>

    @forelse($bookings as $booking)

    <div class="favorite-card">

        {{-- Your existing Spot Booking Card Here --}}

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
        🏨 Hotel Reservations
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
                <strong>🏨 Hotel</strong>
                {{ $booking->hotel->name }}
            </div>

            <div class="booking-info">
                <strong>📅 Check In</strong>
                {{ \Carbon\Carbon::parse($booking->check_in)->format('F d, Y') }}
            </div>

            <div class="booking-info">
                <strong>📅 Check Out</strong>
                {{ \Carbon\Carbon::parse($booking->check_out)->format('F d, Y') }}
            </div>

            <div class="booking-info">
                <strong>🚪 Rooms</strong>
                {{ $booking->rooms }}
            </div>

            <div class="booking-info">
                <strong>💰 Total Payment</strong>
                ₱{{ number_format($booking->total_price,2) }}
            </div>

        </div>

        <div class="booking-status">

            @if($booking->status == 'pending')

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

            @if($booking->status == 'pending')

            <a
            href="/hotel/booking/cancel/{{ $booking->id }}"
            class="cancel-booking-btn"
            onclick="return confirm('Cancel this reservation?')">

                Cancel Reservation

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

🍽 Restaurant Reservations

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

                🍽 Restaurant

            </strong>

            {{ $booking->food->name }}

        </div>

        <div class="booking-info">

            <strong>

                📅 Reservation Date

            </strong>

            {{ \Carbon\Carbon::parse($booking->reservation_date)->format('F d, Y') }}

        </div>

        <div class="booking-info">

            <strong>

                🕒 Reservation Time

            </strong>

            {{ \Carbon\Carbon::parse($booking->reservation_time)->format('h:i A') }}

        </div>

        <div class="booking-info">

            <strong>

                👥 Guests

            </strong>

            {{ $booking->guests }}

        </div>

        @if($booking->special_request)

        <div class="booking-info">

            <strong>

                📝 Special Request

            </strong>

            {{ $booking->special_request }}

        </div>

        @endif

    </div>

    <div class="booking-status">

        @if($booking->status == 'pending')

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

        @if($booking->status == 'pending')

        <a
        href="/food/booking/cancel/{{ $booking->id }}"
        class="cancel-booking-btn"
        onclick="return confirm('Cancel this reservation?')">

            Cancel Reservation

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
```


</html>