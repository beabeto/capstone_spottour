<!DOCTYPE html>

<html lang="en">
<head>

<meta charset="UTF-8">

<meta name="viewport"
content="width=device-width, initial-scale=1.0">

<title>My Favorites</title>

<link rel="stylesheet" href="{{ asset('css/style.css') }}">
<link rel="stylesheet" href="{{ asset('css/user-dashboard.css') }}">
<link rel="stylesheet" href="{{ asset('css/account-pages.css') }}">

</head>

<body class="dashboard-body account-page">

@include('user.partials.account-bar')

<div class="dashboard-container">

<header class="account-hero">
    <p class="account-kicker">Saved places</p>
    <h1>My Favorites</h1>
    <p>Spots, hotels, and restaurants you liked and want to open again.</p>
</header>

{{-- SPOT FAVORITES --}}

@if($favorites->count())
<h2 class="section-title">Tourist Spots</h2>
@endif

@forelse($favorites as $favorite)

<div class="favorite-card">

    <img
    src="{{ asset('uploads/spots/'.$favorite->spot->image) }}"
    class="favorite-image">

    <div class="favorite-content">

        <h3>

             {{ $favorite->spot->name }}

        </h3>

        <p>

            {{ $favorite->spot->location }}

        </p>

        <div class="favorite-rating">

             ★ {{ number_format(
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

@if($hotelFavorites->count())
<h2 class="section-title">Hotels</h2>
@endif

@forelse($hotelFavorites as $favorite)

<div class="favorite-card">

    <img
    src="{{ asset('uploads/hotels/'.$favorite->hotel->image) }}"
    class="favorite-image">

    <div class="favorite-content">

        <h3>

             {{ $favorite->hotel->name }}

        </h3>

        <p>

            {{ $favorite->hotel->location }}

        </p>

        <div class="favorite-rating">

             ★ {{ number_format(
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


{{-- FOOD FAVORITES --}}

@if($foodFavorites->count())
<h2 class="section-title">Restaurants</h2>
@endif

@forelse($foodFavorites as $favorite)

<div class="favorite-card">

    <img
    src="{{ asset('uploads/foods/'.$favorite->food->image) }}"
    class="favorite-image">

    <div class="favorite-content">

        <h3>

             {{ $favorite->food->name }}

        </h3>

        <p>

            {{ $favorite->food->location }}

        </p>

        <div class="favorite-rating">

             ★ {{ number_format(
                $favorite->food->ratings->avg('rating') ?? 0,
                1
            ) }}

        </div>

        <div class="favorite-actions">

            <a
            href="/foods/{{ $favorite->food->id }}"
            class="view-btn">

                View Restaurant

            </a>

            <a
            href="/food/favorite/{{ $favorite->food->id }}"
            class="remove-btn">

                Remove

            </a>

        </div>

    </div>

</div>

@empty
@endforelse

@if($favorites->count() == 0 && $hotelFavorites->count() == 0 && $foodFavorites->count() == 0)
<div class="empty-card">
    <h3>No favorites yet</h3>
    <p>Places you like will show up here.</p>
</div>
@endif

</div>

</body>

</html>
