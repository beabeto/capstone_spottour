<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Surigao day plan | SpotTour</title>
    <link rel="stylesheet" href="{{ asset('css/style.css') }}">
    <link rel="stylesheet" href="{{ asset('css/listing-details.css') }}?v={{ filemtime(public_path('css/listing-details.css')) }}">
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css">
</head>
<body class="dashboard-body booking-form-page day-plan-page">
<main class="booking-form-shell">
    <div class="checkout-toolbar">
        <button type="button" class="back-link" onclick="window.history.back()">Back</button>
    </div>

    <section class="booking-form-heading">
        <span>One day in Surigao</span>
        <h1>Day plan</h1>
        <p>Build your schedule first. Then book each stop and pay on that booking page.</p>
    </section>

    @if(session('success'))<div class="toast-success">{{ session('success') }}</div>@endif
    @if($errors->any())<div class="toast-error">{{ $errors->first() }}</div>@endif

    <form method="POST" action="/user/plan" class="spot-card day-plan-form">
        @csrf
        <label>Date<input type="date" name="plan_date" min="{{ date('Y-m-d') }}" value="{{ old('plan_date', now()->toDateString()) }}" required></label>

        <div class="day-plan-stop">
            <label>Morning spot
                <select name="spot_id">
                    <option value="">No spot</option>
                    @foreach($spots as $spot)<option value="{{ $spot->id }}" @selected(old('spot_id') == $spot->id)>{{ $spot->name }}</option>@endforeach
                </select>
            </label>
            <label>Time
                <input type="time" name="spot_time" value="{{ old('spot_time', '08:00') }}">
            </label>
        </div>

        <div class="day-plan-stop">
            <label>Meal
                <select name="food_id">
                    <option value="">No restaurant</option>
                    @foreach($foods as $food)<option value="{{ $food->id }}" @selected(old('food_id') == $food->id)>{{ $food->name }}</option>@endforeach
                </select>
            </label>
            <label>Time
                <input type="time" name="food_time" value="{{ old('food_time', '12:00') }}">
            </label>
        </div>

        <div class="day-plan-stop">
            <label>Stay
                <select name="hotel_id">
                    <option value="">No hotel</option>
                    @foreach($hotels as $hotel)<option value="{{ $hotel->id }}" @selected(old('hotel_id') == $hotel->id)>{{ $hotel->name }}</option>@endforeach
                </select>
            </label>
            <label>Check-in time
                <input type="time" name="hotel_time" value="{{ old('hotel_time', '14:00') }}">
            </label>
        </div>

        <label>Note<input type="text" name="note" value="{{ old('note') }}" placeholder="Optional, such as a birthday trip"></label>
        <button type="submit" class="submit-payment-btn">Build my day</button>
    </form>

    @if($plan)
        @php
            $planDate = $plan->plan_date->toDateString();
            $spotTime = $plan->spot_time ? \Carbon\Carbon::parse($plan->spot_time)->format('H:i') : '08:00';
            $foodTime = $plan->food_time ? \Carbon\Carbon::parse($plan->food_time)->format('H:i') : '12:00';
            $checkOut = $plan->plan_date->copy()->addDay()->toDateString();
            $spotUrl = $plan->spot ? '/booking/spot/'.$plan->spot->id.'?'.http_build_query(['booking_date' => $planDate]) : null;
            $foodUrl = $plan->food ? '/booking/food/'.$plan->food->id.'?'.http_build_query(['reservation_date' => $planDate, 'reservation_time' => $foodTime]) : null;
            $hotelUrl = $plan->hotel ? '/booking/hotel/'.$plan->hotel->id.'?'.http_build_query(['check_in' => $planDate, 'check_out' => $checkOut]) : null;
        @endphp

        <section class="spot-card day-plan-result">
            <div class="day-plan-next">
                <strong>What’s next</strong>
                <p>This plan is only your schedule. Open each stop below, finish the reservation form, then choose cash, GCash, Maya, or bank transfer on the payment page.</p>
            </div>

            <h2>{{ $plan->plan_date->format('F d, Y') }}</h2>
            @if($plan->note)<p class="day-plan-note">{{ $plan->note }}</p>@endif

            <div class="day-plan-timeline">
                <article class="day-plan-row">
                    <div>
                        <strong>Morning</strong>
                        @if($plan->spot_time)<span class="day-plan-time">{{ $plan->timeLabel('spot_time') }}</span>@endif
                        <span>{{ $plan->spot->name ?? 'No spot yet' }}</span>
                    </div>
                    @if($spotUrl)
                        <a class="day-plan-book" href="{{ $spotUrl }}">Book &amp; pay</a>
                    @endif
                </article>
                <article class="day-plan-row">
                    <div>
                        <strong>Meal</strong>
                        @if($plan->food_time)<span class="day-plan-time">{{ $plan->timeLabel('food_time') }}</span>@endif
                        <span>{{ $plan->food->name ?? 'No restaurant yet' }}</span>
                    </div>
                    @if($foodUrl)
                        <a class="day-plan-book" href="{{ $foodUrl }}">Reserve &amp; pay</a>
                    @endif
                </article>
                <article class="day-plan-row">
                    <div>
                        <strong>Stay</strong>
                        @if($plan->hotel_time)<span class="day-plan-time">{{ $plan->timeLabel('hotel_time') }}</span>@endif
                        <span>{{ $plan->hotel->name ?? 'No hotel yet' }}</span>
                    </div>
                    @if($hotelUrl)
                        <a class="day-plan-book" href="{{ $hotelUrl }}">Reserve &amp; pay</a>
                    @endif
                </article>
            </div>

            <p class="day-plan-hint">Pay separately for each place. After payment, check <a href="/user/bookings">My bookings</a> for confirmation.</p>
            <div id="planMap" class="day-plan-map"></div>
        </section>
    @endif
</main>
<style>
body.day-plan-page .booking-form-shell{padding-top:28px!important}
body.day-plan-page .checkout-toolbar{display:flex;align-items:center;margin:0 0 14px}
body.day-plan-page .back-link{position:static!important;top:auto!important;left:auto!important;display:inline-flex!important;align-items:center;justify-content:center;min-width:76px;min-height:42px;padding:0 15px;border:1px solid #d7ebe4;border-radius:9px;background:#fff;color:#17473f;font-weight:800;cursor:pointer}
body.day-plan-page .back-link:hover{border-color:#167b69;background:#f3fbf8;color:#126a59}
.day-plan-stop{display:grid;grid-template-columns:minmax(0,1.4fr) minmax(120px,.6fr);gap:12px;align-items:end;margin-bottom:4px}
.day-plan-result{margin-top:18px!important;padding:22px!important;border:1px solid #d7e8e3!important;border-radius:16px!important;background:#fff!important}
.day-plan-next{margin:0 0 18px;padding:14px 16px;border-radius:12px;background:#e8f5f1;color:#17473f}
.day-plan-next strong{display:block;margin-bottom:4px;font-size:15px}
.day-plan-next p{margin:0;color:#31564d;font-size:14px;line-height:1.45;font-weight:600}
.day-plan-note{margin:0 0 14px;color:#607772}
.day-plan-timeline{display:grid;gap:10px;margin:16px 0}
.day-plan-row{display:flex;align-items:center;justify-content:space-between;gap:14px;padding:14px 16px;border:1px solid #dcebe7;border-radius:12px;background:#fbfefd}
.day-plan-row > div{display:grid;grid-template-columns:88px auto minmax(0,1fr);gap:8px 10px;align-items:center;min-width:0}
.day-plan-row strong{color:#17473f}
.day-plan-time{display:inline-flex;align-items:center;justify-content:center;min-height:28px;padding:0 8px;border-radius:999px;background:#e8f5f1;color:#167b69;font-size:12px;font-weight:800}
.day-plan-book{flex-shrink:0;display:inline-flex;align-items:center;justify-content:center;min-height:40px;padding:0 14px;border-radius:9px;background:#167b69;color:#fff!important;font-size:13px;font-weight:800;text-decoration:none}
.day-plan-book:hover{background:#12685a;color:#fff!important}
.day-plan-hint{margin:4px 0 14px;color:#607772;font-size:13px;line-height:1.45}
.day-plan-hint a{color:#167b69;font-weight:800}
.day-plan-map{height:320px;margin-top:4px;border-radius:12px;overflow:hidden}
@media(max-width:640px){
.day-plan-stop,.day-plan-row > div{grid-template-columns:1fr}
.day-plan-row{flex-direction:column;align-items:stretch}
.day-plan-book{width:100%}
}
</style>
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
@if($plan)
<script>
const pins = @json($pins);
const map = L.map('planMap').setView([9.7868, 125.495], 13);
L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', { attribution: '&copy; OpenStreetMap' }).addTo(map);
const bounds = [];
pins.forEach((pin) => {
    if (pin.lat === null || pin.lng === null) return;
    L.marker([pin.lat, pin.lng]).addTo(map).bindPopup('<a href="'+pin.url+'">'+pin.label+'</a>');
    bounds.push([pin.lat, pin.lng]);
});
if (bounds.length) map.fitBounds(bounds, { padding: [28, 28] });
</script>
@endif
</body>
</html>
