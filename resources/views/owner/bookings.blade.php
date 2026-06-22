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

@forelse($bookings as $booking)

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

                    Booking Request

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

            @else

                <span class="status rejected">

                    Rejected

                </span>

            @endif

        </div>

    </div>

    <div class="booking-details">

        <p>

            📍 Spot:

            <strong>

                {{ $booking->spot->name }}

            </strong>

        </p>

        <p>

            📅 Booking Date:

            <strong>

                {{ \Carbon\Carbon::parse(
                    $booking->booking_date
                )->format('F d, Y') }}

            </strong>

        </p>

        <p>

            🏠 Quantity:

            <strong>

                {{ $booking->quantity }}

            </strong>

        </p>

        <p>

            💰 Total Payment:

            <strong>

                ₱{{ number_format(
                    $booking->total_price,
                    2
                ) }}

            </strong>

        </p>

        <p>

            📱 Payer Name:

            <strong>

                {{ $booking->payer_name }}

            </strong>

        </p>

        <p>

            ☎️ Payer Number:

            <strong>

                {{ $booking->payer_number }}

            </strong>

        </p>

        @if($booking->reference_number)

        <p>

            🎫 Reference:

            <strong>

                {{ $booking->reference_number }}

            </strong>

        </p>

        @endif

    </div>

    @if($booking->receipt)

    <div class="receipt-preview">

        <h4>

            Payment Receipt

        </h4>

        <a
        href="{{ asset('uploads/receipts/'.$booking->receipt) }}"
        target="_blank">

            <img
            src="{{ asset('uploads/receipts/'.$booking->receipt) }}"
            class="receipt-image">

        </a>

    </div>

    @endif

    @if($booking->status == 'pending')

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

@if($booking->status == 'cancelled')

<span class="status-cancelled">

    Cancelled By User

</span>

@endif

</div>

@empty

<div class="empty-state">

    No booking requests found.

</div>

@endforelse

</div>

</body>
</html>