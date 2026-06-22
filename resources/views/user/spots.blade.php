<!DOCTYPE html>
<html>
<head>

<title>Tourist Spots</title>

<link rel="stylesheet"
href="{{ asset('css/style.css') }}">

</head>
<body>

<div class="page-container">

<!-- BACK -->

<div class="back-btn">
    <button onclick="history.back()">
        ← 
    </button>
</div>

<br></br>

<h1>Tourist Spots</h1>


<div class="spots-grid">

@foreach($spots as $spot)

<div class="spots-card">

    <img
    src="{{ asset('uploads/spots/'.$spot->image) }}"
    class="spots-image">

    <div class="spots-content">

        <h3>{{ $spot->name }}</h3>

        <p>
            📍 {{ $spot->location }}
        </p>

        <a
        href="/spots/{{ $spot->id }}"
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