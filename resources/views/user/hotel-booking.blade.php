<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reserve {{ $hotel->name }} | SpotTour</title>
    <link rel="stylesheet" href="{{ asset('css/style.css') }}">
    <link rel="stylesheet" href="{{ asset('css/listing-details.css') }}">
</head>
<body class="dashboard-body booking-form-page">
<div class="back-btn"><button type="button" onclick="window.history.back()">Back</button></div>
<main class="booking-form-shell">
    <section class="booking-form-heading">
        <span>Hotel reservation</span>
        <h1>Reserve {{ $hotel->name }}</h1>
        <p>PHP {{ number_format($hotel->price_per_night, 2) }} per night</p>
    </section>
    <form method="GET" action="/hotel/booking/confirm" class="spot-card">
        <input type="hidden" name="hotel_id" value="{{ $hotel->id }}">
        <label>Check in<input type="date" name="check_in" class="availability-date" data-listing-type="hotel" data-listing-id="{{ $hotel->id }}" min="{{ date('Y-m-d') }}" value="{{ request('check_in') }}" required></label>
        <label>Check out<input type="date" name="check_out" min="{{ date('Y-m-d') }}" value="{{ request('check_out') }}" required></label>
        <label>Room type
            <select name="hotel_room_id" {{ $hotel->rooms->isNotEmpty() ? 'required' : '' }}>
                @if($hotel->rooms->isEmpty())
                    <option value="">Standard room — PHP {{ number_format($hotel->price_per_night, 2) }} / night ({{ $hotel->available_rooms }} left)</option>
                @endif
                @foreach($hotel->rooms as $room)
                    <option value="{{ $room->id }}">{{ $room->name }} — PHP {{ number_format($room->price_per_night, 2) }} / night ({{ $room->available_rooms }} left)</option>
                @endforeach
            </select>
        </label>
        <label>Rooms<input type="number" name="rooms" min="1" max="{{ $hotel->available_rooms }}" value="1" required></label>
        @if($hotel->packages->isNotEmpty())
            <label>Hotel service (optional)
                <select name="package_id">
                    <option value="">No extra service</option>
                    @foreach($hotel->packages as $package)
                        <option value="{{ $package->id }}">{{ $package->name }}@if($package->price) — PHP {{ number_format($package->price, 2) }}@endif</option>
                    @endforeach
                </select>
            </label>
        @endif
        <div class="booking-estimate"><span>Available rooms</span><strong>{{ $hotel->available_rooms }}</strong></div>
        <button type="submit" class="submit-payment-btn">Continue to confirmation</button>
    </form>
</main>
<script src="{{ asset('js/availability-calendar.js') }}"></script></body>
</html>
