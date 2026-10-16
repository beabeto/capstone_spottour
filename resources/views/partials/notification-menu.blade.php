@php
    $noteCount = $unreadCount ?? 0;
    $noteItems = $notifications ?? collect();
    $noteAll = $allUrl ?? '/user/notifications';
@endphp
<div class="notification">
    <button type="button" class="notification-icon" onclick="toggleNotificationMenu()" aria-label="View notifications" aria-expanded="false" aria-controls="notificationMenu">
        &#128276;
        @if($noteCount > 0)
            <span class="badge">{{ $noteCount }}</span>
        @endif
    </button>
    <div class="notification-menu" id="notificationMenu">
        <div class="notification-menu-header">
            <strong>Notifications</strong>
            <a href="{{ $noteAll }}">View all</a>
        </div>
        @if(!empty($pendingSummary))
            <div class="notification-menu-pending">
                @foreach($pendingSummary as $item)
                    <a href="{{ $item['url'] }}">{{ $item['label'] }}</a>
                @endforeach
            </div>
        @endif
        <div class="notification-menu-list">
            @forelse($noteItems as $notification)
                @php
                    $note = strtolower($notification->message);
                    if ($notification->category === 'chat') {
                        $noteTitle = 'Chat';
                    } elseif ($notification->category === 'listing') {
                        $noteTitle = 'Listing';
                    } elseif (str_contains($note, 'confirmed')) {
                        $noteTitle = 'Booking confirmed';
                    } elseif (str_contains($note, 'booking') || str_contains($note, 'reservation') || str_contains($note, 'payment') || str_contains($note, 'paid')) {
                        $noteTitle = 'Booking update';
                    } elseif ($notification->food_id) {
                        $noteTitle = 'Restaurant update';
                    } elseif ($notification->hotel_id) {
                        $noteTitle = 'Hotel update';
                    } elseif ($notification->spot_id) {
                        $noteTitle = 'Spot update';
                    } else {
                        $noteTitle = 'Account update';
                    }
                @endphp
                <a href="/notification/{{ $notification->id }}" class="notification-menu-item {{ !$notification->is_read ? 'unread-notification' : '' }}">
                    <span class="notification-menu-dot {{ !$notification->is_read ? 'is-unread' : '' }}"></span>
                    <span>
                        <strong>{{ $noteTitle }}</strong>
                        <small>{{ $notification->message }}</small>
                    </span>
                </a>
            @empty
                <p class="notification-menu-empty">You are all caught up.</p>
            @endforelse
        </div>
    </div>
</div>
