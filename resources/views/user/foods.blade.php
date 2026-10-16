<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Restaurants</title>
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
            <h1>Restaurants</h1>
            <p>Local places where you can reserve a table and see the price per guest.</p>
        </div>
        <strong class="catalog-count">{{ $foods->count() }} {{ $foods->count() === 1 ? 'restaurant' : 'restaurants' }}</strong>
    </header>

    @forelse($foods as $food)
        @if($loop->first)<div class="catalog-grid">@endif
        <a class="catalog-card" href="/foods/{{ $food->id }}">
            <div class="catalog-media">
                @if($food->image)
                    <img src="{{ asset('uploads/foods/'.$food->image) }}" alt="{{ $food->name }}">
                @else
                    <div class="catalog-fallback">No photo yet</div>
                @endif
                <span class="catalog-chip">Restaurant</span>
            </div>
            <div class="catalog-body">
                <h2>{{ $food->name }}</h2>
                <p class="catalog-location">{{ $food->location }}</p>
                <p class="catalog-summary">{{ \Illuminate\Support\Str::limit($food->description, 90) }}</p>
                <div class="catalog-meta">
                    <span class="catalog-rating">{{ $food->ratings_avg_rating ? '★ '.number_format($food->ratings_avg_rating, 1) : 'New' }}</span>
                    <span class="catalog-price">
                        @if((float) $food->price_per_guest > 0)
                            PHP {{ number_format((float) $food->price_per_guest, 2) }}
                            <small>per guest</small>
                        @else
                            {{ $food->available_tables }} tables
                        @endif
                    </span>
                </div>
                <span class="catalog-action">View details</span>
            </div>
        </a>
        @if($loop->last)</div>@endif
    @empty
        <div class="catalog-empty">
            <h2>No restaurants yet</h2>
            <p>Approved restaurants will show up here.</p>
        </div>
    @endforelse
</div>

</body>
</html>
