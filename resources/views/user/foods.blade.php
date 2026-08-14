<!DOCTYPE html>

<html>
<head>

<title>Food Establishments</title>

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

🍽 Food Establishments

</h1>

<div class="spots-grid">

@foreach($foods as $food)

<div class="spots-card">

    @if($food->image)

    <img
    src="{{ asset('uploads/foods/'.$food->image) }}"
    class="spots-image">

    @else

    <img
    src="https://via.placeholder.com/300x200"
    class="spots-image">

    @endif

    <div class="spots-content">

        <h3>

            {{ $food->name }}

        </h3>

        <p>

            📍 {{ $food->location }}

        </p>

        <p>

            🍽 {{ $food->cuisine }}

        </p>

        <p>

            💰 {{ $food->price_range }}

        </p>

        <a
        href="/foods/{{ $food->id }}"
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