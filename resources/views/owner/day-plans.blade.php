<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Guest day plans | SpotTour</title>
    <link rel="stylesheet" href="{{ asset('css/style.css') }}">
    <style>
        .owner-day-plans-page{padding-bottom:48px;background:#f3f7f6}
        .owner-day-plans-page .page-shell{width:min(980px,calc(100% - 40px));margin:0 auto;padding:78px 0 56px}
        .owner-day-plans-page .page-head{margin:0 0 18px;padding:20px 22px;border:1px solid #d7e8e3;border-radius:16px;background:linear-gradient(135deg,#e8f6f1,#fff)}
        .owner-day-plans-page .page-head span{display:block;margin:0 0 4px;color:#167b69;font-size:11px;font-weight:800;letter-spacing:.08em;text-transform:uppercase}
        .owner-day-plans-page .page-head h1{margin:0 0 6px;color:#17473f;font-size:clamp(24px,3vw,30px);letter-spacing:-.03em}
        .owner-day-plans-page .page-head p{margin:0;color:#607772;font-size:14px;line-height:1.45}
        .owner-day-plans-page .plan-toolbar{display:grid;grid-template-columns:1fr auto;gap:12px;margin:0 0 16px;padding:14px;border:1px solid #d7e8e3;border-radius:14px;background:#fff}
        .owner-day-plans-page .plan-toolbar label{display:grid;gap:6px;color:#48635c;font-size:12px;font-weight:800}
        .owner-day-plans-page .plan-toolbar input,.owner-day-plans-page .plan-toolbar select{min-height:42px;padding:0 12px;border:1px solid #d5ebe4;border-radius:10px;background:#fff;color:#203531;font:inherit}
        .owner-day-plans-page .plan-summary{display:flex;flex-wrap:wrap;gap:8px;margin:0 0 14px}
        .owner-day-plans-page .plan-summary span{display:inline-flex;align-items:center;min-height:30px;padding:0 10px;border-radius:999px;background:#e8f5f1;color:#126a59;font-size:12px;font-weight:800}
        .owner-day-plans-page .plan-list{display:grid;gap:12px}
        .owner-day-plans-page .plan-card{display:grid;grid-template-columns:78px minmax(0,1fr);gap:14px;padding:14px;border:1px solid #d7e8e3;border-radius:14px;background:#fff;box-shadow:0 6px 18px rgba(23,71,63,.04)}
        .owner-day-plans-page .plan-card[hidden]{display:none!important}
        .owner-day-plans-page .plan-date{display:grid;place-items:center;align-content:center;min-height:78px;padding:10px;border-radius:12px;background:#e8f5f1;color:#17473f;text-align:center}
        .owner-day-plans-page .plan-date strong{display:block;font-size:28px;line-height:1;letter-spacing:-.04em}
        .owner-day-plans-page .plan-date span{display:block;margin-top:4px;font-size:12px;font-weight:800;text-transform:uppercase}
        .owner-day-plans-page .plan-body{min-width:0}
        .owner-day-plans-page .plan-body h2{margin:0 0 2px;color:#17473f;font-size:17px}
        .owner-day-plans-page .plan-body .guest-mail{margin:0 0 10px;color:#607772;font-size:12px}
        .owner-day-plans-page .plan-mine{margin:0 0 10px;padding:10px 12px;border-radius:10px;background:#f3faf7;color:#17473f;font-size:13px;font-weight:700;line-height:1.4}
        .owner-day-plans-page .plan-mine small{display:block;margin-bottom:2px;color:#167b69;font-size:11px;font-weight:800;letter-spacing:.04em;text-transform:uppercase}
        .owner-day-plans-page .plan-stops{display:grid;gap:6px;margin:0;padding:0;list-style:none}
        .owner-day-plans-page .plan-stops li{display:grid;grid-template-columns:72px minmax(0,1fr);gap:8px;align-items:start;color:#48635c;font-size:13px}
        .owner-day-plans-page .plan-stops b{color:#167b69;font-size:12px}
        .owner-day-plans-page .plan-note{margin:10px 0 0;color:#607772;font-size:13px}
        .owner-day-plans-page .plan-empty{padding:28px 18px;border:1px dashed #c9e2da;border-radius:14px;background:#fff;color:#607772;text-align:center}
        .owner-day-plans-page .plan-pager{margin-top:16px}
        @media(max-width:700px){
            .owner-day-plans-page .page-shell{width:min(100% - 24px,980px);padding-top:70px}
            .owner-day-plans-page .plan-toolbar{grid-template-columns:1fr}
            .owner-day-plans-page .plan-card{grid-template-columns:1fr}
            .owner-day-plans-page .plan-date{min-height:64px;justify-items:start;grid-auto-flow:column;gap:10px;padding:12px 14px}
            .owner-day-plans-page .plan-date strong{font-size:22px}
        }
    </style>
</head>
<body class="dashboard-body owner-day-plans-page">
@php
    $spotCount = $plans->getCollection()->filter(fn ($plan) => $plan->spot && $plan->spot->user_id === Auth::id())->count();
    $foodCount = $plans->getCollection()->filter(fn ($plan) => $plan->food && $plan->food->owner_id === Auth::id())->count();
    $hotelCount = $plans->getCollection()->filter(fn ($plan) => $plan->hotel && $plan->hotel->owner_id === Auth::id())->count();
@endphp
<div class="back-btn"><button type="button" onclick="window.history.back(); return false;">Back</button></div>
<main class="page-shell">
    <header class="page-head">
        <span>Owner workspace</span>
        <h1>Guest day plans</h1>
        <p>Travellers who included your spot, restaurant, or hotel in their Surigao day plan.</p>
    </header>

    <div class="plan-toolbar">
        <label>Search traveller or place
            <input type="search" id="planSearch" placeholder="Name, email, or listing">
        </label>
        <label>Your listing type
            <select id="planType">
                <option value="all">All in your listings</option>
                <option value="spot">Your spots</option>
                <option value="food">Your restaurants</option>
                <option value="hotel">Your hotels</option>
            </select>
        </label>
    </div>

    <div class="plan-summary">
        <span>{{ $plans->total() }} upcoming plans</span>
        <span>{{ $spotCount }} with your spot</span>
        <span>{{ $foodCount }} with your restaurant</span>
        <span>{{ $hotelCount }} with your hotel</span>
    </div>

    <section class="plan-list" id="planList">
        @forelse($plans as $plan)
            @php
                $mine = collect([
                    $plan->spot && $plan->spot->user_id === Auth::id()
                        ? ['type' => 'spot', 'label' => 'Spot', 'name' => $plan->spot->name, 'time' => $plan->timeLabel('spot_time')]
                        : null,
                    $plan->food && $plan->food->owner_id === Auth::id()
                        ? ['type' => 'food', 'label' => 'Meal', 'name' => $plan->food->name, 'time' => $plan->timeLabel('food_time')]
                        : null,
                    $plan->hotel && $plan->hotel->owner_id === Auth::id()
                        ? ['type' => 'hotel', 'label' => 'Stay', 'name' => $plan->hotel->name, 'time' => $plan->timeLabel('hotel_time')]
                        : null,
                ])->filter()->values();
                $types = $mine->pluck('type')->implode(' ');
                $search = strtolower(trim(($plan->user?->name ?? '').' '.($plan->user?->email ?? '').' '.$mine->pluck('name')->implode(' ').($plan->note ?? '')));
            @endphp
            <article class="plan-card"
                     data-types="{{ $types }}"
                     data-search="{{ $search }}">
                <div class="plan-date">
                    <strong>{{ $plan->plan_date->format('d') }}</strong>
                    <span>{{ $plan->plan_date->format('M Y') }}</span>
                </div>
                <div class="plan-body">
                    <h2>{{ $plan->user?->name ?? 'Traveller' }}</h2>
                    <p class="guest-mail">{{ $plan->user?->email ?: 'No email' }} · {{ $plan->plan_date->format('l, F d, Y') }}</p>

                    <div class="plan-mine">
                        <small>Your place in this plan</small>
                        {{ $mine->map(fn ($item) => $item['label'].': '.$item['name'].($item['time'] ? ' · '.$item['time'] : ''))->implode(' · ') ?: '—' }}
                    </div>

                    <ul class="plan-stops">
                        <li><b>Morning</b><span>{{ $plan->spot?->name ? $plan->spot->name.($plan->spot_time ? ' · '.$plan->timeLabel('spot_time') : '') : 'No spot' }}</span></li>
                        <li><b>Meal</b><span>{{ $plan->food?->name ? $plan->food->name.($plan->food_time ? ' · '.$plan->timeLabel('food_time') : '') : 'No restaurant' }}</span></li>
                        <li><b>Stay</b><span>{{ $plan->hotel?->name ? $plan->hotel->name.($plan->hotel_time ? ' · '.$plan->timeLabel('hotel_time') : '') : 'No hotel' }}</span></li>
                    </ul>

                    @if($plan->note)
                        <p class="plan-note">Note: {{ $plan->note }}</p>
                    @endif
                </div>
            </article>
        @empty
            <div class="plan-empty">No travellers have included your listings in a day plan yet.</div>
        @endforelse
        <div class="plan-empty" id="planFilterEmpty" hidden>No day plans match these filters.</div>
    </section>

    @if($plans->hasPages())
        <div class="plan-pager">{{ $plans->links() }}</div>
    @endif
</main>
<script>
(function () {
    var search = document.getElementById('planSearch');
    var type = document.getElementById('planType');
    var empty = document.getElementById('planFilterEmpty');
    var cards = Array.prototype.slice.call(document.querySelectorAll('.plan-card'));

    function applyFilters() {
        var query = (search.value || '').trim().toLowerCase();
        var selected = type.value;
        var visible = 0;
        cards.forEach(function (card) {
            var types = card.getAttribute('data-types') || '';
            var hay = card.getAttribute('data-search') || '';
            var typeOk = selected === 'all' || types.indexOf(selected) !== -1;
            var searchOk = !query || hay.indexOf(query) !== -1;
            var show = typeOk && searchOk;
            card.hidden = !show;
            if (show) visible += 1;
        });
        if (empty) empty.hidden = visible > 0 || cards.length === 0;
    }

    if (search) search.addEventListener('input', applyFilters);
    if (type) type.addEventListener('change', applyFilters);
})();
</script>
</body>
</html>
