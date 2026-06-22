
<!DOCTYPE html>

<html>
<head>

<title>Hotel Reservation Confirmation</title>

<link rel="stylesheet"
href="{{ asset('css/style.css') }}">

</head>

<body class="dashboard-body">

<div class="back-btn">

    <button onclick="history.back()">

        ←

    </button>

</div>

<div class="booking-confirm-container">

<div class="booking-confirm-card">

    <h1>

        🏨 Hotel Reservation Confirmation

    </h1>

    @if($hotel->image)

    <img
    src="{{ asset('uploads/hotels/'.$hotel->image) }}"
    class="confirm-image">

    @endif

    <div class="spot-info">

        <h2>

            {{ $hotel->name }}

        </h2>

        <p>

            📍 {{ $hotel->location }}

        </p>

        <p>

            {{ $hotel->description }}

        </p>

    </div>

    <div class="booking-summary">

        <h3>

            Reservation Details

        </h3>

        <p>

            <strong>Check In:</strong>

            {{ request('check_in') }}

        </p>

        <p>

            <strong>Check Out:</strong>

            {{ request('check_out') }}

        </p>

        <p>

            <strong>Rooms:</strong>

            {{ request('rooms') }}

        </p>

        <p>

            <strong>Available Rooms:</strong>

            {{ $hotel->available_rooms }}

        </p>

        <p>

            <strong>Price Per Night:</strong>

            ₱{{ number_format(
                $hotel->price_per_night,
                2
            ) }}

        </p>

        @php

            $checkIn = \Carbon\Carbon::parse(
                request('check_in')
            );

            $checkOut = \Carbon\Carbon::parse(
                request('check_out')
            );

            $nights = $checkIn->diffInDays(
                $checkOut
            );

            $rooms = request('rooms');

            $total =
                $nights *
                $rooms *
                $hotel->price_per_night;

        @endphp

        <p>

            <strong>Total Nights:</strong>

            {{ $nights }}

        </p>

        <div class="total-box">

            Estimated Total:

            <strong>

                ₱{{ number_format(
                    $total,
                    2
                ) }}

            </strong>

        </div>

    </div>

    <form
    method="POST"
    action="/hotel/booking/store">

        @csrf

        <input
        type="hidden"
        name="hotel_id"
        value="{{ $hotel->id }}">

        <input
        type="hidden"
        name="check_in"
        value="{{ request('check_in') }}">

        <input
        type="hidden"
        name="check_out"
        value="{{ request('check_out') }}">

        <input
        type="hidden"
        name="rooms"
        value="{{ request('rooms') }}">

        <button
        type="submit"
        class="payment-btn">

            🏨 Confirm Reservation

        </button>

    </form>

</div>

</div>

</body>

</html>
```
