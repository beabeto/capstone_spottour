<!DOCTYPE html>

<html lang="en">
<head>

<meta charset="UTF-8">

<meta name="viewport"
content="width=device-width, initial-scale=1.0">

<title>My Favorites</title>

<link rel="stylesheet"
href="{{ asset('css/style.css') }}">

</head>

<body class="dashboard-body">

<div class="back-btn">

    <button onclick="history.back()">

        ←

    </button>

</div>

<div class="dashboard-container">

<h1>❤️ My Favorites</h1>

{{-- SPOT FAVORITES --}}

@forelse($favorites as $favorite)

<div class="favorite-card">

    <img
    src="{{ asset('uploads/spots/'.$favorite->spot->image) }}"
    class="favorite-image">

    <div class="favorite-content">

        <h3>

            📍 {{ $favorite->spot->name }}

        </h3>

        <p>

            {{ $favorite->spot->location }}

        </p>

        <div class="favorite-rating">

            ⭐ {{ number_format(
                $favorite->spot->ratings->avg('rating') ?? 0,
                1
            ) }}

        </div>

        <div class="favorite-actions">

            <a
            href="/spots/{{ $favorite->spot->id }}"
            class="view-btn">

                View Spot

            </a>

            <a
            href="/favorite/{{ $favorite->spot->id }}"
            class="remove-btn">

                Remove

            </a>

        </div>

    </div>

</div>

@empty
@endforelse


{{-- HOTEL FAVORITES --}}

@forelse($hotelFavorites as $favorite)

<div class="favorite-card">

    <img
    src="{{ asset('uploads/hotels/'.$favorite->hotel->image) }}"
    class="favorite-image">

    <div class="favorite-content">

        <h3>

            🏨 {{ $favorite->hotel->name }}

        </h3>

        <p>

            {{ $favorite->hotel->location }}

        </p>

        <div class="favorite-rating">

            ⭐ {{ number_format(
                $favorite->hotel->ratings->avg('rating') ?? 0,
                1
            ) }}

        </div>

        <div class="favorite-actions">

            <a
            href="/hotels/{{ $favorite->hotel->id }}"
            class="view-btn">

                View Hotel

            </a>

            <a
            href="/hotel/favorite/{{ $favorite->hotel->id }}"
            class="remove-btn">

                Remove

            </a>

        </div>

    </div>

</div>

@empty
@endforelse


@if(
    $favorites->count() == 0 &&
    $hotelFavorites->count() == 0
)

<div class="empty-card">

    <h3>

        No Favorites Yet

    </h3>

</div>

@endif

</div>

</body>

</html>