<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="UTF-8">

    <meta name="viewport"
          content="width=device-width, initial-scale=1.0">

    <title>SpotTour Dashboard</title>

    <link rel="stylesheet" href="{{ asset('css/style.css') }}">
    <link rel="stylesheet" href="{{ asset('css/user-dashboard.css') }}?v={{ filemtime(public_path('css/user-dashboard.css')) }}">
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css">

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

             <strong>Spot<span>Tour</span></strong>

        </a>

    </div>

    <!-- Search -->

    <div class="dashboard-search">

    <form action="/search" method="GET" autocomplete="off">

        <input
            type="text"
            name="keyword"
            value="{{ request('keyword') }}"
            placeholder="Search spots, hotels or restaurants"
            id="dashboardSearch"
            aria-expanded="false">

        <button type="submit" aria-label="Search">Search</button>

    </form>

    <div class="search-suggestions" id="searchSuggestions" role="listbox"></div>

</div>

    <!-- Menu -->

    <ul class="nav-links">

        <li>

            <a href="/spots">

                 Spots

            </a>

        </li>

        <li>

            <a href="/hotels">

                 Hotels

            </a>

        </li>

        <li>

            <a href="/foods">

                 Restaurants

            </a>

        </li>

        <li>

            <a href="/user/plan">

                 Day plan

            </a>

        </li>

    </ul>

    <!-- Right -->

    <div class="nav-right">

        <!-- Notification -->

        @include('partials.notification-menu', ['notifications' => $notifications, 'unreadCount' => $unreadCount, 'allUrl' => '/user/notifications'])

        <!-- Profile -->

        <div class="user-dropdown">

            <button type="button"
                class="user-info account-chip is-traveller"
                onclick="toggleUserMenu()"
                aria-expanded="false"
                aria-controls="userMenu">

                @if(Auth::user()?->profile_image)
                    <img src="{{ asset('uploads/profile/'.Auth::user()?->profile_image) }}" class="profile-mini" alt="">
                @else
                    <img src="https://cdn-icons-png.flaticon.com/512/3135/3135715.png" class="profile-mini" alt="">
                @endif

                <span class="account-chip-text">
                    <strong>{{ Auth::user()?->name }}</strong>
                    <small>Traveller</small>
                </span>
            </button>

            <div class="user-menu" id="userMenu">
                <div class="user-menu-profile">
                    @if(Auth::user()?->profile_image)
                        <img src="{{ asset('uploads/profile/'.Auth::user()?->profile_image) }}" alt="">
                    @else
                        <img src="https://cdn-icons-png.flaticon.com/512/3135/3135715.png" alt="">
                    @endif
                    <div>
                        <strong>{{ Auth::user()?->name }}</strong>
                        <small>{{ Auth::user()?->email }}</small>
                    </div>
                </div>

                <a href="/user/profile" class="menu-item profile-item">
                    <span class="menu-item-icon" aria-hidden="true"><svg viewBox="0 0 24 24"><circle cx="12" cy="8" r="3.2"/><path d="M5.5 19.2c1.4-2.8 3.7-4.2 6.5-4.2s5.1 1.4 6.5 4.2"/></svg></span>
                    <span class="menu-item-copy"><strong>Profile</strong><small>Name, photo, and account details</small></span>
                </a>
                <a href="/user/plan" class="menu-item bookings-item">
                    <span class="menu-item-icon" aria-hidden="true"><svg viewBox="0 0 24 24"><path d="M5 6.5h14M7 4.5v4M17 4.5v4M5 10h14v9.5H5z"/></svg></span>
                    <span class="menu-item-copy"><strong>Day plan</strong><small>Spot, meal, and stay on one map</small></span>
                </a>
                <a href="/user/bookings" class="menu-item bookings-item">
                    <span class="menu-item-icon" aria-hidden="true"><svg viewBox="0 0 24 24"><rect x="4" y="5" width="16" height="15" rx="2"/><path d="M8 3.5v3M16 3.5v3M4 10h16"/></svg></span>
                    <span class="menu-item-copy"><strong>My Bookings</strong><small>Reservations and PDF confirmations</small></span>
                </a>
                <a href="/user/favorites" class="menu-item favorites-item">
                    <span class="menu-item-icon" aria-hidden="true"><svg viewBox="0 0 24 24"><path d="M12 19.2s-6.2-3.8-6.2-8.1A3.5 3.5 0 0 1 12 8.4a3.5 3.5 0 0 1 6.2 2.7c0 4.3-6.2 8.1-6.2 8.1z"/></svg></span>
                    <span class="menu-item-copy"><strong>Favorites</strong><small>Places you liked</small></span>
                </a>
                <a href="/user/bookmarks" class="menu-item bookmarks-item">
                    <span class="menu-item-icon" aria-hidden="true"><svg viewBox="0 0 24 24"><path d="M7 4.5h10a1 1 0 0 1 1 1V20l-6-3.2L6 20V5.5a1 1 0 0 1 1-1z"/></svg></span>
                    <span class="menu-item-copy"><strong>Bookmarks</strong><small>Places saved for later</small></span>
                </a>
                <a href="/user/checklist" class="menu-item checklist-item">
                    <span class="menu-item-icon" aria-hidden="true"><svg viewBox="0 0 24 24"><rect x="4.5" y="4.5" width="15" height="15" rx="3"/><path d="M8 12.2l2.4 2.4L16 9.2"/></svg></span>
                    <span class="menu-item-copy"><strong>Checklist</strong><small>Places you plan to visit</small></span>
                </a>
                <a href="/user/feedback" class="menu-item feedback-item">
                    <span class="menu-item-icon" aria-hidden="true"><svg viewBox="0 0 24 24"><path d="M5 6.5h14a1 1 0 0 1 1 1v7.2a1 1 0 0 1-1 1H10l-4.2 3.2V7.5a1 1 0 0 1 1-1z"/></svg></span>
                    <span class="menu-item-copy"><strong>Feedback</strong><small>Message the administrator</small></span>
                </a>
                <a href="/logout" class="logout-menu menu-item" onclick="localStorage.removeItem('spottour-welcome-closed')">
                    <span class="menu-item-icon" aria-hidden="true"><svg viewBox="0 0 24 24"><path d="M10 5.5H6.5A1.5 1.5 0 0 0 5 7v10a1.5 1.5 0 0 0 1.5 1.5H10"/><path d="M13 8.5 16.5 12 13 15.5M9.5 12H16"/></svg></span>
                    <span class="menu-item-copy"><strong>Logout</strong><small>Sign out of SpotTour</small></span>
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

    <button type="button" class="welcome-close" onclick="closeWelcome()" aria-label="Close welcome message">&times;</button>

    <div class="welcome-content">

        <div>

            <p class="welcome-kicker">Your Surigao trip</p>

            <h1>

                Welcome Back,
                {{ Auth::user()?->name }}!

            </h1>

            <p>

                Discover amazing tourist destinations,
                comfortable hotels, and delicious restaurants
                around Surigao.

            </p>

        </div>

        @if(Auth::user()?->profile_image)

            <img
                src="{{ asset('uploads/profile/'.Auth::user()?->profile_image) }}"
                class="welcome-avatar">

        @else

            <img
                src="https://cdn-icons-png.flaticon.com/512/3135/3135715.png"
                class="welcome-avatar">

        @endif

    </div>

</div>


<section class="city-map" id="surigao-map">

    <div class="section-header">

        <div>

            <h2>Surigao City map</h2>

            <p>Pins for the spots, hotels, and restaurants listed on SpotTour.</p>

        </div>

        <select id="map-filter" class="map-filter" aria-label="Places to show on the map">
            <option value="all">All places</option>
            <option value="spot">Spots</option>
            <option value="hotel">Hotels</option>
            <option value="restaurant">Restaurants</option>
        </select>

    </div>

    <div class="city-map-card">

        <div id="place-map"></div>

        <p class="map-empty" hidden>No listed places have a map location yet.</p>

    </div>

</section>

<script type="application/json" id="place-pins">@json($mapPlaces)</script>


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

        @if($spots->first()?->image)
        <img src="{{ asset('uploads/spots/'.$spots->first()->image) }}" alt="{{ $spots->first()->name }}" class="explore-data-image">
        @endif

        <div class="explore-icon" aria-hidden="true">&#127754;</div>

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

            Explore Spots

        </a>

    </div>


    <!-- Hotels -->

    <div class="explore-card">

        @if($hotels->first()?->image)
        <img src="{{ asset('uploads/hotels/'.$hotels->first()->image) }}" alt="{{ $hotels->first()->name }}" class="explore-data-image">
        @endif

        <div class="explore-icon" aria-hidden="true">&#127976;</div>

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

            Explore Hotels

        </a>

    </div>


    <!-- Restaurants -->

    <div class="explore-card">

        @if($foods->first()?->image)
        <img src="{{ asset('uploads/foods/'.$foods->first()->image) }}" alt="{{ $foods->first()->name }}" class="explore-data-image">
        @endif

        <div class="explore-icon" aria-hidden="true">&#127860;</div>

        <h3>

            Restaurants

        </h3>

        <p>

            Discover restaurants,
            cafes,
            and local food establishments.

        </p>

        <a
            href="/foods"
            class="explore-btn">

            Explore Restaurants

        </a>

    </div>

</div>

@if(false)
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

        <div class="stat-icon" aria-hidden="true">&#128197;</div>

        <h2>

            {{ $bookings ?? 0 }}

        </h2>

        <p>

            Bookings

        </p>

    </div>

    <!-- Favorites -->

    <div class="stat-card">

        <div class="stat-icon" aria-hidden="true">&#9825;</div>

        <h2>

            {{ $favorites ?? 0 }}

        </h2>

        <p>

            Favorites

        </p>

    </div>

    <!-- Bookmarks -->

    <div class="stat-card">

        <div class="stat-icon" aria-hidden="true">&#128278;</div>

        <h2>

            {{ $bookmarks ?? 0 }}

        </h2>

        <p>

            Bookmarks

        </p>

    </div>

    <!-- Checklist -->

    <div class="stat-card">

        <div class="stat-icon" aria-hidden="true">&#10003;</div>

        <h2>

            {{ $checklists ?? 0 }}

        </h2>

        <p>

            Checklist

        </p>

    </div>

    <!-- Reviews -->

    <div class="stat-card">

        <div class="stat-icon" aria-hidden="true">&#9733;</div>

        <h2>

            {{ $reviews ?? 0 }}

        </h2>

        <p>

            Reviews

        </p>

    </div>

</div>

@endif

<!-- =====================================
        UPCOMING TRIPS
====================================== -->

<section class="dashboard-panel">
    <div class="section-header panel-header">
        <div>
            <h2>Upcoming bookings</h2>
            <p>Your next spot, hotel, and restaurant reservations.</p>
        </div>
        <a href="/user/bookings" class="view-all-link">View all</a>
    </div>

    @forelse(($upcomingBookings ?? collect($nextBooking ? [$nextBooking] : [])) as $booking)
        @php
            $image = match ($booking->type) {
                'spot' => $booking->spot?->image ? asset('uploads/spots/'.$booking->spot->image) : null,
                'hotel' => $booking->hotel?->image ? asset('uploads/hotels/'.$booking->hotel->image) : null,
                'food' => $booking->food?->image ? asset('uploads/foods/'.$booking->food->image) : null,
                default => null,
            };
            $kind = match ($booking->type) {
                'spot' => 'Tourist spot',
                'hotel' => 'Hotel',
                default => 'Restaurant',
            };
        @endphp
        <article class="trip-row">
            <div class="trip-thumb">
                @if($image)
                    <img src="{{ $image }}" alt="{{ $booking->listing }}">
                @else
                    <span class="trip-thumb-fallback">{{ strtoupper(substr($kind, 0, 1)) }}</span>
                @endif
            </div>
            <div class="trip-copy">
                <span class="trip-kind">{{ $kind }}</span>
                <h3>{{ $booking->listing }}</h3>
                <p>{{ \Carbon\Carbon::parse($booking->date)->format('M d, Y') }} · {{ ucfirst(str_replace('_', ' ', $booking->status)) }}</p>
            </div>
            <a href="{{ $booking->url ?? '/user/bookings' }}" class="trip-action">View</a>
        </article>
    @empty
        <div class="trip-empty">
            <h3>No upcoming booking</h3>
            <p>Book a tourist spot, hotel, or restaurant to start your trip.</p>
            <a href="/spots" class="explore-btn">Explore places</a>
        </div>
    @endforelse

    @if(! empty($nextPlan))
        <div class="panel-divider"></div>
        <div class="section-header panel-header is-compact">
            <div>
                <h2>Next day plan</h2>
                <p>Book each stop from your schedule.</p>
            </div>
            <a href="/user/plan" class="view-all-link">Open plan</a>
        </div>
        <article class="trip-row is-plan">
            <div class="trip-thumb is-plan-mark"><span>Plan</span></div>
            <div class="trip-copy">
                <span class="trip-kind">Day plan</span>
                <h3>{{ $nextPlan->plan_date->format('M d, Y') }}</h3>
                <p>
                    {{ collect([
                        $nextPlan->spot?->name ? 'Morning: '.$nextPlan->spot->name : null,
                        $nextPlan->food?->name ? 'Meal: '.$nextPlan->food->name : null,
                        $nextPlan->hotel?->name ? 'Stay: '.$nextPlan->hotel->name : null,
                    ])->filter()->implode(' · ') }}
                </p>
                @if($nextPlan->note)<p class="trip-note">{{ $nextPlan->note }}</p>@endif
            </div>
            <a href="/user/plan" class="trip-action">Open</a>
        </article>
    @endif
</section>

@if(false)
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

        View All

    </a>

</div>

@if($recentNotifications->count())

<div class="notification-list">

@foreach($recentNotifications as $notification)

<div class="notification-preview">

    <div class="notification-preview-icon">

        @if($notification->food_id)



        @elseif($notification->hotel_id)



        @elseif($notification->spot_id)



        @else



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



    </div>

    <h3>

        No Notifications

    </h3>

    <p>

        You're all caught up!

    </p>

</div>

@endif

@endif

<!-- =====================================
        FOOTER
====================================== -->

<footer class="dashboard-footer">

    <div class="footer-left">

        <h3>

             Spot<span>Tour</span>

        </h3>

        <p>

            Explore - Book - Travel

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

        <a href="#surigao-map">

            Map

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

<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>

<script>

function toggleUserMenu()
{
    const menu = document.getElementById('userMenu');
    const trigger = document.querySelector('.user-info');
    document.getElementById('notificationMenu').classList.remove('show-notification-menu');
    document.querySelector('.notification-icon').setAttribute('aria-expanded', 'false');
    menu.classList.toggle('show-menu');
    trigger.setAttribute('aria-expanded', menu.classList.contains('show-menu'));
}

function toggleNotificationMenu()
{
    const menu = document.getElementById('notificationMenu');
    const trigger = document.querySelector('.notification-icon');
    document.getElementById('userMenu').classList.remove('show-menu');
    document.querySelector('.user-info').setAttribute('aria-expanded', 'false');
    menu.classList.toggle('show-notification-menu');
    trigger.setAttribute('aria-expanded', menu.classList.contains('show-notification-menu'));
}

function closeWelcome()
{
    document.querySelector('.welcome-section')?.remove();
    localStorage.setItem('spottour-welcome-closed', '1');
}

if (localStorage.getItem('spottour-welcome-closed') === '1') {
    document.querySelector('.welcome-section')?.remove();
}

const dashboardSearch = document.getElementById('dashboardSearch');
const searchSuggestions = document.getElementById('searchSuggestions');
let searchTimer;

dashboardSearch?.addEventListener('input', function () {
    clearTimeout(searchTimer);
    const query = this.value.trim();
    searchSuggestions.replaceChildren();
    this.setAttribute('aria-expanded', 'false');
    if (!query) return;

    searchTimer = setTimeout(async () => {
        const response = await fetch('/search/suggestions?q=' + encodeURIComponent(query));
        const matches = await response.json();
        matches.forEach(match => {
            const link = document.createElement('a');
            link.href = match.url;
            link.innerHTML = '<strong></strong><small></small>';
            link.querySelector('strong').textContent = match.label;
            link.querySelector('small').textContent = match.type;
            searchSuggestions.appendChild(link);
        });
        searchSuggestions.classList.toggle('has-suggestions', matches.length > 0);
        dashboardSearch.setAttribute('aria-expanded', matches.length > 0 ? 'true' : 'false');
    }, 180);
});

document.querySelectorAll('.unread-notification').forEach(function(item)
{
    item.addEventListener('click', function()
    {
        const badge = document.querySelector('.notification-icon .badge');
        const dot = item.querySelector('.notification-menu-dot');

        if (dot) dot.classList.remove('is-unread');
        item.classList.remove('unread-notification');

        if (!badge) return;

        const remaining = Math.max(0, Number(badge.textContent.trim()) - 1);
        if (remaining) {
            badge.textContent = remaining;
        } else {
            badge.remove();
        }
    }, { once: true });
});

const placePins = JSON.parse(document.getElementById('place-pins')?.textContent || '[]');
const placeMapNode = document.getElementById('place-map');

if (placeMapNode && window.L) {
    const placeMap = L.map(placeMapNode, { scrollWheelZoom: false }).setView([9.789, 125.495], 12);

    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        maxZoom: 19,
        attribution: '&copy; OpenStreetMap'
    }).addTo(placeMap);

    placeMap.on('click', function () {
        placeMap.scrollWheelZoom.enable();
    });

    const pinColors = { spot: '#167b69', hotel: '#1d6f93', restaurant: '#c56a12' };

    function pinIcon(color) {
        return L.divIcon({
            className: 'place-pin',
            html: '<span style="background:' + color + '"></span>',
            iconSize: [28, 36],
            iconAnchor: [14, 36],
            popupAnchor: [0, -32]
        });
    }

    const mapMarkers = placePins.map(function (place) {
        const marker = L.marker([place.lat, place.lng], {
            icon: pinIcon(pinColors[place.type] || '#167b69')
        }).addTo(placeMap);

        const popup = document.createElement('div');
        popup.className = 'place-popup';

        const label = document.createElement('small');
        label.textContent = place.label;

        const title = document.createElement('strong');
        title.textContent = place.name;

        const address = document.createElement('p');
        address.textContent = place.location || '';

        const actions = document.createElement('div');
        const details = document.createElement('a');
        details.href = place.url;
        details.textContent = 'View place';

        const maps = document.createElement('a');
        maps.href = place.maps;
        maps.target = '_blank';
        maps.rel = 'noopener';
        maps.textContent = 'Google Maps';

        actions.append(details, maps);
        popup.append(label, title, address, actions);
        marker.bindPopup(popup);

        return { marker: marker, type: place.type };
    });

    function showPins(type) {
        const visible = [];

        mapMarkers.forEach(function (item) {
            const show = type === 'all' || item.type === type;

            if (show) {
                item.marker.addTo(placeMap);
                visible.push(item.marker);
            } else {
                placeMap.removeLayer(item.marker);
            }
        });

        if (visible.length) {
            placeMap.fitBounds(L.featureGroup(visible).getBounds().pad(0.25), { maxZoom: 14 });
        }
    }

    document.querySelector('.map-empty')?.toggleAttribute('hidden', placePins.length > 0);

    document.getElementById('map-filter')?.addEventListener('change', function () {
        showPins(this.value);
    });

    if (mapMarkers.length) {
        showPins('all');
    }

    setTimeout(function () { placeMap.invalidateSize(); }, 200);
}

window.onclick = function(event)
{
    if(!event.target.closest('.user-dropdown'))
    {
        document
            .getElementById('userMenu')
            .classList
            .remove('show-menu');
        document.querySelector('.user-info').setAttribute('aria-expanded', 'false');
    }

    if(!event.target.closest('.notification'))
    {
        document.getElementById('notificationMenu').classList.remove('show-notification-menu');
        document.querySelector('.notification-icon').setAttribute('aria-expanded', 'false');
    }
}

</script>

</body>

</html>
