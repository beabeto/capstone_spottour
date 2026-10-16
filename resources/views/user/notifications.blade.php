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

    <button type="button" onclick="window.history.back(); return false;">Back</button>

</div>

<div class="dashboard-container notification-page">

<div class="notification-page-header">
    <div>
        <span class="eyebrow">Inbox</span>
        <h1>Notifications</h1>
        <p>Updates about your bookings, saved places, and account activity.</p>
    </div>
    <a href="/user" class="notification-dashboard-btn">Back to dashboard</a>
</div>

@forelse($notifications as $notification)

<div class="notification-card {{ !$notification->is_read ? 'unread' : '' }}">

    <a

    href="/notification/{{ $notification->id }}"

    class="notification-link">
    
        <div class="notification-left">

            <img

            src="{{ $notification->sender && $notification->sender->profile_image
                ? asset('uploads/profile/'.$notification->sender->profile_image)
                : 'https://cdn-icons-png.flaticon.com/512/3135/3135715.png' }}"

            class="notification-avatar">

        </div>

        <div class="notification-content">

            <div class="notification-type">

                @if($notification->category === 'booking' || str_contains(strtolower($notification->message), 'booking') || str_contains(strtolower($notification->message), 'reservation') || str_contains(strtolower($notification->message), 'payment'))

     Booking

@elseif($notification->food_id)

     Restaurant Notification

@elseif($notification->hotel_id)

     Hotel Notification

@elseif($notification->spot_id)

     Spot Notification

@elseif(str_contains(strtolower($notification->message), 'feedback'))

     Feedback Reply

@else

     Notification

@endif

            </div>

            <p class="notification-message">

                {{ $notification->message }}

            </p>

            <small class="notification-time">

                {{ $notification->created_at->diffForHumans() }}

            </small>

        </div>

    </a>

    <a
    href="/notification/delete/{{ $notification->id }}"
    class="delete-btn"
    aria-label="Delete notification"
    onclick="return confirm('Delete this notification?')">
        <svg viewBox="0 0 24 24" aria-hidden="true">
            <path d="M9 3h6l1 2h4v2H4V5h4l1-2zm-2 6h10l-.8 11H7.8L7 9z" fill="currentColor"/>
        </svg>
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
