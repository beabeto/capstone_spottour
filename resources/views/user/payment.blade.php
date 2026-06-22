<!DOCTYPE html>

<html>
<head>

<title>Payment</title>

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

        💳 Payment Information

    </h1>

    @if($spot->image)

    <img
    src="{{ asset('uploads/spots/'.$spot->image) }}"
    class="payment-image">

    @endif

    <div class="payment-summary">

        <h2>

            {{ $spot->name }}

        </h2>

        <p>

            📍 {{ $spot->location }}

        </p>

        <hr>

        <p>

            <strong>Booking Date:</strong>

            {{ \Carbon\Carbon::parse(
                $booking_date
            )->format('F d, Y') }}

        </p>

        <p>

            <strong>Quantity:</strong>

            {{ $quantity }}

        </p>

        @php

        $total =
        $spot->booking_type == 'cottage'
        ? $spot->cottage_price * $quantity
        : $spot->price;

        @endphp

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

                {{ $spot->owner->gcash_number }}

            </h2>

            <p>

                <strong>Account Name</strong>

            </p>

            <h3>

                {{ $spot->owner->gcash_name }}

            </h3>

        </div>

        <div class="payment-note">

            ⚠️ Send the exact amount shown above,
            then upload your payment receipt.

        </div>

    </div>

    <form
method="POST"
action="/bookings/store"
enctype="multipart/form-data">

    @csrf

    <input
    type="hidden"
    name="spot_id"
    value="{{ $spot->id }}">

    <input
    type="hidden"
    name="booking_date"
    value="{{ $booking_date }}">

    <input
    type="hidden"
    name="quantity"
    value="{{ $quantity }}">

    <!-- CUSTOMER PAYMENT DETAILS -->

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

    <!-- RECEIPT -->

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

        ✅ Submit Payment

    </button>

</form>

</div>

</div>

</body>

</html>