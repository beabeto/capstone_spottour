<!DOCTYPE html>
<html>
<head>

<title>Reservation Confirmation</title>

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

         Reservation Confirmation

    </h1>

    @if($food->image)

    <img
    src="{{ asset('uploads/foods/'.$food->image) }}"
    class="confirm-image">

    @endif

    <div class="spot-info">

        <h2>

            {{ $food->name }}

        </h2>

        <p>

             {{ $food->location }}

        </p>

        <p>

            {{ $food->description }}

        </p>

    </div>

    <div class="booking-summary">

        <h3>

            Reservation Details

        </h3>

        <p>

            <strong>Reservation Date:</strong>

            {{ \Carbon\Carbon::parse($reservationDate)->format('F d, Y') }}

        </p>

        <p>

            <strong>Reservation Time:</strong>

            {{ \Carbon\Carbon::parse($reservationTime)->format('h:i A') }}

        </p>

        <p>

            <strong>Guests:</strong>

            {{ $guests }}

        </p>

        @if($specialRequest)
        <p><strong>Special Request:</strong> {{ $specialRequest }}</p>
        @endif

        <p>

            <strong>Available Tables:</strong>

            {{ $food->available_tables }}

        </p>

        <p>

            <strong>Venue Capacity:</strong>

            {{ $food->venue_capacity }}

        </p>

    </div>

    <form
    method="GET"
    action="/food/payment">

        <input
        type="hidden"
        name="food_id"
        value="{{ $food->id }}">

        <input type="hidden" name="reservation_date" value="{{ $reservationDate }}">
        <input type="hidden" name="reservation_time" value="{{ $reservationTime }}">
        <input type="hidden" name="guests" value="{{ $guests }}">
        <input type="hidden" name="special_request" value="{{ $specialRequest }}">
        <input type="hidden" name="package_id" value="{{ request('package_id') }}">

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
