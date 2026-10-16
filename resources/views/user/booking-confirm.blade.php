<!DOCTYPE html>

<html>
<head>

<title>Booking Confirmation</title>

<link rel="stylesheet"
href="{{ asset('css/style.css') }}">
<link rel="stylesheet" href="{{ asset('css/listing-details.css') }}">

</head>

<body class="dashboard-body">

<div class="back-btn">
    <button type="button" onclick="window.history.back(); return false;">Back</button>
</div>

<div class="booking-confirm-container">

<div class="booking-confirm-card">

    <h1>

         Booking Confirmation

    </h1>

    @if($spot->image)

    <img
    src="{{ asset('uploads/spots/'.$spot->image) }}"
    class="confirm-image">

    @endif

    <div class="spot-info">

        <h2>

            {{ $spot->name }}

        </h2>

        <p>

             {{ $spot->location }}

        </p>

        <p>

            {{ $spot->description }}

        </p>

    </div>

    <div class="booking-summary">

        <h3>

            Reservation Details

        </h3>

        <p>

            <strong>Booking Type:</strong>

            {{ ucfirst($spot->booking_type) }}

        </p>

        <p><strong>Booking Date:</strong> {{ \Carbon\Carbon::parse($bookingDate)->format('F d, Y') }}</p>

        <p>

            <strong>Entrance Fee:</strong>

            PHP {{ number_format(
                $spot->price,
                2
            ) }}

        </p>

        @if($spot->booking_type == 'cottage')

        <p>

            <strong>Cottage Price:</strong>

            PHP {{ number_format(
                $spot->cottage_price,
                2
            ) }}

        </p>

        <p><strong>Cottages Reserved:</strong> {{ $quantity }}</p>

        @endif

    </div>

    <form method="GET" action="/booking/payment">
        <input type="hidden" name="spot_id" value="{{ $spot->id }}">
        <input type="hidden" name="booking_date" value="{{ $bookingDate }}">
        <input type="hidden" name="quantity" value="{{ $quantity }}">
        <input type="hidden" name="needs_guide" value="{{ request('needs_guide') ? 1 : 0 }}">
        <input type="hidden" name="package_id" value="{{ request('package_id') }}">

        @if($spot->booking_type == 'cottage')
        <div class="total-box">

            Estimated Total:

            <span id="totalPrice">

                PHP {{ number_format($spot->cottage_price * $quantity, 2) }}

            </span>

        </div>

        @endif

        <button
        type="submit"
        class="payment-btn">

             Proceed To Payment

        </button>

    </form>

</div>

</div>

</body>

</html>
