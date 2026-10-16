<!doctype html><html lang="en"><head><meta charset="UTF-8"><meta name="viewport" content="width=device-width, initial-scale=1.0"><title>Complete payment | SpotTour</title><link rel="stylesheet" href="{{ asset('css/style.css') }}"><link rel="stylesheet" href="{{ asset('css/listing-details.css') }}?v={{ filemtime(public_path('css/listing-details.css')) }}"></head><body class="dashboard-body payment-page">
@php
    $roomId = (int) request('hotel_room_id', old('hotel_room_id'));
    $packageId = (int) request('package_id', old('package_id'));
    $nights = \Carbon\Carbon::parse($check_in)->diffInDays(\Carbon\Carbon::parse($check_out));
    $roomType = $hotel->rooms->firstWhere('id', $roomId);
    $nightly = $roomType?->price_per_night ?? $hotel->price_per_night;
    $extra = optional($hotel->packages->firstWhere('id', $packageId))->price ?? 0;
    $total = $nightly * $rooms * $nights + $extra;
@endphp
<main class="checkout-shell">
    <div class="checkout-toolbar"><button type="button" class="back-link" onclick="window.history.back()">Back</button></div>
    <div class="checkout-heading"><span class="eyebrow">Secure payment</span><h1>Confirm your stay</h1><p>Choose how you will pay. GCash or Maya opens the wallet checkout next. Cash and bank stay with the owner for verification.</p></div>
    <div class="checkout-layout">
        <section class="order-card">
            @if($hotel->image)<img src="{{ asset('uploads/hotels/'.$hotel->image) }}" class="checkout-image" alt="{{ $hotel->name }}">@endif
            <div class="order-card-content">
                <span class="order-type">Hotel reservation</span>
                <h2>{{ $hotel->name }}</h2>
                <p class="muted-text">{{ $hotel->location }}</p>
                <dl class="summary-list">
                    <div><dt>Check in</dt><dd>{{ \Carbon\Carbon::parse($check_in)->format('M d, Y') }}</dd></div>
                    <div><dt>Check out</dt><dd>{{ \Carbon\Carbon::parse($check_out)->format('M d, Y') }}</dd></div>
                    <div><dt>Stay</dt><dd>{{ $nights }} night{{ $nights == 1 ? '' : 's' }} / {{ $rooms }} room{{ $rooms == 1 ? '' : 's' }}</dd></div>
                </dl>
                <div class="amount-row"><span>Total due</span><strong>&#8369;{{ number_format($total, 2) }}</strong></div>
            </div>
        </section>
        <section class="payment-form-card">
            <form method="POST" action="/hotel/booking/store" enctype="multipart/form-data" class="checkout-form">
                @csrf
                <input type="hidden" name="hotel_id" value="{{ $hotel->id }}">
                <input type="hidden" name="check_in" value="{{ $check_in }}">
                <input type="hidden" name="check_out" value="{{ $check_out }}">
                <input type="hidden" name="rooms" value="{{ $rooms }}">
                <input type="hidden" name="hotel_room_id" value="{{ $roomId ?: '' }}">
                <input type="hidden" name="package_id" value="{{ $packageId ?: '' }}">
                @include('user.partials.payment-choice', ['owner' => $hotel->owner, 'amount' => $total])
                <button type="submit" class="submit-payment-btn">Continue</button>
            </form>
        </section>
    </div>
</main></body></html>
