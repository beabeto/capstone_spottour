<!DOCTYPE html>

<html>
<head>

<title>Hotel Reservation Confirmation</title>

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

         Hotel Reservation Confirmation

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

             {{ $hotel->location }}

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
            <strong>Room type:</strong>
            {{ $hotel->rooms->firstWhere('id', (int) request('hotel_room_id'))?->name ?? 'Standard room' }}
        </p>

        <p>

            <strong>Available Rooms:</strong>

            {{ $hotel->available_rooms }}

        </p>

        <p>

            <strong>Price Per Night:</strong>

            PHP {{ number_format(
                $hotel->rooms->firstWhere('id', (int) request('hotel_room_id'))?->price_per_night ?? $hotel->price_per_night,
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
            $roomType = $hotel->rooms->firstWhere('id', (int) request('hotel_room_id'));
            $nightly = $roomType?->price_per_night ?? $hotel->price_per_night;
            $extra = optional($hotel->packages->firstWhere('id', (int) request('package_id')))->price ?? 0;

            $total =
                $nights *
                $rooms *
                $nightly +
                $extra;

        @endphp

        <p>

            <strong>Total Nights:</strong>

            {{ $nights }}

        </p>

        <div class="total-box">

            Estimated Total:

            <strong>

                PHP {{ number_format(
                    $total,
                    2
                ) }}

            </strong>

        </div>

    </div>

<form method="GET" action="/hotel/payment">
    <input type="hidden" name="hotel_id" value="{{ $hotel->id }}">
    <input type="hidden" name="check_in" value="{{ request('check_in') }}">
    <input type="hidden" name="check_out" value="{{ request('check_out') }}">
    <input type="hidden" name="rooms" value="{{ request('rooms') }}">
    <input type="hidden" name="hotel_room_id" value="{{ request('hotel_room_id') }}">
    <input type="hidden" name="package_id" value="{{ request('package_id') }}">
    <button type="submit" class="payment-btn">Proceed To Payment</button>
</form>

</div>

</div>

</body>

</html>
