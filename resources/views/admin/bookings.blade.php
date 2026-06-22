<!DOCTYPE html>

<html>
<head>

<title> Booking Monitoring</title>

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

<br>
<h1>

        Booking Monitoring

</h1>
</br>
<div class="booking-stats">


<div class="stat-card">

    <h2>

       {{ $totalBookings }}

    </h2>

    <p>

        Total Bookings

    </p>

</div>

<div class="stat-card approved">

    <h2>

       {{ $approvedBookings }}

    </h2>

    <p>

        Approved

    </p>

</div>

<div class="stat-card pending">

    <h2>

        {{ $pendingBookings }}

    </h2>

    <p>

        Pending

    </p>

</div>

<div class="stat-card rejected">

    <h2>

        {{ $rejectedBookings }}

    </h2>

    <p>

        Rejected

    </p>

</div>

<div class="stat-card cancelled">

    <h2>

        {{ $cancelledBookings }}
    </h2>

    <p>

        Cancelled

    </p>

</div>


</div>

<br>

@forelse($bookings as $booking)

<div class="admin-booking-card">

```
<div class="booking-header">

    <div>

        <h2>

            🏖️ {{ $booking->spot->name ?? 'Deleted Spot' }}

        </h2>

       

    </div>

    <div>

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

</div>

<div class="booking-grid">

    <div>

        <strong>

            👤 User

        </strong>

        <p>

            {{ $booking->user->name }}

        </p>

    </div>

    <div>

        <strong>

            📅 Booking Date

        </strong>

        <p>

            {{ \Carbon\Carbon::parse($booking->booking_date)->format('F d, Y') }}

        </p>

    </div>

    <div>

        <strong>

            🏠 Quantity

        </strong>

        <p>

            {{ $booking->quantity }}

        </p>

    </div>

    <div>

        <strong>

            💰 Total

        </strong>

        <p>

            ₱{{ number_format($booking->total_price,2) }}

        </p>

    </div>

    <div>

        <strong>

            👤 Payer Name

        </strong>

        <p>

            {{ $booking->payer_name ?? 'N/A' }}

        </p>

    </div>

    <div>

        <strong>

            📱 GCash Number

        </strong>

        <p>

            {{ $booking->payer_number ?? 'N/A' }}

        </p>

    </div>

</div>

@if($booking->receipt)

<div class="receipt-section">

    <strong>

        🧾 Payment Receipt

    </strong>

    <br><br>

    <a
    href="{{ asset('uploads/receipts/'.$booking->receipt) }}"
    target="_blank">

        <img
        src="{{ asset('uploads/receipts/'.$booking->receipt) }}"
        class="admin-receipt">

    </a>

</div>

@endif
```

</div>

<br>

@empty

<div class="empty-card">

```
No bookings found.
```

</div>

@endforelse

</div>

</body>

</html>
