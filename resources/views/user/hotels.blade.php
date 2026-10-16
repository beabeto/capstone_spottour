<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Hotels</title>
<link rel="stylesheet" href="{{ asset('css/style.css') }}">
</head>
<body class="catalog-page">

<div class="back-btn">
    <button type="button" onclick="window.history.back(); return false;">Back</button>
</div>

<div class="catalog-shell">
    <header class="catalog-heading">
        <div>
            <span>Explore</span>
            <h1>Hotels</h1>
            <p>Stays with nightly rates, room availability, and direct booking.</p>
        </div>
        <strong class="catalog-count">{{ $hotels->count() }} {{ $hotels->count() === 1 ? 'hotel' : 'hotels' }}</strong>
    </header>

    @forelse($hotels as $hotel)
        @if($loop->first)<div class="catalog-grid">@endif
        <a class="catalog-card" href="/hotels/{{ $hotel->id }}">
            <div class="catalog-media">
                @if($hotel->image)
                    <img src="{{ asset('uploads/hotels/'.$hotel->image) }}" alt="{{ $hotel->name }}">
                @else
                    <div class="catalog-fallback">No photo yet</div>
                @endif
                <span class="catalog-chip">Hotel</span>
            </div>
            <div class="catalog-body">
                <h2>{{ $hotel->name }}</h2>
                <p class="catalog-location">{{ $hotel->location }}</p>
                <div class="catalog-meta">
                    <span class="catalog-rating">{{ $hotel->ratings_avg_rating ? '★ '.number_format($hotel->ratings_avg_rating, 1) : 'New' }}</span>
                    <span class="catalog-price">
                        PHP {{ number_format((float) $hotel->price_per_night, 2) }}
                        <small>{{ $hotel->available_rooms }} rooms open</small>
                    </span>
                </div>
                <span class="catalog-action">View details</span>
            </div>
        </a>
        @if($loop->last)</div>@endif
    @empty
        <div class="catalog-empty">
            <h2>No hotels yet</h2>
            <p>Approved stays will show up here.</p>
        </div>
    @endforelse
</div>

</body>
</html>
