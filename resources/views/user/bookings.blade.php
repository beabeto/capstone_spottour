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

@forelse($bookings as $booking)

<div class="favorite-card">

    <div class="favorite-image">

        @if($booking->spot && $booking->spot->image)

        <img
        src="{{ asset('uploads/spots/'.$booking->spot->image) }}"
        alt="Spot">

        @else

        <img
        src="https://via.placeholder.com/300x200">

        @endif

    </div>

    <div class="booking-details">

    <div class="booking-info">
        <strong>📅 Booking Date</strong>
        {{ \Carbon\Carbon::parse($booking->booking_date)->format('F d, Y') }}
    </div>

    <div class="booking-info">
        <strong>🏠 Quantity</strong>
        {{ $booking->quantity }}
    </div>

    <div class="booking-info">
        <strong>💰 Total Payment</strong>
        ₱{{ number_format($booking->total_price,2) }}
    </div>

    <div class="booking-info">
        <strong>🔖 Reference No.</strong>
        {{ $booking->reference_no ?? 'N/A' }}
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
href="/booking/cancel/{{ $booking->id }}"
class="cancel-booking-btn"
onclick="return confirm(
'Cancel this booking?'
)">

     Cancel Booking

</a>

@endif

        </div>

        @if($booking->receipt)

        <div class="receipt-preview">

            <p>

                Payment Receipt

            </p>

            <img
            src="{{ asset('uploads/receipts/'.$booking->receipt) }}"
            class="receipt-image">

        </div>

        @endif

        @if($booking->payer_name)

        <div class="payment-details">

            <p>

                👤 Payer:

                {{ $booking->payer_name }}

            </p>

            <p>

                📱 GCash:

                {{ $booking->payer_number }}

            </p>

        </div>

        @endif

    </div>

</div>

@empty

<div class="empty-favorites">

    <h2>

        No Bookings Yet

    </h2>

    <p>

        Start exploring spots and make your first booking.

    </p>

</div>

@endforelse

</div>

</body>

</html>