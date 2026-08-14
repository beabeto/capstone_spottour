<!DOCTYPE html>

<html>
<head>

<title>Booking Monitoring</title>

<link rel="stylesheet"
href="{{ asset('css/style.css') }}">

</head>

<body class="dashboard-body">

<div class="back-btn">
    <button onclick="history.back()">
        ←
    </button>
</div>

<div class="page-container">

<h2 class="section-title">

📍 Spot Bookings

</h2>

@forelse($spotBookings as $booking)

<div class="admin-booking-card">

    <div class="booking-header">

        <h2>

            🏖 {{ $booking->spot->name ?? 'Deleted Spot' }}

        </h2>

        @if($booking->status == 'approved')

            <span class="status-approved">
                Approved
            </span>

        @elseif($booking->status == 'pending')

            <span class="status-pending">
                Pending
            </span>

        @elseif($booking->status == 'cancelled')

            <span class="status-cancelled">
                Cancelled
            </span>

        @else

            <span class="status-rejected">
                Rejected
            </span>

        @endif

    </div>

    <div class="booking-grid">

        <div>

            <strong>👤 User</strong>

            <p>{{ $booking->user->name }}</p>

        </div>

        <div>

            <strong>📅 Booking Date</strong>

            <p>{{ \Carbon\Carbon::parse($booking->booking_date)->format('F d, Y') }}</p>

        </div>

        <div>

            <strong>🎫 Quantity</strong>

            <p>{{ $booking->quantity }}</p>

        </div>

        <div>

            <strong>💰 Total</strong>

            <p>₱{{ number_format($booking->total_price,2) }}</p>

        </div>

        @if($booking->payer_name)

        <div>

            <strong>👤 Payer</strong>

            <p>{{ $booking->payer_name }}</p>

        </div>

        @endif

        @if($booking->payer_number)

        <div>

            <strong>📱 Contact</strong>

            <p>{{ $booking->payer_number }}</p>

        </div>

        @endif

        @if($booking->receipt)

        <div>

            <strong>📄 Receipt</strong>

            <p>

                <a
                href="{{ asset('uploads/receipts/'.$booking->receipt) }}"
                target="_blank"
                class="view-btn">

                    View Receipt

                </a>

            </p>

        </div>

        @endif

    </div>

</div>

@empty

<div class="empty-card">

    No Spot Bookings Found.

</div>

@endforelse

<h2 class="section-title">

🏨 Hotel Bookings

</h2>

@forelse($hotelBookings as $booking)

<div class="admin-booking-card">

    <div class="booking-header">

        <h2>

            🏨 {{ $booking->hotel->name ?? 'Deleted Hotel' }}

        </h2>

        @if($booking->status == 'approved')

            <span class="status-approved">
                Approved
            </span>

        @elseif($booking->status == 'pending')

            <span class="status-pending">
                Pending
            </span>

        @elseif($booking->status == 'cancelled')

            <span class="status-cancelled">
                Cancelled
            </span>

        @else

            <span class="status-rejected">
                Rejected
            </span>

        @endif

    </div>

    <div class="booking-grid">

        <div>

            <strong>👤 User</strong>

            <p>{{ $booking->user->name }}</p>

        </div>

        <div>

            <strong>📅 Check In</strong>

            <p>{{ \Carbon\Carbon::parse($booking->check_in)->format('F d, Y') }}</p>

        </div>

        <div>

            <strong>📅 Check Out</strong>

            <p>{{ \Carbon\Carbon::parse($booking->check_out)->format('F d, Y') }}</p>

        </div>

        <div>

            <strong>🚪 Rooms</strong>

            <p>{{ $booking->rooms }}</p>

        </div>

        <div>

            <strong>💰 Total Price</strong>

            <p>₱{{ number_format($booking->total_price,2) }}</p>

        </div>

        @if($booking->payer_name)

        <div>

            <strong>👤 Payer</strong>

            <p>{{ $booking->payer_name }}</p>

        </div>

        @endif

        @if($booking->payer_number)

        <div>

            <strong>📱 Contact</strong>

            <p>{{ $booking->payer_number }}</p>

        </div>

        @endif

        @if($booking->receipt)

        <div>

            <strong>📄 Receipt</strong>

            <p>

                <a
                href="{{ asset('uploads/receipts/'.$booking->receipt) }}"
                target="_blank"
                class="view-btn">

                    View Receipt

                </a>

            </p>

        </div>

        @endif

    </div>

</div>

@empty

<div class="empty-card">

    No Hotel Bookings Found.

</div>

@endforelse

<h2 class="section-title">

🍽 Restaurant Bookings

</h2>

@forelse($foodBookings as $booking)

<div class="admin-booking-card">

    <div class="booking-header">

        <h2>

            🍽 {{ $booking->food->name ?? 'Deleted Restaurant' }}

        </h2>

        @if($booking->status == 'approved')

            <span class="status-approved">

                Approved

            </span>

        @elseif($booking->status == 'pending')

            <span class="status-pending">

                Pending

            </span>

        @elseif($booking->status == 'cancelled')

            <span class="status-cancelled">

                Cancelled

            </span>

        @else

            <span class="status-rejected">

                Rejected

            </span>

        @endif

    </div>

    <div class="booking-grid">

        <div>

            <strong>👤 User</strong>

            <p>{{ $booking->user->name }}</p>

        </div>

        <div>

            <strong>📅 Reservation Date</strong>

            <p>

                {{ \Carbon\Carbon::parse($booking->reservation_date)->format('F d, Y') }}

            </p>

        </div>

        <div>

            <strong>🕒 Reservation Time</strong>

            <p>

                {{ \Carbon\Carbon::parse($booking->reservation_time)->format('h:i A') }}

            </p>

        </div>

        <div>

            <strong>👥 Guests</strong>

            <p>

                {{ $booking->guests }}

            </p>

        </div>

        <div>

            <strong>💰 Total Payment</strong>

            <p>

                ₱{{ number_format($booking->total_price,2) }}

            </p>

        </div>

        @if($booking->special_request)

        <div>

            <strong>📝 Special Request</strong>

            <p>

                {{ $booking->special_request }}

            </p>

        </div>

        @endif

        @if($booking->payer_name)

        <div>

            <strong>👤 Payer</strong>

            <p>

                {{ $booking->payer_name }}

            </p>

        </div>

        @endif

        @if($booking->payer_number)

        <div>

            <strong>📱 Contact</strong>

            <p>

                {{ $booking->payer_number }}

            </p>

        </div>

        @endif

        @if($booking->receipt)

        <div>

            <strong>📄 Receipt</strong>

            <p>

                <a
                href="{{ asset('uploads/receipts/'.$booking->receipt) }}"
                target="_blank"
                class="view-btn">

                    View Receipt

                </a>

            </p>

        </div>

        @endif

    </div>

</div>

@empty

<div class="empty-card">

    No Restaurant Bookings Found.

</div>

@endforelse

</div>

</body>
</html>
