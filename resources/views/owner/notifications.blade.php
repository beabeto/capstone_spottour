<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Owner notifications | SpotTour</title>
    <link rel="stylesheet" href="{{ asset('css/style.css') }}">
</head>
<body class="dashboard-body">
<div class="back-btn"><button type="button" onclick="window.history.back()">Back</button></div>
<div class="dashboard-container notification-page">
    <div class="notification-page-header">
        <div>
            <span class="eyebrow">Inbox</span>
            <h1>Notifications</h1>
            <p>Booking requests and payment updates for your listings. Open one to review it.</p>
        </div>
        <a href="/owner/bookings" class="notification-dashboard-btn">Review bookings</a>
    </div>
    @forelse($notifications as $notification)
        <div class="notification-card {{ !$notification->is_read ? 'unread' : '' }}">
            <a href="/notification/{{ $notification->id }}" class="notification-link">
                <div class="notification-content">
                    <div class="notification-type">{{ $notification->category === 'listing' ? 'Listing' : ($notification->category === 'chat' ? 'Chat' : ($notification->category === 'booking' ? 'Booking' : 'Update')) }}</div>
                    <p class="notification-message">{{ $notification->message }}</p>
                    <small class="notification-time">{{ $notification->created_at->diffForHumans() }}</small>
                </div>
            </a>
            <a href="/notification/delete/{{ $notification->id }}" class="delete-btn" aria-label="Delete notification" onclick="return confirm('Delete this notification?')">
                <svg viewBox="0 0 24 24" aria-hidden="true"><path d="M9 3h6l1 2h4v2H4V5h4l1-2zm-2 6h10l-.8 11H7.8L7 9z" fill="currentColor"/></svg>
            </a>
        </div>
    @empty
        <div class="empty-card"><h3>No notifications yet</h3><p>New booking requests will appear here.</p></div>
    @endforelse
</div>
</body>
</html>
