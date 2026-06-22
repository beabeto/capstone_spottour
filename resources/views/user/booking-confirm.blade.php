<!DOCTYPE html>

<html>
<head>

<title>Booking Confirmation</title>

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

        📖 Booking Confirmation

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

            📍 {{ $spot->location }}

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

        <p>

            <strong>Entrance Fee:</strong>

            ₱{{ number_format(
                $spot->price,
                2
            ) }}

        </p>

        @if($spot->booking_type == 'cottage')

        <p>

            <strong>Cottage Price:</strong>

            ₱{{ number_format(
                $spot->cottage_price,
                2
            ) }}

        </p>

        <p>

            <strong>Available Cottages:</strong>

            {{ $spot->available_cottages }}

        </p>

        @endif

    </div>

    <form
    method="POST"
    action="/booking/payment">

        @csrf

        <input
        type="hidden"
        name="spot_id"
        value="{{ $spot->id }}">

        <label>

            📅 Booking Date

        </label>

        <input
        type="date"
        name="booking_date"
        min="{{ date('Y-m-d') }}"
        required>

        @if($spot->booking_type == 'cottage')

        <label>

            🏡 Number of Cottages

        </label>

        <input
        type="number"
        id="quantity"
        name="quantity"
        min="1"
        max="{{ $spot->available_cottages }}"
        value="1"
        oninput="calculateTotal()">

        <div class="total-box">

            Estimated Total:

            <span id="totalPrice">

                ₱{{ number_format(
                    $spot->cottage_price,
                    2
                ) }}

            </span>

        </div>

        @endif

        <button
        type="submit"
        class="payment-btn">

            💳 Proceed To Payment

        </button>

    </form>

</div>

</div>

@if($spot->booking_type == 'cottage')

<script>

function calculateTotal()
{
    let quantity =
        document.getElementById(
            'quantity'
        ).value;

    let price =
        {{ $spot->cottage_price }};

    let total =
        quantity * price;

    document
    .getElementById(
        'totalPrice'
    )
    .innerHTML =
    '₱' +
    total.toLocaleString(
        undefined,
        {
            minimumFractionDigits:2
        }
    );
}

</script>

@endif

</body>

</html>