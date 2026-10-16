<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Payment received | SpotTour</title>
    <link rel="stylesheet" href="{{ asset('css/style.css') }}">
    <link rel="stylesheet" href="{{ asset('css/listing-details.css') }}?v={{ filemtime(public_path('css/listing-details.css')) }}">
</head>
<body class="dashboard-body payment-page">
<main class="checkout-shell">
    <div class="checkout-toolbar">
        <a class="back-link" href="/user/bookings">My bookings</a>
    </div>
    <section class="booking-confirm-card" style="margin:0 auto;padding:28px;border:1px solid #d7e8e3;border-radius:18px;background:#fff;">
        <span class="eyebrow" style="color:#167b69;font-size:12px;font-weight:800;letter-spacing:.08em;text-transform:uppercase;">Payment received</span>
        <h1 style="margin:8px 0 10px;color:#17473f;font-size:clamp(26px,4vw,34px);">{{ $method }} payment confirmed</h1>
        <p style="margin:0 0 18px;color:#607772;line-height:1.5;">SpotTour received your {{ $method }} payment for <strong>{{ $place }}</strong>. Your reservation is saved and waiting for the owner to approve it.</p>
        <div class="booking-summary" style="margin:0 0 20px;">
            <h3>Booking summary</h3>
            <p><strong>Place</strong>{{ $place }}</p>
            <p><strong>Payment</strong>{{ $method }}</p>
            <p><strong>Amount</strong>&#8369;{{ number_format($total, 2) }}</p>
            <p><strong>Status</strong>Waiting for owner approval</p>
        </div>
        <div style="display:flex;flex-wrap:wrap;gap:10px;">
            <a href="/user/bookings" class="payment-btn" style="display:inline-flex;align-items:center;justify-content:center;min-height:44px;padding:0 18px;border-radius:9px;background:#167b69;color:#fff;font-weight:800;text-decoration:none;">View my bookings</a>
            <a href="/user" class="back-link" style="text-decoration:none;">Back to dashboard</a>
        </div>
    </section>
</main>
</body>
</html>
