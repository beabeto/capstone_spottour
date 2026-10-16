<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Confirm table | SpotTour</title>
    <link rel="stylesheet" href="{{ asset('css/style.css') }}">
    <link rel="stylesheet" href="{{ asset('css/listing-details.css') }}?v={{ filemtime(public_path('css/listing-details.css')) }}">
</head>
<body class="dashboard-body payment-page">
<main class="checkout-shell">
    <div class="checkout-toolbar">
        <button type="button" class="back-link" onclick="window.history.back()">Back</button>
    </div>
    <div class="checkout-heading">
        <span class="eyebrow">Secure payment</span>
        <h1>Confirm your table</h1>
        <p>Choose how you will pay. The restaurant verifies it before the table is confirmed.</p>
    </div>
    <div class="checkout-layout">
        <section class="order-card" aria-label="Reservation summary">
            @if($food->image)
                <img src="{{ asset('uploads/foods/'.$food->image) }}" class="checkout-image" alt="{{ $food->name }}">
            @endif
            <div class="order-card-content">
                <span class="order-type">Restaurant reservation</span>
                <h2>{{ $food->name }}</h2>
                <p class="muted-text">{{ $food->location }}</p>
                <dl class="summary-list">
                    <div><dt>Date</dt><dd>{{ \Carbon\Carbon::parse($reservation_date)->format('M d, Y') }}</dd></div>
                    <div><dt>Time</dt><dd>{{ \Carbon\Carbon::parse($reservation_time)->format('h:i A') }}</dd></div>
                    <div><dt>Guests</dt><dd>{{ $guests }}</dd></div>
                    @forelse(($menus ?? collect()) as $menu)
                        <div><dt>{{ $menu['name'] }} × {{ $menu['qty'] }}</dt><dd>&#8369;{{ number_format($menu['line_total'], 2) }}</dd></div>
                    @empty
                    @endforelse
                </dl>
                <div class="amount-row"><span>Total due</span><strong>&#8369;{{ number_format($total, 2) }}</strong></div>
            </div>
        </section>
        <section class="payment-form-card">
            <form method="POST" action="/food/booking/store" enctype="multipart/form-data" class="checkout-form">
                @csrf
                <input type="hidden" name="food_id" value="{{ $food->id }}">
                <input type="hidden" name="reservation_date" value="{{ $reservation_date }}">
                <input type="hidden" name="reservation_time" value="{{ $reservation_time }}">
                <input type="hidden" name="guests" value="{{ $guests }}">
                <input type="hidden" name="special_request" value="{{ $special_request }}">
                <input type="hidden" name="package_id" value="{{ request('package_id') }}">
                <input type="hidden" name="menu_items" value='@json($menus ?? [])'>
                @include('user.partials.payment-choice', ['owner' => $food->owner, 'amount' => $total])
                <button type="submit" class="submit-payment-btn">Continue</button>
            </form>
        </section>
    </div>
</main>
</body>
</html>
