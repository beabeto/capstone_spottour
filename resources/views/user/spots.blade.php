<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Tourist Spots</title>
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
            <h1>Tourist spots</h1>
            <p>Beaches, parks, and local places you can reserve directly with the owner.</p>
        </div>
        <strong class="catalog-count">{{ $spots->count() }} {{ $spots->count() === 1 ? 'place' : 'places' }}</strong>
    </header>

    @forelse($spots as $spot)
        @if($loop->first)<div class="catalog-grid">@endif
        <a class="catalog-card" href="/spots/{{ $spot->id }}">
            <div class="catalog-media">
                @if($spot->image)
                    <img src="{{ asset('uploads/spots/'.$spot->image) }}" alt="{{ $spot->name }}">
                @else
                    <div class="catalog-fallback">No photo yet</div>
                @endif
                <span class="catalog-chip">{{ $spot->booking_type === 'cottage' ? 'Cottage' : 'Tourist spot' }}</span>
            </div>
            <div class="catalog-body">
                <h2>{{ $spot->name }}</h2>
                <p class="catalog-location">{{ $spot->location }}</p>
                <div class="catalog-meta">
                    <span class="catalog-rating">{{ $spot->ratings_avg_rating ? '★ '.number_format($spot->ratings_avg_rating, 1) : 'New' }}</span>
                    <span class="catalog-price">
                        @if($spot->booking_type === 'cottage' && $spot->cottage_price)
                            PHP {{ number_format($spot->cottage_price, 2) }}
                            <small>per cottage</small>
                        @elseif($spot->price > 0)
                            PHP {{ number_format($spot->price, 2) }}
                            <small>entry fee</small>
                        @else
                            Free entry
                        @endif
                    </span>
                </div>
                <span class="catalog-action">View details</span>
            </div>
        </a>
        @if($loop->last)</div>@endif
    @empty
        <div class="catalog-empty">
            <h2>No tourist spots yet</h2>
            <p>Approved places will show up here.</p>
        </div>
    @endforelse
</div>

</body>
</html>
