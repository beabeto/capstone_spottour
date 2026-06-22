<!DOCTYPE html>
<html lang="en">
<head>

<meta charset="UTF-8">

<meta name="viewport"
content="width=device-width, initial-scale=1.0">

<title>My Notifications</title>

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

<h1>

    🔔 Notifications

</h1>

@forelse($notifications as $notification)

<div class="notification-card {{ !$notification->is_read ? 'unread' : '' }}">

    <a

    href="
    @if($notification->hotel_id)

        /hotels/{{ $notification->hotel_id }}

    @elseif($notification->spot_id)

        /spots/{{ $notification->spot_id }}

    @else

        #

    @endif
    "

    class="notification-link">

        <div class="notification-left">

            <img

            src="{{ $notification->sender && $notification->sender->profile_image
                ? asset('uploads/profile/'.$notification->sender->profile_image)
                : 'https://cdn-icons-png.flaticon.com/512/3135/3135715.png' }}"

            class="notification-avatar">

        </div>

        <div class="notification-content">

            <div class="notification-badge">

                @if($notification->hotel_id)

                    🏨 Hotel Notification

                @elseif($notification->spot_id)

                    📍 Spot Notification

                @else

                    🔔 Notification

                @endif

            </div>

            <p class="notification-message">

                {{ $notification->message }}

            </p>

            <small class="notification-time">

                {{ $notification->created_at->diffForHumans() }}

            </small>

        </div>

        <div class="notification-arrow">

            →

        </div>

    </a>

    <a

    href="/notification/delete/{{ $notification->id }}"

    class="delete-btn"

    onclick="return confirm('Delete this notification?')">

        🗑️

    </a>

</div>

@empty

<div class="empty-card">

    <h3>

        No notifications yet

    </h3>

    <p>

        New notifications will appear here.

    </p>

</div>

@endforelse

</div>

</body>

</html>