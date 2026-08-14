<!DOCTYPE html>
<html lang="en">

<head>

<meta charset="UTF-8">

<meta name="viewport"
content="width=device-width, initial-scale=1.0">

<title>Search Results</title>

<link rel="stylesheet"
href="{{ asset('css/style.css') }}">

<link rel="stylesheet"
href="{{ asset('css/user-dashboard.css') }}">

</head>

<body class="dashboard-body">

<div class="dashboard-container">

<div class="back-btn">

<button onclick="history.back()">

← Back

</button>

</div>

<!-- ===========================
SEARCH HEADER
=========================== -->

<div class="welcome-section">

<div class="welcome-content">

<div>

<h1>

🔍 Search Results

</h1>

<p>

Showing results for

<strong>

"{{ $keyword }}"

</strong>

</p>

</div>

</div>

</div>

<!-- =====================================
TOURIST SPOTS
===================================== -->

@if($spots->count())

<div class="section-header">

<h2>

📍 Tourist Spots

</h2>

</div>

<div class="explore-grid">

@foreach($spots as $spot)

<div class="explore-card">

<img

src="{{ asset('uploads/spots/'.$spot->image) }}"

style="width:100%;
height:220px;
object-fit:cover;
border-radius:15px;">

<h3>

{{ $spot->name }}

</h3>

<p>

📍 {{ $spot->location }}

</p>

<p>

⭐

{{ number_format(
$spot->ratings->avg('rating') ?? 0,
1
) }}

</p>

<a

href="/spots/{{ $spot->id }}"

class="explore-btn">

View Spot →

</a>

</div>

@endforeach

</div>

@endif

<!-- =====================================
        HOTELS
===================================== -->

@if($hotels->count())

<div class="section-header">

<h2>

🏨 Hotels

</h2>

</div>

<div class="explore-grid">

@foreach($hotels as $hotel)

<div class="explore-card">

<img

src="{{ asset('uploads/hotels/'.$hotel->image) }}"

style="width:100%;
height:220px;
object-fit:cover;
border-radius:15px;">

<h3>

{{ $hotel->name }}

</h3>

<p>

📍 {{ $hotel->location }}

</p>

<p>

⭐ {{ number_format(
$hotel->ratings->avg('rating') ?? 0,
1
) }}

</p>

<a

href="/hotels/{{ $hotel->id }}"

class="explore-btn">

View Hotel →

</a>

</div>

@endforeach

</div>

@endif


<!-- =====================================
        RESTAURANTS
===================================== -->

@if($foods->count())

<div class="section-header">

<h2>

🍽 Restaurants

</h2>

</div>

<div class="explore-grid">

@foreach($foods as $food)

<div class="explore-card">

<img

src="{{ asset('uploads/foods/'.$food->image) }}"

style="width:100%;
height:220px;
object-fit:cover;
border-radius:15px;">

<h3>

{{ $food->name }}

</h3>

<p>

📍 {{ $food->location }}

</p>

<p>

⭐ {{ number_format(
$food->ratings->avg('rating') ?? 0,
1
) }}

</p>

<a

href="/foods/{{ $food->id }}"

class="explore-btn">

View Restaurant →

</a>

</div>

@endforeach

</div>

@endif


<!-- =====================================
        NO RESULTS
===================================== -->

@if(
$spots->count()==0 &&
$hotels->count()==0 &&
$foods->count()==0
)

<div class="empty-dashboard-card">

<div class="empty-icon">

🔍

</div>

<h2>

No Results Found

</h2>

<p>

Sorry, we couldn't find anything matching

<strong>

"{{ $keyword }}"

</strong>

</p>

<br>

<a

href="/user"

class="explore-btn">

Back to Dashboard

</a>

</div>

@endif


<!-- =====================================
        FOOTER
===================================== -->

<footer class="dashboard-footer">

<div class="footer-left">

<h3>

🌴 SpotTour

</h3>

<p>

Explore • Book • Travel

</p>

</div>

<div class="footer-right">

<a href="/spots">

Tourist Spots

</a>

<a href="/hotels">

Hotels

</a>

<a href="/foods">

Restaurants

</a>

</div>

</footer>

</div>

</body>

</html>