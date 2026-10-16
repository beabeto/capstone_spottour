<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Complete payment | SpotTour</title>
    <link rel="stylesheet" href="{{ asset('css/style.css') }}"><link rel="stylesheet" href="{{ asset('css/listing-details.css') }}?v={{ filemtime(public_path('css/listing-details.css')) }}">
</head>
<body class="dashboard-body payment-page">
    @php
        $package = \App\Models\Package::where('spot_id', $spot->id)->find(request('package_id'));
        $total = ($spot->booking_type == 'cottage' ? $spot->cottage_price * $quantity : $spot->price) + (float) ($package->price ?? 0);
    @endphp
    <main class="checkout-shell">
        <div class="checkout-toolbar">
            <button type="button" class="back-link" onclick="window.history.back()">Back</button>
        </div>
        <div class="checkout-heading">
            <span class="eyebrow">Secure payment</span>
            <h1>Confirm your spot booking</h1>
            <p>Choose how you will pay. The owner verifies it before the booking is confirmed.</p>
        </div>
        <div class="checkout-layout">
            <section class="order-card" aria-label="Booking summary">
                @if($spot->image)<img src="{{ asset('uploads/spots/'.$spot->image) }}" class="checkout-image" alt="{{ $spot->name }}">@endif
                <div class="order-card-content">
                    <span class="order-type">Spot booking</span>
                    <h2>{{ $spot->name }}</h2>
                    <p class="muted-text">{{ $spot->location }}</p>
                    <dl class="summary-list">
                        <div><dt>Date</dt><dd>{{ \Carbon\Carbon::parse($booking_date)->format('M d, Y') }}</dd></div>
                        <div><dt>{{ $spot->booking_type == 'cottage' ? 'Cottages' : 'Entry' }}</dt><dd>{{ $quantity }}</dd></div>
                    </dl>
                    <div class="amount-row"><span>Total due</span><strong>&#8369;{{ number_format($total, 2) }}</strong></div>
                </div>
            </section>
            <section class="payment-form-card">
                <form method="POST" action="/bookings/store" enctype="multipart/form-data" class="checkout-form">
                    @csrf
                    <input type="hidden" name="spot_id" value="{{ $spot->id }}">
                    <input type="hidden" name="booking_date" value="{{ $booking_date }}">
                    <input type="hidden" name="quantity" value="{{ $quantity }}">
                    <input type="hidden" name="needs_guide" value="{{ request('needs_guide') ? 1 : 0 }}">
                    <input type="hidden" name="package_id" value="{{ request('package_id') }}">
                    @include('user.partials.payment-choice', ['owner' => $spot->owner, 'amount' => $total])
                    <button type="submit" class="submit-payment-btn">Continue</button>
                </form>
            </section>
        </div>
    </main>
</body>
</html>
