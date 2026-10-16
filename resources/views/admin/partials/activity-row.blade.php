@php
    $kind = $activity['kind'] ?? 'account';
    $kindLabel = match ($kind) {
        'booking' => 'Booking',
        'spot' => 'Spot',
        'hotel' => 'Hotel',
        'restaurant' => 'Restaurant',
        default => 'Account',
    };
@endphp
<article class="activity-card">
    <div class="activity-avatar">
        @if(!empty($activity['profile_image']))
            <img src="{{ asset('uploads/profile/'.$activity['profile_image']) }}" alt="">
        @else
            <div class="activity-icon">{{ $activity['icon'] }}</div>
        @endif
    </div>
    <div class="activity-content">
        <h4>{{ $activity['title'] }}</h4>
        <p>{{ $activity['message'] }}</p>
    </div>
    <div class="activity-meta">
        <span class="activity-kind {{ $kind }}">{{ $kindLabel }}</span>
        <small>{{ \Carbon\Carbon::parse($activity['date'])->diffForHumans() }}</small>
    </div>
</article>
