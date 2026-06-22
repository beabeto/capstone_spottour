<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Dashboard</title>

    <link rel="stylesheet"
    href="{{ asset('css/style.css') }}">
</head>
<body>

<nav class="navbar">

@php

$notifications = \App\Models\Notification::where(
    'user_id',
    Auth::id()
)
->latest()
->get();

$unreadCount = \App\Models\Notification::where(
    'user_id',
    Auth::id()
)
->where(
    'is_read',
    0
)
->count();

@endphp

    <div class="logo">
        <h2>SpotTour</h2>
    </div>

    <!-- MAIN MENU -->

    <ul class="nav-links">

        <li>
            <a href="/spots">
                Spot
            </a>
        </li>

        <li>
            <a href="/hotels">
                Hotel
            </a>
        </li>

        <li>
            <a href="/foods">
                Food
            </a>
        </li>

    </ul>

    <!-- RIGHT SIDE -->

    <div class="nav-right">

        <!-- NOTIFICATIONS -->

        <div class="notification">

    <a
href="/user/notifications"
class="notification-icon">

    🔔

    @if($unreadCount > 0)

<span class="badge">

    {{ $unreadCount }}

</span>

@endif

</a>

    <div
    class="notification-dropdown"
    id="notificationMenu">

        @forelse($notifications as $notification)

        <div class="notification-item">

            

            <p>

                {{ $notification->message }}

            </p>

            <small>

                {{ $notification->created_at->diffForHumans() }}

            </small>

        </div>

        @empty

        <div class="notification-item">

            No notifications yet.

        </div>

        @endforelse

    </div>

</div>

        <!-- USER DROPDOWN -->

        <div class="user-dropdown">

            <div
            class="user-info"
            onclick="toggleUserMenu()">

                @if(Auth::user()->profile_image)

                    <img
                    src="{{ asset('uploads/profile/' . Auth::user()->profile_image) }}"
                    alt="Profile">

                @else

                    <img
                    src="https://cdn-icons-png.flaticon.com/512/3135/3135715.png"
                    alt="Profile">

                @endif

                <span>

                    {{ Auth::user()->name }}

                </span>

            </div>

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

                <a href="/user/checklist">

                    ✅ Checklist

                </a>

                <a href="/user/bookmarks">

                    🔖 Bookmarks

                </a>

                <a href="/user/feedback">

                    💬 Feedback

                </a>

                <a href="/logout">

                    🚪 Logout

                </a>

            </div>

        </div>

    </div>

</nav>

<!-- CONTENT -->

<div class="content">

    <div class="welcome-card">

        <div class="card-content">

            <h1>

                Welcome,
                {{ Auth::user()->name }}

            </h1>

            <p>

                Discover tourist spots,
                hotels, cottages and
                food establishments.

            </p>

        </div>

    </div>

</div>

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

    if(!event.target.closest('.notification'))
    {
        document
            .getElementById('notificationMenu')
            .classList
            .remove('show-menu');
    }
}

function toggleNotifications()
{
    document
        .getElementById('notificationMenu')
        .classList
        .toggle('show-menu');
}

</script>

</body>
</html>