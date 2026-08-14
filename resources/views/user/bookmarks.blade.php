<!DOCTYPE html>

<html lang="en">
<head>

<meta charset="UTF-8">

<meta name="viewport"
content="width=device-width, initial-scale=1.0">

<title>My Bookmarks</title>

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

<h1>🔖 My Bookmarks</h1>

{{-- ================= SPOT BOOKMARKS ================= --}}

@if($bookmarks->count())

<h2 class="section-title">

    📍 Tourist Spots

</h2>

@foreach($bookmarks as $bookmark)

<div class="favorite-card">

    <img
    src="{{ asset('uploads/spots/'.$bookmark->spot->image) }}"
    class="favorite-image">

    <div class="favorite-content">

        <h3>

            {{ $bookmark->spot->name }}

        </h3>

        <p>

            📍 {{ $bookmark->spot->location }}

        </p>

        <div class="favorite-rating">

            ⭐ {{ number_format(
                $bookmark->spot->ratings->avg('rating') ?? 0,
                1
            ) }}

        </div>

        <div class="favorite-actions">

            <a
            href="/spots/{{ $bookmark->spot->id }}"
            class="view-btn">

                View Spot

            </a>

            <a
            href="/bookmark/{{ $bookmark->spot->id }}"
            class="remove-btn">

                Remove

            </a>

        </div>

    </div>

</div>

@endforeach

@endif


{{-- ================= HOTEL BOOKMARKS ================= --}}

@if($hotelBookmarks->count())

<h2 class="section-title">

    🏨 Hotels

</h2>

@foreach($hotelBookmarks as $bookmark)

<div class="favorite-card">

    <img
    src="{{ asset('uploads/hotels/'.$bookmark->hotel->image) }}"
    class="favorite-image">

    <div class="favorite-content">

        <h3>

            {{ $bookmark->hotel->name }}

        </h3>

        <p>

            📍 {{ $bookmark->hotel->location }}

        </p>

        <div class="favorite-rating">

            ⭐ {{ number_format(
                $bookmark->hotel->ratings->avg('rating') ?? 0,
                1
            ) }}

        </div>

        <div class="favorite-actions">

            <a
            href="/hotels/{{ $bookmark->hotel->id }}"
            class="view-btn">

                View Hotel

            </a>

            <a
            href="/hotel/bookmark/{{ $bookmark->hotel->id }}"
            class="remove-btn">

                Remove

            </a>

        </div>

    </div>

</div>

@endforeach

@endif


{{-- ================= FOOD BOOKMARKS ================= --}}

@if($foodBookmarks->count())

<h2 class="section-title">

    🍽 Restaurants

</h2>

@foreach($foodBookmarks as $bookmark)

<div class="favorite-card">

    <img
    src="{{ asset('uploads/foods/'.$bookmark->food->image) }}"
    class="favorite-image">

    <div class="favorite-content">

        <h3>

            {{ $bookmark->food->name }}

        </h3>

        <p>

            📍 {{ $bookmark->food->location }}

        </p>

        <div class="favorite-rating">

            ⭐ {{ number_format(
                $bookmark->food->ratings->avg('rating') ?? 0,
                1
            ) }}

        </div>

        <div class="favorite-actions">

            <a
            href="/foods/{{ $bookmark->food->id }}"
            class="view-btn">

                View Restaurant

            </a>

            <a
            href="/food/bookmark/{{ $bookmark->food->id }}"
            class="remove-btn">

                Remove

            </a>

        </div>

    </div>

</div>

@endforeach

@endif


{{-- ================= EMPTY STATE ================= --}}

@if(
    $bookmarks->count() == 0 &&
    $hotelBookmarks->count() == 0 &&
    $foodBookmarks->count() == 0
)

<div class="empty-card">

    <h3>

        No Bookmarks Yet

    </h3>

    <p>

        Save your favorite tourist spots,
        hotels, and restaurants to
        access them quickly later.

    </p>

</div>

@endif

</div>

</body>

</html>