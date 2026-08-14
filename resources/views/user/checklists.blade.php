<!DOCTYPE html>

<html lang="en">
<head>

<meta charset="UTF-8">

<meta name="viewport"
content="width=device-width, initial-scale=1.0">

<title>My Checklist</title>

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

<h1>✅ My Checklist</h1>

{{-- ================= SPOT CHECKLIST ================= --}}

@if($checklists->count())

<h2 class="section-title">

    📍 Tourist Spots

</h2>

@foreach($checklists as $checklist)

<div class="favorite-card">

    <img
    src="{{ asset('uploads/spots/'.$checklist->spot->image) }}"
    class="favorite-image">

    <div class="favorite-content">

        <h3>

            {{ $checklist->spot->name }}

        </h3>

        <p>

            📍 {{ $checklist->spot->location }}

        </p>

        <div class="favorite-rating">

            ⭐ {{ number_format(
                $checklist->spot->ratings->avg('rating') ?? 0,
                1
            ) }}

        </div>

        <div class="favorite-actions">

            <a
            href="/spots/{{ $checklist->spot->id }}"
            class="view-btn">

                View Spot

            </a>

            <a
            href="/checklist/{{ $checklist->spot->id }}"
            class="remove-btn">

                Remove

            </a>

        </div>

    </div>

</div>

@endforeach

@endif


{{-- ================= HOTEL CHECKLIST ================= --}}

@if($hotelChecklists->count())

<h2 class="section-title">

    🏨 Hotels

</h2>

@foreach($hotelChecklists as $checklist)

<div class="favorite-card">

    <img
    src="{{ asset('uploads/hotels/'.$checklist->hotel->image) }}"
    class="favorite-image">

    <div class="favorite-content">

        <h3>

            {{ $checklist->hotel->name }}

        </h3>

        <p>

            📍 {{ $checklist->hotel->location }}

        </p>

        <div class="favorite-rating">

            ⭐ {{ number_format(
                $checklist->hotel->ratings->avg('rating') ?? 0,
                1
            ) }}

        </div>

        <div class="favorite-actions">

            <a
            href="/hotels/{{ $checklist->hotel->id }}"
            class="view-btn">

                View Hotel

            </a>

            <a
            href="/hotel/checklist/{{ $checklist->hotel->id }}"
            class="remove-btn">

                Remove

            </a>

        </div>

    </div>

</div>

@endforeach

@endif


{{-- ================= FOOD CHECKLIST ================= --}}

@if($foodChecklists->count())

<h2 class="section-title">

    🍽 Restaurants

</h2>

@foreach($foodChecklists as $checklist)

<div class="favorite-card">

    <img
    src="{{ asset('uploads/foods/'.$checklist->food->image) }}"
    class="favorite-image">

    <div class="favorite-content">

        <h3>

            {{ $checklist->food->name }}

        </h3>

        <p>

            📍 {{ $checklist->food->location }}

        </p>

        <div class="favorite-rating">

            ⭐ {{ number_format(
                $checklist->food->ratings->avg('rating') ?? 0,
                1
            ) }}

        </div>

        <div class="favorite-actions">

            <a
            href="/foods/{{ $checklist->food->id }}"
            class="view-btn">

                View Restaurant

            </a>

            <a
            href="/food/checklist/{{ $checklist->food->id }}"
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
    $checklists->count() == 0 &&
    $hotelChecklists->count() == 0 &&
    $foodChecklists->count() == 0
)

<div class="empty-card">

    <h3>

        No Checklist Items Yet

    </h3>

    <p>

        Start exploring tourist spots,
        hotels, and restaurants and
        add them to your checklist.

    </p>

</div>

@endif

</div>

</body>

</html>