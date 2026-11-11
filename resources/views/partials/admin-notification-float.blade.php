@if(auth()->check() && auth()->user()->role === 'admin')
    <div class="admin-bell-float">
        @include('partials.notification-menu', ['allUrl' => '/admin/notifications'])
    </div>
@endif
