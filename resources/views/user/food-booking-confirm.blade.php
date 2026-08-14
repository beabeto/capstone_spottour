<!DOCTYPE html>
<html>
<head>

<title>Reservation Confirmation</title>

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

        🍽 Reservation Confirmation

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

            📍 {{ $food->location }}

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

        <label>

            📅 Reservation Date

        </label>

        <input
        type="date"
        name="reservation_date"
        min="{{ date('Y-m-d') }}"
        required>

        <label>

            🕒 Reservation Time

        </label>

        <input
        type="time"
        name="reservation_time"
        required>

        <label>

            👥 Number of Guests

        </label>

        <input
        type="number"
        id="guests"
        name="guests"
        min="1"
        max="{{ $food->venue_capacity }}"
        value="1"
        required>

        <label>

            📝 Special Request

        </label>

        <textarea
        name="special_request"
        rows="4"
        placeholder="Optional"></textarea>

        <button
        type="submit"
        class="payment-btn">

            💳 Proceed To Payment

        </button>

    </form>

</div>

</div>

</body>
</html>