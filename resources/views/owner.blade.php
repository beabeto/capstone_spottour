<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">

<title>Owner Dashboard</title>

<link rel="stylesheet" href="{{ asset('css/style.css') }}">

</head>
<body class="owner-body">

<div class="owner-container">

    <!-- SIDEBAR -->

    <aside class="owner-sidebar" id="ownerSidebar">

        <div class="sidebar-logo">

    <div class="logo-header">

        <h2>SpotTour</h2>

        <button
            class="menu-toggle"
            onclick="toggleOwnerSidebar()">
            ☰
        </button>

    </div>

    <div class="sidebar-profile">

        @if(Auth::user()->profile_image)

            <img
                src="{{ asset('uploads/profile/' . Auth::user()->profile_image) }}"
                alt="Profile">

        @else

            <img
                src="https://cdn-icons-png.flaticon.com/512/3135/3135715.png"
                alt="Profile">

        @endif

        <h4>{{ Auth::user()->name }}</h4>

        <small>
            {{ ucfirst(Auth::user()->role) }}
        </small>

    </div>

</div>

        <ul class="owner-menu">

            <li>
                <a href="dashboard">
                    🏠
                    <span>Dashboard</span>
                </a>
            </li>

            @if(
                Auth::user()->business_type == 'spot_cottage' ||
                Auth::user()->business_type == 'spot_hotel' ||
                Auth::user()->business_type == 'spot_food' ||
                Auth::user()->business_type == 'spot_hotel_food'
            )

            <li>
                <a href="owner/spots">
                    📍
                    <span>My Spots</span>
                </a>
            </li>

            @endif

            @if(
                Auth::user()->business_type == 'hotel_inn_lounge' ||
                Auth::user()->business_type == 'spot_hotel' ||
                Auth::user()->business_type == 'hotel_food' ||
                Auth::user()->business_type == 'spot_hotel_food'
            )

            <li>
                <div class="dashboard-card">

<a href="/owner/hotels">

    🏨

    <h3>

        Hotels

    </h3>

</a>

</div>
            </li>

            @endif

            @if(
                Auth::user()->business_type == 'food_establishment' ||
                Auth::user()->business_type == 'spot_food' ||
                Auth::user()->business_type == 'hotel_food' ||
                Auth::user()->business_type == 'spot_hotel_food'
            )

            <li>
                <a href="owner/food">
                    🍽
                    <span>Food Establishments</span>
                </a>
            </li>

            @endif

            <li>
                <a href="owner/bookings">
                    📖
                    <span>Bookings</span>
                </a>
            </li>

            <li>
                <a href="/owner/calendar" class="dashboard-card">
    <span>📅</span>
    <h3>Reservation Calendar</h3>
</a>
            </li>

            @if(
    Auth::user()->business_type == 'spot_cottage' ||
    Auth::user()->business_type == 'spot_hotel' ||
    Auth::user()->business_type == 'spot_food' ||
    Auth::user()->business_type == 'spot_hotel_food' ||

    Auth::user()->business_type == 'hotel_inn_lounge' ||
    Auth::user()->business_type == 'hotel_food'
)

<li>
    <a href="/owner/reviews">
        ⭐
        <span>Reviews</span>
    </a>
</li>

@endif

            <li>
                <a href="owner/feedback">
                    💬
                    <span>Feedback</span>
                </a>
            </li>

            <li>
                <a href="owner/profile">
                    👤
                    <span>Profile</span>
                </a>
            </li>

            <li>
                <a href="/logout">
                    🚪
                    <span>Logout</span>
                </a>
            </li>

        </ul>

    </aside>

    <!-- MAIN -->

    <main class="owner-main">

        <div class="owner-topbar">

            <h1>
                Owner Dashboard
            </h1>

            <div class="admin-user">

    @if(Auth::user()->profile_image)

        <img
            src="{{ asset('uploads/profile/' . Auth::user()->profile_image) }}"
            alt="Profile">

    @else

        <img
            src="https://cdn-icons-png.flaticon.com/512/3135/3135715.png"
            alt="Profile">

    @endif

    <div>

        <span class="admin-name">
            {{ Auth::user()->name }}
        </span>

        <small class="admin-role">
            {{ ucfirst(Auth::user()->role) }}
        </small>

    </div>

</div>

        </div>

        <div class="owner-cards">

            <div class="owner-card">
                <h3>Total Listings</h3>
                <p>12</p>
            </div>

            <div class="owner-card">
                <h3>Total Bookings</h3>
                <p>54</p>
            </div>

            <div class="owner-card">
                <h3>Pending Requests</h3>
                <p>8</p>
            </div>

            <div class="owner-card">
                <h3>Reviews</h3>
                <p>36</p>
            </div>

        </div>

        <div class="owner-welcome">

            <h2>
                Welcome,
                {{ Auth::user()->name }}
            </h2>

            <p>
                Business Type:
                {{ Auth::user()->business_type }}
            </p>

        </div>

    </main>

</div>

<script>

function toggleOwnerSidebar()
{
    document
        .getElementById('ownerSidebar')
        .classList
        .toggle('collapsed');
}

</script>

</body>
</html>