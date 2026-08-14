<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="UTF-8">

    <meta name="viewport"
          content="width=device-width, initial-scale=1.0">

    <title>SpotTour Dashboard</title>

    <link rel="stylesheet" href="{{ asset('css/style.css') }}">
    <link rel="stylesheet" href="{{ asset('css/user-dashboard.css') }}">

</head>

<body class="dashboard-body">

@php

$unreadCount = \App\Models\Notification::where(
    'user_id',
    Auth::id()
)->where(
    'is_read',
    0
)->count();

@endphp

<!-- ===========================
        NAVBAR
=========================== -->

<nav class="navbar">

    <!-- Logo -->

    <div class="logo">

        <a href="/user">

            🌴 <strong>SpotTour</strong>

        </a>

    </div>

    <!-- Search -->

    <div class="dashboard-search">

    <form action="/search" method="GET">

        <input
            type="text"
            name="keyword"
            placeholder="Search spots, hotels or restaurants">

        <button type="submit">

            🔍

        </button>

    </form>

</div>

    <!-- Menu -->

    <ul class="nav-links">

        <li>

            <a href="/spots">

                📍 Spots

            </a>

        </li>

        <li>

            <a href="/hotels">

                🏨 Hotels

            </a>

        </li>

        <li>

            <a href="/foods">

                🍽 Restaurants

            </a>

        </li>

    </ul>

    <!-- Right -->

    <div class="nav-right">

        <!-- Notification -->

        <div class="notification">

            <a
                href="/user/notifications"
                class="notification-icon">

                🔔

                @if($unreadCount)

                <span class="badge">

                    {{ $unreadCount }}

                </span>

                @endif

            </a>

        </div>

        <!-- Profile -->

        <div class="user-dropdown">

            <div

                class="user-info"

                onclick="toggleUserMenu()">

                @if(Auth::user()->profile_image)

                    <img

                        src="{{ asset('uploads/profile/'.Auth::user()->profile_image) }}"

                        class="profile-mini">

                @else

                    <img

                        src="https://cdn-icons-png.flaticon.com/512/3135/3135715.png"

                        class="profile-mini">

                @endif

                <span>

                    {{ Auth::user()->name }}

                </span>

                ▼

            </div>

            <!-- Dropdown -->

            <div

                class="user-menu"

                id="userMenu">

                <a href="/user/profile">

                    👤 Profile

                </a>

                <a href="/user/bookings">

                    📖 My Bookings

                </a>

                <a href="/user/favorites">

                    ❤️ Favorites

                </a>

                <a href="/user/bookmarks">

                    🔖 Bookmarks

                </a>

                <a href="/user/checklist">

                    ✅ Checklist

                </a>

                <a href="/user/feedback">

                    💬 Feedback

                </a>

                <hr>

                <a href="/logout">

                    🚪 Logout

                </a>

            </div>

        </div>

    </div>

</nav>

<!-- ===========================
        PAGE CONTENT
=========================== -->

<div class="dashboard-container">

<!-- =====================================
        WELCOME CARD
====================================== -->

<div class="welcome-section">

    <div class="welcome-content">

        <div>

            <h1>

                Welcome Back,
                {{ Auth::user()->name }} 👋

            </h1>

            <p>

                Discover amazing tourist destinations,
                comfortable hotels, and delicious restaurants
                around Surigao.

            </p>

        </div>

        @if(Auth::user()->profile_image)

            <img
                src="{{ asset('uploads/profile/'.Auth::user()->profile_image) }}"
                class="welcome-avatar">

        @else

            <img
                src="https://cdn-icons-png.flaticon.com/512/3135/3135715.png"
                class="welcome-avatar">

        @endif

    </div>

</div>


<!-- =====================================
        EXPLORE SECTION
====================================== -->

<div class="section-header">

    <h2>

        Continue Exploring

    </h2>

    <p>

        Choose where you want to go next.

    </p>

</div>


<div class="explore-grid">

    <!-- Tourist Spots -->

    <div class="explore-card">

        <div class="explore-icon">

            📍

        </div>

        <h3>

            Tourist Spots

        </h3>

        <p>

            Explore beaches,
            mountains,
            parks,
            and tourist attractions.

        </p>

        <a
            href="/spots"
            class="explore-btn">

            Explore Spots →

        </a>

    </div>


    <!-- Hotels -->

    <div class="explore-card">

        <div class="explore-icon">

            🏨

        </div>

        <h3>

            Hotels

        </h3>

        <p>

            Find hotels,
            resorts,
            inns,
            and cottages for your stay.

        </p>

        <a
            href="/hotels"
            class="explore-btn">

            Explore Hotels →

        </a>

    </div>


    <!-- Restaurants -->

    <div class="explore-card">

        <div class="explore-icon">

            🍽

        </div>

        <h3>

            Restaurants

        </h3>

        <p>

            Discover restaurants,
            cafés,
            and local food establishments.

        </p>

        <a
            href="/foods"
            class="explore-btn">

            Explore Restaurants →

        </a>

    </div>

</div>

<!-- =====================================
        DASHBOARD STATISTICS
====================================== -->

<div class="section-header">

    <h2>

        Your Activity

    </h2>

    <p>

        A quick overview of your account.

    </p>

</div>

<div class="stats-grid">

    <!-- Bookings -->

    <div class="stat-card">

        <div class="stat-icon">

            📖

        </div>

        <h2>

            {{ $bookings ?? 0 }}

        </h2>

        <p>

            Bookings

        </p>

    </div>

    <!-- Favorites -->

    <div class="stat-card">

        <div class="stat-icon">

            ❤️

        </div>

        <h2>

            {{ $favorites ?? 0 }}

        </h2>

        <p>

            Favorites

        </p>

    </div>

    <!-- Bookmarks -->

    <div class="stat-card">

        <div class="stat-icon">

            🔖

        </div>

        <h2>

            {{ $bookmarks ?? 0 }}

        </h2>

        <p>

            Bookmarks

        </p>

    </div>

    <!-- Checklist -->

    <div class="stat-card">

        <div class="stat-icon">

            ✅

        </div>

        <h2>

            {{ $checklists ?? 0 }}

        </h2>

        <p>

            Checklist

        </p>

    </div>

    <!-- Reviews -->

    <div class="stat-card">

        <div class="stat-icon">

            ⭐

        </div>

        <h2>

            {{ $reviews ?? 0 }}

        </h2>

        <p>

            Reviews

        </p>

    </div>

</div>

<!-- =====================================
        UPCOMING BOOKING
====================================== -->

<div class="section-header">

    <h2>

        Upcoming Booking

    </h2>

    <p>

        Your next reservation.

    </p>

</div>

@if($nextBooking)

<div class="booking-preview-card">

    <div class="booking-preview-image">

        @if($nextBooking->type == 'spot' && $nextBooking->spot)

<img
src="{{ asset('uploads/spots/'.$nextBooking->spot->image) }}">

@elseif($nextBooking->type == 'hotel' && $nextBooking->hotel)

<img
src="{{ asset('uploads/hotels/'.$nextBooking->hotel->image) }}">

@elseif($nextBooking->type == 'food' && $nextBooking->food)

<img
src="{{ asset('uploads/foods/'.$nextBooking->food->image) }}">

@else

<img
src="https://cdn-icons-png.flaticon.com/512/3135/3135715.png">

@endif

    </div>

    <div class="booking-preview-content">

        <span class="booking-category">

            @if($nextBooking->type == 'spot')

                📍 Tourist Spot

            @elseif($nextBooking->type == 'hotel')

                🏨 Hotel

            @else

                🍽 Restaurant

            @endif

        </span>

        <h3>

            @if($nextBooking->type == 'spot')

    {{ optional($nextBooking->spot)->name ?? 'Tourist Spot' }}

@elseif($nextBooking->type == 'hotel')

    {{ optional($nextBooking->hotel)->name ?? 'Hotel' }}

@elseif($nextBooking->type == 'food')

    {{ optional($nextBooking->food)->name ?? 'Restaurant' }}

@else

    Booking

@endif

        </h3>

        <p>

            📅

            {{ \Carbon\Carbon::parse($nextBooking->date)->format('F d, Y') }}

        </p>

        <span class="booking-status">

            {{ ucfirst($nextBooking->status) }}

        </span>

        <br><br>

        <a
        href="/user/bookings"
        class="explore-btn">

            View Booking →

        </a>

    </div>

</div>

@else

<div class="empty-dashboard-card">

    <div class="empty-icon">

        📅

    </div>

    <h3>

        No Upcoming Booking

    </h3>

    <p>

        Book a tourist spot,
        hotel,
        or restaurant to start your adventure.

    </p>

    <a
    href="/spots"
    class="explore-btn">

        Explore Now →

    </a>

</div>

@endif

<!-- =====================================
        RECENT NOTIFICATIONS
====================================== -->

<div class="section-header">

    <h2>

        Recent Notifications

    </h2>

    <a
    href="/user/notifications"
    class="view-all-link">

        View All →

    </a>

</div>

@if($recentNotifications->count())

<div class="notification-list">

@foreach($recentNotifications as $notification)

<div class="notification-preview">

    <div class="notification-preview-icon">

        @if($notification->food_id)

            🍽

        @elseif($notification->hotel_id)

            🏨

        @elseif($notification->spot_id)

            📍

        @else

            🔔

        @endif

    </div>

    <div class="notification-preview-content">

        <p>

            {{ $notification->message }}

        </p>

        <small>

            {{ $notification->created_at->diffForHumans() }}

        </small>

    </div>

    @if(!$notification->is_read)

    <span class="notification-dot">

    </span>

    @endif

</div>

@endforeach

</div>

@else

<div class="empty-dashboard-card">

    <div class="empty-icon">

        🔔

    </div>

    <h3>

        No Notifications

    </h3>

    <p>

        You're all caught up!

    </p>

</div>

@endif

<!-- =====================================
        FOOTER
====================================== -->

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

        <a href="/user/feedback">

            Feedback

        </a>

    </div>

</footer>

</div>
<!-- END dashboard-container -->


<!-- =====================================
        JAVASCRIPT
====================================== -->

<script>

function toggleUserMenu()
{
    document
        .getElementById('userMenu')
        .classList
        .toggle('show-menu');
}

window.onclick = function(event)
{
    if(!event.target.closest('.user-dropdown'))
    {
        document
            .getElementById('userMenu')
            .classList
            .remove('show-menu');
    }
}

</script>

</body>

</html>