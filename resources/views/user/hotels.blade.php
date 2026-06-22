<!DOCTYPE html>

<html>
<head>

<title>Hotels</title>

<link rel="stylesheet"
href="{{ asset('css/style.css') }}">

</head>

<body>

<div class="page-container">

<!-- BACK -->

<div class="back-btn">
    <button onclick="history.back()">
        ← Back
    </button>
</div>

<br></br>

<h1>

🏨 Hotels

</h1>

<div class="spots-grid">

@foreach($hotels as $hotel)

<div class="spots-card">

    @if($hotel->image)

    <img
    src="{{ asset('uploads/hotels/'.$hotel->image) }}"
    class="spots-image">

    @else

    <img
    src="https://via.placeholder.com/300x200"
    class="spots-image">

    @endif

    <div class="spots-content">

        <h3>

            {{ $hotel->name }}

        </h3>

        <p>

            📍 {{ $hotel->location }}

        </p>

        <p>

            💰 ₱{{ number_format(
                $hotel->price_per_night,
                2
            ) }}

            / Night

        </p>

        <p>

            🛏 {{ $hotel->available_rooms }}
            Rooms Available

        </p>

        <a
        href="/hotels/{{ $hotel->id }}"
        class="view-btn">

            View Details

        </a>

    </div>

</div>

@endforeach

</div>

</div>

</body>

</html>