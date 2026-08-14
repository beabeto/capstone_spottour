<!DOCTYPE html>

<html>
<head>

<title>Owner Bookings</title>

<link rel="stylesheet"
href="{{ asset('css/style.css') }}">

</head>


<body class="dashboard-body">

<div class="back-btn">
    <button onclick="history.back()">
        ←
    </button>
</div>

<div class="notifications-container">

    <h1>
        📅 Booking Requests
    </h1>

    {{-- SPOT BOOKINGS --}}

    <div class="booking-section">

        <h2 class="section-title">
            📍 Spot Bookings
        </h2>

        @forelse($spotBookings as $booking)

<div class="booking-card">

    <div class="booking-top">

        <div class="booking-user">

            <img
            src="{{ $booking->user->profile_image
                ? asset('uploads/profile/'.$booking->user->profile_image)
                : 'https://cdn-icons-png.flaticon.com/512/3135/3135715.png' }}"
            class="booking-avatar">

            <div>

                <h3>

                    {{ $booking->user->name }}

                </h3>

                <small>

                    Spot Booking

                </small>

            </div>

        </div>

        <div>

            @if($booking->status == 'pending_payment' || $booking->status == 'pending_verification')

                <span class="status pending">

                    Pending

                </span>

            @elseif($booking->status == 'approved')

                <span class="status approved">

                    Approved

                </span>

            @elseif($booking->status == 'completed')

                <span class="status approved">

                    Completed

                </span>

            @else

                <span class="status rejected">

                    Rejected

                </span>

            @endif

        </div>

    </div>

    <div class="booking-details">

        <p>

            📍 <strong>{{ $booking->spot->name }}</strong>

        </p>

        <p>

            📅 Booking Date:

            <strong>

                {{ \Carbon\Carbon::parse($booking->booking_date)->format('F d, Y') }}

            </strong>

        </p>

        <p>

            🏡 Quantity:

            <strong>

                {{ $booking->quantity }}

            </strong>

        </p>

        <p>

            💰 Total Payment:

            <strong>

                ₱{{ number_format($booking->total_price,2) }}

            </strong>

        </p>

        @if($booking->payer_name)

        <p>

            👤 Payer:

            <strong>

                {{ $booking->payer_name }}

            </strong>

        </p>

        @endif

        @if($booking->payer_number)

        <p>

            📱 GCash Number:

            <strong>

                {{ $booking->payer_number }}

            </strong>

        </p>

        @endif

        @if($booking->receipt)

        <a
        href="{{ asset('uploads/receipts/'.$booking->receipt) }}"
        target="_blank"
        class="view-btn">

            📄 View Payment Receipt

        </a>

        @endif

    </div>

    @if($booking->status == 'pending_payment' || $booking->status == 'pending_verification')

    <div class="booking-actions">

        <a
        href="/owner/bookings/approve/{{ $booking->id }}"
        class="approve-btn">

            ✅ Approve

        </a>

        <a
        href="/owner/bookings/reject/{{ $booking->id }}"
        class="reject-btn">

            ❌ Reject

        </a>

    </div>

    @endif

</div>

@empty

<div class="empty-state">

    No Spot Bookings Found.

</div>

@endforelse

    </div>


    {{-- HOTEL BOOKINGS --}}

    <div class="booking-section">

        <h2 class="section-title">
            🏨 Hotel Bookings
        </h2>

        @forelse($hotelBookings as $booking)

        <div class="booking-card">

            <div class="booking-top">

                <div class="booking-user">

                    <img
                    src="{{ $booking->user->profile_image
                        ? asset('uploads/profile/'.$booking->user->profile_image)
                        : 'https://cdn-icons-png.flaticon.com/512/3135/3135715.png' }}"
                    class="booking-avatar">

                    <div>

                        <h3>
                            {{ $booking->user->name }}
                        </h3>

                        <small>
                            Hotel Reservation
                        </small>

                    </div>

                </div>

                <div>

                    @if($booking->status == 'pending')

                        <span class="status pending">
                            Pending
                        </span>

                    @elseif($booking->status == 'approved')

                        <span class="status approved">
                            Approved
                        </span>

                    @elseif($booking->status == 'cancelled')

                        <span class="status cancelled">
                            Cancelled
                        </span>

                    @else

                        <span class="status rejected">
                            Rejected
                        </span>

                    @endif

                </div>

            </div>

            <div class="booking-details">

    <p>
        🏨 <strong>{{ $booking->hotel->name }}</strong>
    </p>

    <p>
        📅 Check In:
        <strong>
            {{ \Carbon\Carbon::parse($booking->check_in)->format('F d, Y') }}
        </strong>
    </p>

    <p>
        📅 Check Out:
        <strong>
            {{ \Carbon\Carbon::parse($booking->check_out)->format('F d, Y') }}
        </strong>
    </p>

    <p>
        🚪 Rooms:
        <strong>{{ $booking->rooms }}</strong>
    </p>

    <p>
        💰 Total Payment:
        <strong>
            ₱{{ number_format($booking->total_price,2) }}
        </strong>
    </p>

    <hr>

    @if($booking->payer_name)

    <p>
        👤 Payer:
        <strong>{{ $booking->payer_name }}</strong>
    </p>

    @endif

    @if($booking->payer_number)

    <p>
        📱 GCash Number:
        <strong>{{ $booking->payer_number }}</strong>
    </p>

    @endif

    @if($booking->receipt)

    <a
    href="{{ asset('uploads/receipts/'.$booking->receipt) }}"
    target="_blank"
    class="view-btn">

        📄 View Payment Receipt

    </a>

    @endif

</div>

            

           @if($booking->status == 'pending')

<div class="booking-actions">

    <a
    href="/owner/hotel-bookings/approve/{{ $booking->id }}"
    class="approve-btn">

        ✅ Approve

    </a>

    <a
    href="/owner/hotel-bookings/reject/{{ $booking->id }}"
    class="reject-btn">

        ❌ Reject

    </a>

</div>

@endif

        </div>

        @empty

        <div class="empty-state">

            No Hotel Bookings Found.

        </div>

        @endforelse

    </div>

    {{-- ================= RESTAURANT BOOKINGS ================= --}}

<div class="booking-section">

    <h2 class="section-title">
        🍽 Restaurant Bookings
    </h2>

    @forelse($foodBookings as $booking)

    <div class="booking-card">

        <div class="booking-top">

            <div class="booking-user">

                <img
                src="{{ $booking->user->profile_image
                    ? asset('uploads/profile/'.$booking->user->profile_image)
                    : 'https://cdn-icons-png.flaticon.com/512/3135/3135715.png' }}"
                class="booking-avatar">

                <div>

                    <h3>

                        {{ $booking->user->name }}

                    </h3>

                    <small>

                        Restaurant Reservation

                    </small>

                </div>

            </div>

            <div>

                @if($booking->status == 'pending')

                    <span class="status pending">
                        Pending
                    </span>

                @elseif($booking->status == 'approved')

                    <span class="status approved">
                        Approved
                    </span>

                @elseif($booking->status == 'cancelled')

                    <span class="status cancelled">
                        Cancelled
                    </span>

                @else

                    <span class="status rejected">
                        Rejected
                    </span>

                @endif

            </div>

        </div>

        <div class="booking-details">

    <p>
        🍽 <strong>{{ $booking->food->name }}</strong>
    </p>

    <p>
        📅 Reservation Date:
        <strong>
            {{ \Carbon\Carbon::parse($booking->reservation_date)->format('F d, Y') }}
        </strong>
    </p>

    <p>
        🕒 Reservation Time:
        <strong>
            {{ \Carbon\Carbon::parse($booking->reservation_time)->format('h:i A') }}
        </strong>
    </p>

   <p>
    💵 Price Per Guest:
    <strong>
        ₱{{ number_format($booking->food->price_per_guest, 2) }}
    </strong>
</p>

<p>
    👥 Guests:
    <strong>{{ $booking->guests }}</strong>
</p>

<p>
    💰 Total Payment:
    <strong>
        ₱{{ number_format($booking->total_price, 2) }}
    </strong>
</p>

    @if($booking->special_request)

    <p>
        📝 Special Request:
        <strong>{{ $booking->special_request }}</strong>
    </p>

    @endif

    <hr>

    @if($booking->payer_name)

    <p>
        👤 Payer:
        <strong>{{ $booking->payer_name }}</strong>
    </p>

    @endif

    @if($booking->payer_number)

    <p>
        📱 GCash Number:
        <strong>{{ $booking->payer_number }}</strong>
    </p>

    @endif

    @if($booking->receipt)

    <a
    href="{{ asset('uploads/receipts/'.$booking->receipt) }}"
    target="_blank"
    class="view-btn">

        📄 View Payment Receipt

    </a>

    @endif

</div>

        @if($booking->status == 'pending')

<div class="booking-actions">

    <a
    href="/owner/food-bookings/approve/{{ $booking->id }}"
    class="approve-btn">

        ✅ Approve

    </a>

    <a
    href="/owner/food-bookings/reject/{{ $booking->id }}"
    class="reject-btn">

        ❌ Reject

    </a>

</div>

@endif

    </div>

    @empty

    <div class="empty-state">

        No Restaurant Bookings Found.

    </div>

    @endforelse

</div>

</div>

</body>


</html>