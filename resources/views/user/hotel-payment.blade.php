```blade
<!DOCTYPE html>

<html>
<head>

<title>Hotel Payment</title>

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

        🏨 Hotel Payment

    </h1>

    @if($hotel->image)

    <img
    src="{{ asset('uploads/hotels/'.$hotel->image) }}"
    class="payment-image">

    @endif

    <div class="payment-summary">

        <h2>

            {{ $hotel->name }}

        </h2>

        <p>

            📍 {{ $hotel->location }}

        </p>

        <hr>

        <p>

            <strong>Check In:</strong>

            {{ \Carbon\Carbon::parse(
                $check_in
            )->format('F d, Y') }}

        </p>

        <p>

            <strong>Check Out:</strong>

            {{ \Carbon\Carbon::parse(
                $check_out
            )->format('F d, Y') }}

        </p>

        <p>

            <strong>Rooms:</strong>

            {{ $rooms }}

        </p>

        @php

        $nights =
            \Carbon\Carbon::parse($check_in)
            ->diffInDays(
                \Carbon\Carbon::parse($check_out)
            );

        $total =
            $hotel->price_per_night *
            $rooms *
            $nights;

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

                {{ $hotel->owner->gcash_number }}

            </h2>

            <p>

                <strong>Account Name</strong>

            </p>

            <h3>

                {{ $hotel->owner->gcash_name }}

            </h3>

        </div>

        <div class="payment-note">

            ⚠️ Send the exact amount shown above,
            then upload your payment receipt.

        </div>

    </div>

    <form
    method="POST"
    action="/hotel/booking/store"
    enctype="multipart/form-data">

        @csrf

        <input
        type="hidden"
        name="hotel_id"
        value="{{ $hotel->id }}">

        <input
        type="hidden"
        name="check_in"
        value="{{ $check_in }}">

        <input
        type="hidden"
        name="check_out"
        value="{{ $check_out }}">

        <input
        type="hidden"
        name="rooms"
        value="{{ $rooms }}">

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

            ✅ Submit Hotel Payment

        </button>

    </form>

</div>

</div>

</body>

</html>
```
