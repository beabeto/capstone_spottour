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

    <aside class="owner-sidebar" id="ownerSidebar">

        <div class="sidebar-logo">

    <div class="logo-header">

        <h2>SpotTour</h2>

        <button
            class="menu-toggle"
            onclick="toggleOwnerSidebar()">
            ☰
        </button>

    </div>



        <ul class="owner-menu">

            <li>
                <a href="{{ url('/owner') }}">
                    🏠
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
                <a href="/owner/spots">
                    📍
                    <span>My Spots</span>
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

<a href="/owner/hotels">

🏨

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

    <a href="/owner/foods">

        🍽

        <span>

            Food Establishments

        </span>

    </a>

</li>

@endif

            <li>
                <a href="/owner/bookings">

    📖 Bookings

    @if($pendingBookings > 0)

        <span class="menu-badge">

            {{ $pendingBookings }}

        </span>

    @endif

</a>
           

<li>

<a href="/owner/calendar">

📅

<span>

Reservation Calendar

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
    <a href="/owner/reviews">

    ⭐ Reviews

    @if($totalReviews > 0)

<span class="menu-badge">

    {{ $totalReviews }}

</span>

@endif

</a>
</li>

@endif

            <li>
                <a href="/owner/feedback">
                    💬
                    <span>Feedback</span>
                </a>
            </li>

            <li>
                <a href="owner/profile">
                    👤
                    <span>Profile</span>
                </a>
            </li>

            <li>
                <a href="/logout">
                    🚪
                    <span>Logout</span>
                </a>
            </li>

        </ul>

    </aside>

    <!-- MAIN -->

    <main class="owner-main">

        <div class="owner-topbar">

            <h1>
                Owner Dashboard
            </h1>

            <div class="admin-user">

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

        <small class="admin-role">
            {{ ucfirst(Auth::user()->role) }}
        </small>

    </div>

</div>

        </div>


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
                <h3>Reviews</h3>
                <p>{{ $totalReviews }}</p>
            </div>

            <div class="owner-card revenue-card">

    <div class="revenue-icon">

        💰

    </div>

    <div>

        <small>

            Total Revenue

        </small>

        <h2>

            ₱{{ number_format($totalRevenue,2) }}

        </h2>

        <span class="growth-text">

            Approved bookings only

        </span>

    </div>

</div>

        </div>

       

        <div class="dashboard-graphs">

    <div class="graph-card">

        <h3>📊 Monthly Bookings</h3>

        <canvas id="bookingChart"></canvas>

    </div>

    <div class="graph-card">

        <h3>💰 Monthly Revenue</h3>

        <canvas id="revenueChart"></canvas>

    </div>

</div>

<div class="dashboard-section">

<div class="section-header">

<h2>

📅 Upcoming Reservations

</h2>

<a href="/owner/calendar">

View Calendar →

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

•

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

        <h2>

            📖 Latest Bookings

        </h2>

        <a href="/owner/bookings">

            View All →

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

                            📍 Spot

                        @elseif(isset($booking->hotel))

                            🏨 Hotel

                        @elseif(isset($booking->food))

                            🍽 Restaurant

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

            ⭐ Recent Reviews

        </h2>

        <a href="/owner/reviews">

            View All →

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

                    📍 {{ $review->spot->name }}

                @elseif(isset($review->hotel))

                    🏨 {{ $review->hotel->name }}

                @elseif(isset($review->food))

                    🍽 {{ $review->food->name }}

                @endif

            </div>

            <div class="review-stars">

                ⭐

                {{ number_format(
                    $review->ratings->avg('rating') ?? 0,
                    1
                ) }}

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

🏆 Business Performance

</h2>

</div>

<div class="performance-grid">

<div class="performance-card">

<h3>

📍 Best Spot

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

🏨 Best Hotel

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

🍽 Best Restaurant

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

✅ Completion Rate

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

        <div class="owner-welcome">

            <h2>

Welcome back,

{{ Auth::user()->name }}

👋

</h2>

<p>

Manage your business, monitor bookings, track revenue, and respond to customer reviews from one place.

</p>

<p>

<strong>

Business Type:

</strong>

{{ ucfirst(str_replace('_',' ',Auth::user()->business_type)) }}

</p>
        </div>

    </main>

</div>

<script>

function toggleOwnerSidebar()
{
    document
        .getElementById('ownerSidebar')
        .classList
        .toggle('collapsed');
}

</script>
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<script>

const bookingChart =
document.getElementById('bookingChart');

new Chart(bookingChart,{

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

const revenueChart =
document.getElementById('revenueChart');

if(revenueChart){

new Chart(revenueChart,{

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

}

</script>

</body>
</html>