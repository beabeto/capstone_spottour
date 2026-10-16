@php
use Illuminate\Support\Str;
@endphp

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">

<title>Owner Dashboard</title>

<link rel="stylesheet" href="{{ asset('css/style.css') }}">

</head>
<body class="owner-body">

<div class="owner-container">

    <!-- SIDEBAR -->

    <aside class="owner-sidebar workspace-sidebar" id="ownerSidebar">

        <div class="sidebar-logo">

    <div class="logo-header">

        <h2>Spot<span>Tour</span></h2>

        <button
            class="menu-toggle"
            onclick="toggleOwnerSidebar()"
            aria-label="Toggle sidebar"
            aria-expanded="true">

        </button>

    </div>

</div>

        <ul class="owner-menu">

            <li>
                <a href="{{ url('/owner') }}" class="workspace-nav dashboard-nav"><i></i>

                    <span>Dashboard</span>
                </a>
            </li>

            @if(
                Auth::user()->business_type == 'spot_cottage' ||
                Auth::user()->business_type == 'spot_hotel' ||
                Auth::user()->business_type == 'spot_food' ||
                Auth::user()->business_type == 'spot_hotel_food'
            )

            <li>
                <a href="/owner/spots" class="workspace-nav spots-nav"><i></i>

                    <span>Spots</span>
                </a>
            </li>

            @endif

            @if(
                Auth::user()->business_type == 'hotel_inn_lounge' ||
                Auth::user()->business_type == 'spot_hotel' ||
                Auth::user()->business_type == 'hotel_food' ||
                Auth::user()->business_type == 'spot_hotel_food'
            )

            <li>

<a href="/owner/hotels" class="workspace-nav hotels-nav"><i></i>



<span>

Hotels

</span>

</a>

</li>

            @endif

            @if(
    Auth::user()->business_type == 'food_establishment' ||
    Auth::user()->business_type == 'spot_food' ||
    Auth::user()->business_type == 'hotel_food' ||
    Auth::user()->business_type == 'spot_hotel_food'
)

<li>

    <a href="/owner/foods" class="workspace-nav foods-nav"><i></i>



        <span>

            Restaurants

        </span>

    </a>

</li>

@endif

            <li>
                <a href="/owner/packages" class="workspace-nav"><i></i>
                    <span>Packages</span>
                </a>
            </li>

            <li>
                <a href="/owner/day-plans" class="workspace-nav calendar-nav"><i></i>
                    <span>Day plans</span>
                </a>
            </li>
            <li>
                <a href="/owner/bookings" class="workspace-nav bookings-nav"><i></i>
                    <span>Bookings</span>
                    @if($pendingBookings > 0)
                        <span class="menu-badge">{{ $pendingBookings }}</span>
                    @endif
                </a>
            </li>

<li>

<a href="/owner/calendar" class="workspace-nav calendar-nav"><i></i>



<span>

Calendar

</span>

</a>

</li>

            @if(
    Auth::user()->business_type == 'spot_cottage' ||
    Auth::user()->business_type == 'spot_hotel' ||
    Auth::user()->business_type == 'spot_food' ||
    Auth::user()->business_type == 'spot_hotel_food' ||

    Auth::user()->business_type == 'hotel_inn_lounge' ||
    Auth::user()->business_type == 'hotel_food'
)

<li>
    <a href="/owner/reviews" class="workspace-nav reviews-nav"><i></i>

     <span>Ratings</span>

    @if($totalReviews > 0)

<span class="menu-badge">

    {{ $totalReviews }}

</span>

@endif

</a>
</li>

@endif

            <li>
                <a href="/owner/feedback" class="workspace-nav feedback-nav"><i></i>

                    <span>Feedback</span>
                </a>
            </li>

            <li>
                <a href="/owner/chats" class="workspace-nav"><i></i>
                    <span>Traveller chat</span>
                    @php $chatWaiting = \App\Models\ChatThread::where('assignee_role', 'owner')->where('assignee_id', Auth::id())->where('staff_unread', '>', 0)->count(); @endphp
                    @if($chatWaiting > 0)<span class="menu-badge">{{ $chatWaiting }}</span>@endif
                </a>
            </li>

            <li>
                <a href="/logout" onclick="localStorage.removeItem('spottour-owner-welcome-closed')" class="logout-link workspace-nav logout-nav"><i></i>

                    <span>Logout</span>
                </a>
            </li>

        </ul>

    </aside>

    <!-- MAIN -->

    <main class="owner-main">

        <div class="owner-topbar">

            <button type="button" class="header-menu" onclick="toggleOwnerSidebar()" aria-label="Open menu"></button>
            <div class="workspace-title">
                <h1>Owner Dashboard</h1>
                <p class="store-indicator"><span>Your business</span>{{ ucfirst(str_replace('_', ' ', Auth::user()->business_type)) }}</p>
            </div>

            <div class="topbar-actions">
            @include('partials.notification-menu', ['notifications' => $notifications, 'unreadCount' => $unreadCount, 'allUrl' => '/owner/notifications'])
            <div class="admin-user account-chip is-owner" role="link" tabindex="0" onclick="window.location='/owner/profile'" onkeydown="if(event.key==='Enter')window.location='/owner/profile'">

    @if(Auth::user()->profile_image)

        <img
            src="{{ asset('uploads/profile/' . Auth::user()->profile_image) }}"
            alt="Profile">

    @else

        <img
            src="https://cdn-icons-png.flaticon.com/512/3135/3135715.png"
            alt="Profile">

    @endif

    <div>

        <span class="admin-name">
            {{ Auth::user()->name }}
        </span>

        <small class="admin-role">Owner</small>

    </div>

</div>
            </div>

        </div>

        <section class="owner-welcome owner-welcome-top" id="ownerWelcome">
            <button type="button" class="welcome-close" aria-label="Close welcome message" onclick="closeOwnerWelcome()">&times;</button>
            <h2>Welcome back, {{ Auth::user()->name }}!</h2>
            <p>Manage your business, track revenue, and respond to booking requests from one place.</p>
        </section>


        <div class="owner-cards">

            <div class="owner-card">
                <h3>Total Listings</h3>
                <p>{{ $listings }}</p>
            </div>

            <div class="owner-card">
                <h3>Total Bookings</h3>
                <p>{{ $totalBookings }}</p>
            </div>

            <div class="owner-card">
                <h3>Pending Requests</h3>
                <p>{{ $pendingBookings }}</p>
            </div>

            <div class="owner-card">
                <h3>Ratings</h3>
                <p>{{ $totalReviews }}</p>
            </div>

            <div class="owner-card revenue-card">

    <div>

        <small>

            Total Revenue

        </small>

        <h2>

            PHP {{ number_format($totalRevenue,2) }}

        </h2>

        <span class="growth-text">

            Approved bookings only

        </span>

    </div>

</div>

        </div>



        <div class="dashboard-graphs">

    <div class="graph-card">

        <div class="graph-heading"><h3 id="bookingGraphTitle">This Month's Bookings</h3><select id="bookingPeriod" aria-label="Booking period"><option value="month" selected>This month</option><option value="year">This year</option></select></div>

        <canvas id="bookingChart"></canvas>

    </div>

    <div class="graph-card">

        <div class="graph-heading"><h3 id="revenueGraphTitle">This Year's Revenue</h3><select id="revenuePeriod" aria-label="Revenue period"><option value="today">Today</option><option value="week">This week</option><option value="month">This month</option><option value="year" selected>This year</option></select></div>

        <canvas id="revenueChart"></canvas>

    </div>

</div>

<div class="dashboard-section">

<div class="section-header">

<h2>

 Upcoming Reservations

</h2>

<a href="/owner/calendar">

View Calendar

</a>

</div>

<div class="schedule-list">

@forelse($upcomingBookings as $booking)

<div class="schedule-card">

<div class="schedule-date">

<h3>

{{ \Carbon\Carbon::parse($booking->date)->format('d') }}

</h3>

<span>

{{ \Carbon\Carbon::parse($booking->date)->format('M') }}

</span>

</div>

<div class="schedule-content">

<h4>

{{ $booking->customer }}

</h4>

<p>

{{ $booking->listing }}

</p>

<small>

{{ ucfirst($booking->category) }}

-

{{ \Carbon\Carbon::parse($booking->date)->format('l') }}

</small>

</div>

<div class="schedule-status">

<span class="status {{ $booking->status }}">

{{ ucfirst($booking->status) }}

</span>

</div>

</div>

@empty

<div class="empty-review">

No upcoming reservations.

</div>

@endforelse

</div>

</div>

<div class="dashboard-section">
    <div class="section-header">
        <h2>Guest day plans</h2>
        <a href="/owner/day-plans">View all</a>
    </div>
    <div class="schedule-list">
        @forelse(($dayPlans ?? collect()) as $plan)
            <div class="schedule-card">
                <div class="schedule-date">
                    <h3>{{ $plan->plan_date->format('d') }}</h3>
                    <span>{{ $plan->plan_date->format('M') }}</span>
                </div>
                <div class="schedule-content">
                    <h4>{{ $plan->user?->name ?? 'Traveller' }}</h4>
                    <p>
                        {{ $plan->spot?->name ? 'Spot: '.$plan->spot->name : '' }}
                        {{ $plan->food?->name ? ' · Meal: '.$plan->food->name : '' }}
                        {{ $plan->hotel?->name ? ' · Stay: '.$plan->hotel->name : '' }}
                    </p>
                    <small>{{ $plan->note ?: 'Included your listing' }}</small>
                </div>
            </div>
        @empty
            <div class="empty-review">No travellers have included your listings in a day plan yet.</div>
        @endforelse
    </div>
</div>

<div class="dashboard-section">

    <div class="section-header">

        <h2>

             Latest Bookings

        </h2>

        <a href="/owner/bookings">

            View All

        </a>

    </div>

    <div class="dashboard-table">

        <table>

            <thead>

                <tr>

                    <th>Customer</th>

                    <th>Listing</th>

                    <th>Category</th>

                    <th>Status</th>

                    <th>Date</th>

                </tr>

            </thead>

            <tbody>

            @forelse($latestBookings as $booking)

                <tr>

                    <td>

                        {{ $booking->user->name ?? 'Guest' }}

                    </td>

                    <td>

                        @if(isset($booking->spot))

                            {{ $booking->spot->name }}

                        @elseif(isset($booking->hotel))

                            {{ $booking->hotel->name }}

                        @elseif(isset($booking->food))

                            {{ $booking->food->name }}

                        @endif

                    </td>

                    <td>

                        @if(isset($booking->spot))

                             Spot

                        @elseif(isset($booking->hotel))

                             Hotel

                        @elseif(isset($booking->food))

                             Restaurant

                        @endif

                    </td>

                    <td>

                        <span class="status {{ $booking->status }}">

                            {{ ucfirst($booking->status) }}

                        </span>

                    </td>

                    <td>

                        {{ $booking->created_at->format('M d, Y') }}

                    </td>

                </tr>

            @empty

                <tr>

                    <td colspan="5">

                        No bookings yet.

                    </td>

                </tr>

            @endforelse

            </tbody>

        </table>

    </div>


</div>

<div class="dashboard-section">

    <div class="section-header">

        <h2>

             Recent ratings

        </h2>

        <a href="/owner/reviews">

            View All

        </a>

    </div>

    <div class="reviews-grid">

        @forelse($latestReviews as $review)

        <div class="review-card">

            <div class="review-top">

                <img

                src="{{ $review->user && $review->user->profile_image
                    ? asset('uploads/profile/'.$review->user->profile_image)
                    : 'https://cdn-icons-png.flaticon.com/512/3135/3135715.png' }}"

                class="review-avatar">

                <div>

                    <h4>

                        {{ $review->user->name }}

                    </h4>

                    <small>

                        {{ $review->created_at->diffForHumans() }}

                    </small>

                </div>

            </div>

            <div class="review-listing">

                @if(isset($review->spot))

                     {{ $review->spot->name }}

                @elseif(isset($review->hotel))

                     {{ $review->hotel->name }}

                @elseif(isset($review->food))

                     {{ $review->food->name }}

                @endif

            </div>

            <div class="review-stars">
                @php
                    $listing = $review->spot ?? $review->hotel ?? $review->food ?? null;
                    $guestScore = $listing?->ratings?->firstWhere('user_id', $review->user_id)?->rating
                        ?? $review->ratings->avg('rating')
                        ?? 0;
                @endphp
                @include('partials.score-stars', ['score' => $guestScore])
            </div>

            <p class="review-message">

                "{{ Str::limit($review->comment,120) }}"

            </p>

        </div>

        @empty

        <div class="empty-review">

            No reviews yet.

        </div>

        @endforelse

    </div>

</div>

<div class="dashboard-section">

<div class="section-header">

<h2>

 Business Performance

</h2>

</div>

<div class="performance-grid">

<div class="performance-card">

<h3>

 Best Spot

</h3>

@if($topSpot)

<h2>

{{ $topSpot->name }}

</h2>

<p>

{{ $topSpot->bookings_count }}

Bookings

</p>

@else

<p>

No Spot

</p>

@endif

</div>

<div class="performance-card">

<h3>

 Best Hotel

</h3>

@if($topHotel)

<h2>

{{ $topHotel->name }}

</h2>

<p>

{{ $topHotel->bookings_count }}

Bookings

</p>

@else

<p>

No Hotel

</p>

@endif

</div>

<div class="performance-card">

<h3>

 Best Restaurant

</h3>

@if($topFood)

<h2>

{{ $topFood->name }}

</h2>

<p>

{{ $topFood->bookings_count }}

Bookings

</p>

@else

<p>

No Restaurant

</p>

@endif

</div>

<div class="performance-card">

<h3>

 Completion Rate

</h3>

<h1>

{{ $completionRate }}%

</h1>

<p>

Approved Bookings

</p>

</div>

</div>

</div>

        <footer class="dashboard-footer">
            <div class="footer-left">
                <h3>Spot<span>Tour</span></h3>
                <p>Explore - Book - Travel</p>
            </div>
            <div class="footer-right">
                <a href="/owner/spots">Tourist Spots</a>
                <a href="/owner/hotels">Hotels</a>
                <a href="/owner/foods">Restaurants</a>
                <a href="/owner/feedback">Feedback</a>
            </div>
        </footer>

    </main>

</div>

<script>

function toggleOwnerSidebar()
{
    const sidebar = document.getElementById('ownerSidebar');
    const trigger = document.querySelector('.menu-toggle');
    if (window.matchMedia('(max-width: 900px)').matches) {
        sidebar.classList.toggle('show');
        trigger.setAttribute('aria-expanded', sidebar.classList.contains('show'));
    } else {
        sidebar.classList.toggle('collapsed');
        trigger.setAttribute('aria-expanded', !sidebar.classList.contains('collapsed'));
    }
}

function closeOwnerWelcome(){ document.getElementById('ownerWelcome')?.remove(); localStorage.setItem('spottour-owner-welcome-closed','1'); }
if(localStorage.getItem('spottour-owner-welcome-closed') === '1') document.getElementById('ownerWelcome')?.remove();

document.querySelectorAll('.owner-menu .workspace-nav').forEach((link) => {
    const path = new URL(link.href).pathname;
    const current = window.location.pathname;
    if (current === path || (path !== '/owner' && current.startsWith(path + '/'))) link.classList.add('is-active');
});

</script>
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<script>

const bookingChart =
document.getElementById('bookingChart');

const ownerBookingChart = new Chart(bookingChart,{

    type:'bar',

    data:{

        labels:@json($bookingLabels),

        datasets:[{

            label:'Bookings',

            data:@json($bookingData),

            backgroundColor:'#2563eb',

            borderRadius:8

        }]

    },

    options:{

        responsive:true,

        plugins:{

            legend:{

                display:false

            }

        }

    }

});

document.getElementById('bookingPeriod')?.addEventListener('change', async (event) => {
    const response = await fetch('/owner/booking-period?period=' + encodeURIComponent(event.target.value), {headers:{Accept:'application/json'}});
    if (!response.ok) return;
    const data = await response.json();
    ownerBookingChart.data.labels = data.labels;
    ownerBookingChart.data.datasets[0].label = data.label;
    ownerBookingChart.data.datasets[0].data = data.data;
    ownerBookingChart.update();
    document.getElementById('bookingGraphTitle').textContent = data.label;
});

const revenueChart =
document.getElementById('revenueChart');

if(revenueChart){

const ownerRevenueChart = new Chart(revenueChart,{

    type:'line',

    data:{

        labels:@json($revenueLabels),

        datasets:[{

            label:'Revenue',

            data:@json($revenueData),

            borderColor:'#10b981',

            backgroundColor:'rgba(16,185,129,.2)',

            fill:true,

            tension:.4

        }]

    },

    options:{

        responsive:true,

        plugins:{

            legend:{
                display:false
            }

        },

        scales:{

            y:{

                beginAtZero:true

            }

        }

    }

});

document.getElementById('revenuePeriod')?.addEventListener('change', async (event) => {
    const period = event.target.value;
    const response = await fetch('/owner/revenue-period?period=' + encodeURIComponent(period), {headers:{Accept:'application/json'}});
    if (!response.ok) return;
    const data = await response.json();
    ownerRevenueChart.data.labels = data.labels;
    ownerRevenueChart.data.datasets[0].label = data.label;
    ownerRevenueChart.data.datasets[0].data = data.data;
    ownerRevenueChart.update();
    document.getElementById('revenueGraphTitle').textContent = data.label;
});

}

</script>

</body>
</html>
