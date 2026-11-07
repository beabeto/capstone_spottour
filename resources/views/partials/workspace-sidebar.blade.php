@php
    $isAdminWorkspace = $role === 'admin';
    $base = $isAdminWorkspace ? '/admin' : '/owner';
    $currentPath = '/'.trim(request()->path(), '/');
    $active = fn (string $url) => $currentPath === $url || ($url !== $base && str_starts_with($currentPath, $url.'/'));
    $businessType = Auth::user()?->business_type;
    $canManageSpots = in_array($businessType, ['spot_cottage', 'spot_hotel', 'spot_food', 'spot_hotel_food'], true);
    $canManageHotels = in_array($businessType, ['hotel_inn_lounge', 'spot_hotel', 'hotel_food', 'spot_hotel_food'], true);
    $canManageFoods = in_array($businessType, ['food_establishment', 'spot_food', 'hotel_food', 'spot_hotel_food'], true);
    $canSeeRatings = in_array($businessType, ['spot_cottage', 'spot_hotel', 'spot_food', 'spot_hotel_food', 'hotel_inn_lounge', 'hotel_food'], true);
@endphp

<aside class="{{ $isAdminWorkspace ? 'sidebar' : 'owner-sidebar' }} workspace-sidebar workspace-standalone-sidebar" id="workspaceStandaloneSidebar" aria-label="{{ $isAdminWorkspace ? 'Admin' : 'Owner' }} navigation">
    <div class="sidebar-logo">
        <div class="logo-header">
            <h2>Spot<span>Tour</span></h2>
            <button type="button" class="menu-toggle workspace-sidebar-toggle" aria-label="Close navigation" aria-expanded="true" onclick="toggleStandaloneWorkspaceSidebar()"><span></span></button>
        </div>
    </div>

    <ul class="{{ $isAdminWorkspace ? 'sidebar-menu' : 'owner-menu' }}">
        @if($isAdminWorkspace)
            @foreach([
                ['/admin', 'Dashboard', 'dashboard-nav'],
                ['/admin/spots', 'Spots', 'spots-nav'],
                ['/admin/hotels', 'Hotels', 'hotels-nav'],
                ['/admin/foods', 'Restaurants', 'foods-nav'],
                ['/admin/users', 'Travellers', 'users-nav'],
                ['/admin/owners', 'Owners', 'owners-nav'],
                ['/admin/bookings', 'Bookings', 'bookings-nav'],
                ['/admin/day-plans', 'Day plans', 'calendar-nav'],
                ['/admin/reports', 'Visitor report', 'calendar-nav'],
                ['/admin/calendar', 'Calendar', 'calendar-nav'],
                ['/admin/feedback', 'Feedback', 'feedback-nav'],
                ['/admin/chats', 'Traveller chat', ''],
            ] as [$url, $label, $class])
                <li><a href="{{ $url }}" class="workspace-nav {{ $class }} {{ $active($url) ? 'is-active' : '' }}"><i></i><span>{{ $label }}</span></a></li>
            @endforeach
            <li><a href="/logout" class="logout-link workspace-nav logout-nav"><i></i><span>Logout</span></a></li>
        @else
            <li><a href="/owner" class="workspace-nav dashboard-nav {{ $active('/owner') ? 'is-active' : '' }}"><i></i><span>Dashboard</span></a></li>
            @if($canManageSpots)<li><a href="/owner/spots" class="workspace-nav spots-nav {{ $active('/owner/spots') ? 'is-active' : '' }}"><i></i><span>Spots</span></a></li>@endif
            @if($canManageHotels)<li><a href="/owner/hotels" class="workspace-nav hotels-nav {{ $active('/owner/hotels') ? 'is-active' : '' }}"><i></i><span>Hotels</span></a></li>@endif
            @if($canManageFoods)<li><a href="/owner/foods" class="workspace-nav foods-nav {{ $active('/owner/foods') ? 'is-active' : '' }}"><i></i><span>Restaurants</span></a></li>@endif
            <li><a href="/owner/packages" class="workspace-nav {{ $active('/owner/packages') ? 'is-active' : '' }}"><i></i><span>Packages</span></a></li>
            <li><a href="/owner/day-plans" class="workspace-nav calendar-nav {{ $active('/owner/day-plans') ? 'is-active' : '' }}"><i></i><span>Day plans</span></a></li>
            <li><a href="/owner/bookings" class="workspace-nav bookings-nav {{ $active('/owner/bookings') ? 'is-active' : '' }}"><i></i><span>Bookings</span></a></li>
            <li><a href="/owner/calendar" class="workspace-nav calendar-nav {{ $active('/owner/calendar') ? 'is-active' : '' }}"><i></i><span>Calendar</span></a></li>
            @if($canSeeRatings)<li><a href="/owner/reviews" class="workspace-nav reviews-nav {{ $active('/owner/reviews') ? 'is-active' : '' }}"><i></i><span>Ratings</span></a></li>@endif
            <li><a href="/owner/feedback" class="workspace-nav feedback-nav {{ $active('/owner/feedback') ? 'is-active' : '' }}"><i></i><span>Feedback</span></a></li>
            <li><a href="/owner/chats" class="workspace-nav {{ $active('/owner/chats') ? 'is-active' : '' }}"><i></i><span>Traveller chat</span></a></li>
            <li><a href="/logout" class="logout-link workspace-nav logout-nav"><i></i><span>Logout</span></a></li>
        @endif
    </ul>
</aside>
