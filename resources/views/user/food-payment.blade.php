<!DOCTYPE html>

<html>
<head>

<title>Restaurant Payment</title>

<link rel="stylesheet"
href="{{ asset('css/style.css') }}">

</head>

<body class="dashboard-body">

<div class="back-btn">

    <button onclick="history.back()">

        ←

    </button>

</div>

<div class="payment-container">

<div class="payment-card">

    <h1>

        🍽 Restaurant Payment

    </h1>

    @if($food->image)

    <img
    src="{{ asset('uploads/foods/'.$food->image) }}"
    class="payment-image">

    @endif

    <div class="payment-summary">

        <h2>

            {{ $food->name }}

        </h2>

        <p>

            📍 {{ $food->location }}

        </p>

        <hr>

        <p>

            <strong>Reservation Date:</strong>

            {{ \Carbon\Carbon::parse(
                $reservation_date
            )->format('F d, Y') }}

        </p>

        <p>

            <strong>Reservation Time:</strong>

            {{ \Carbon\Carbon::parse(
                $reservation_time
            )->format('h:i A') }}

        </p>

        <p>

            <strong>Guests:</strong>

            {{ $guests }}

        </p>

        

        <p class="payment-total">

            Total Payment:

            ₱{{ number_format(
                $total,
                2
            ) }}

        </p>

    </div>

    <div class="payment-method">

        <h3>

            📱 GCash Payment

        </h3>

        <div class="gcash-box">

            <p>

                <strong>GCash Number</strong>

            </p>

            <h2>

                {{ $food->owner->gcash_number }}

            </h2>

            <p>

                <strong>Account Name</strong>

            </p>

            <h3>

                {{ $food->owner->gcash_name }}

            </h3>

        </div>

        <div class="payment-note">

            ⚠️ Please send the exact payment amount shown above, then upload your receipt to complete your reservation.

        </div>

    </div>

    <form
    method="POST"
    action="/food/booking/store"
    enctype="multipart/form-data">

        @csrf

        <input
        type="hidden"
        name="food_id"
        value="{{ $food->id }}">

        <input
        type="hidden"
        name="reservation_date"
        value="{{ $reservation_date }}">

        <input
        type="hidden"
        name="reservation_time"
        value="{{ $reservation_time }}">

        <input
        type="hidden"
        name="guests"
        value="{{ $guests }}">

        <input
        type="hidden"
        name="total_price"
        value="{{ $total }}">

        <label>

            👤 Your GCash Name

        </label>

        <input
        type="text"
        name="payer_name"
        placeholder="Enter your GCash account name"
        required>

        <label>

            📱 Your GCash Number

        </label>

        <input
        type="text"
        name="payer_number"
        placeholder="09XXXXXXXXX"
        maxlength="11"
        required>

        <label>

            📸 Upload Receipt

        </label>

        <input
        type="file"
        name="receipt"
        accept="image/*"
        required>

        <button
        type="submit"
        class="submit-payment-btn">

            ✅ Submit Restaurant Payment

        </button>

    </form>

</div>

</div>

</body>

</html>