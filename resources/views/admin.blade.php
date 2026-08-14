<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard</title>

    <link rel="stylesheet" href="{{ asset('css/style.css') }}">
</head>

@php
$user = Auth::user();
@endphp

<body class="admin-body">

<div class="admin-container">

    <!-- SIDEBAR -->

    <aside class="sidebar" id="sidebar">

       <div class="sidebar-logo">

    <div class="logo-header">

        <h2>SpotTour</h2>

        <button
            class="menu-toggle"
            onclick="toggleSidebar()">
            ☰
        </button>

    </div>

   

</div>

        <ul class="sidebar-menu">

            <li><a href="/admin">🏠 <span>Dashboard</span></a></li>
            <li><a href="/admin/spots">📍 <span>Spot</span></a></li>
            <li><a href="/admin/hotels">🏨 <span>Hotel</span></a></li>
            <li><a href="/admin/foods">🍽 <span>Food</span></a></li>
            <li><a href="/admin/users">👥 <span>Users</span></a></li>
            <li><a href="/admin/owners">🧑‍💼 <span>Owners</span></a></li>
            <li><a href="/admin/bookings">📖 <span>Booking</span></a></li>
            <li><a href="/admin/profile">👤 <span>Profile</span></a></li>
            <li>

    <a href="/admin/feedback">

        ⭐

        <span>

            Feedback

        </span>

        @if($newFeedback > 0)

        <span class="menu-badge">

            {{ $newFeedback }}

        </span>

        @endif

    </a>

</li>
            <li><a href="/logout">🚪 <span>Logout</span></a></li>

        </ul>

    </aside>

    <!-- MAIN CONTENT -->

    <main class="admin-main">

        <!-- TOPBAR -->

<div class="topbar-right">

    <div class="notification-wrapper">

        <button
            class="notification-bell"
            onclick="toggleNotifications(event)">

            🔔

            @if($totalNotifications > 0)

                <span class="notification-badge">

                    {{ $totalNotifications }}

                </span>

            @endif

        </button>

        <div
            id="notificationDropdown"
            class="notification-dropdown">

            <h4>Notifications</h4>

            <a href="/admin/users">
                👤 {{ $newUsers }} New Users
            </a>

            <a href="/admin/spots">
                📍 {{ $newSpots }} Pending Spots
            </a>

            <a href="/admin/hotels">
                🏨 {{ $newHotels }} Pending Hotels
            </a>

            <a href="/admin/foods">
                🍽 {{ $newFoods }} Pending Restaurants
            </a>

            <a href="/admin/bookings">
                📖 {{ $newBookings }} Pending Bookings
            </a>

            <a href="/admin/feedback">
                ⭐ {{ $newFeedback }} New Feedback
            </a>

        </div>

    </div>

    <div class="admin-user">

        @if($user && $user->profile_image)

            <img
            src="{{ asset('uploads/profile/'.$user->profile_image) }}"
            alt="Profile">

        @else

            <img
            src="https://cdn-icons-png.flaticon.com/512/3135/3135715.png"
            alt="Profile">

        @endif

        <div>

            <span class="admin-name">

                {{ $user->name }}

            </span>

            <small class="admin-role">

                {{ ucfirst($user->role) }}

            </small>

        </div>

    </div>

</div>

        <!-- CARDS -->

        <div class="dashboard-cards">

            <div class="stat-card">
                <h3>Total Users</h3>
    <p>
        {{ $totalUsers }}
    </p>
            </div>

            <div class="stat-card">
                <h3>Total Owners</h3>
    <p>
        {{ $totalOwners }}
    </p>
            </div>

            <div class="stat-card">
                <h3>Total Bookings</h3>
               <p> {{ $totalBookings }}</p>
            </div>

            <div class="stat-card">
                <h3>Feedback</h3>
                <p>{{ $newFeedback }}</p>
            </div>

            <div class="stat-card">

    <h3>💰 Today's Revenue</h3>

    <p>

        ₱{{ number_format($todayRevenue,2) }}

    </p>

</div>

<div class="stat-card">

    <h3>📅 Monthly Revenue</h3>

    <p>

        ₱{{ number_format($monthlyRevenue,2) }}

    </p>

</div>

<div class="stat-card">

    <h3>🏦 Total Revenue</h3>

    <p>

        ₱{{ number_format($totalRevenue,2) }}

    </p>

</div>
        </div>

        <div class="approval-section">

    <div class="section-title">

        <h2>

            ⏳ Pending Approvals

        </h2>

    </div>

    <div class="approval-grid">

        <a href="/admin/spots" class="approval-card">

            <h3>

                📍 Spots

            </h3>

            <span>

                {{ $pendingSpots }}

            </span>

        </a>

        <a href="/admin/hotels" class="approval-card">

            <h3>

                🏨 Hotels

            </h3>

            <span>

                {{ $pendingHotels }}

            </span>

        </a>

        

        <a href="/admin/foods" class="approval-card">

            <h3>

                🍽 Restaurants

            </h3>

            <span>

                {{ $pendingFoods }}

            </span>

        </a>

        

    </div>

</div>

<div class="dashboard-graphs">

    <div class="graph-card">

        <h2>

            👥 Monthly Registrations

        </h2>

        <canvas id="registrationChart"></canvas>

    </div>

</div>

       <div class="dashboard-graphs">

    <div class="graph-card">

        <h2>

            📖 Booking Analytics

        </h2>

        <canvas id="bookingChart"></canvas>

    </div>

</div>

<div class="dashboard-graphs">

    <div class="graph-card">

        <h2>

            💰 Monthly Revenue

        </h2>

        <canvas id="revenueChart"></canvas>

    </div>

</div>

<div class="top-performers">

    <div class="section-title">

        <h2>

            🏆 Top Performing Listings

        </h2>

    </div>

    <div class="performer-grid">

        <!-- Top Spot -->

        <div class="performer-card">

            <h3>📍 Top Tourist Spot</h3>

            @if($topSpot)

                @if($topSpot->image)

                    <img
                    src="{{ asset('uploads/spots/'.$topSpot->image) }}"
                    alt="{{ $topSpot->name }}">

                @endif

                <h4>{{ $topSpot->name }}</h4>

                <p>

                    {{ $topSpot->bookings_count }} Bookings

                </p>

            @else

                <p>No data available.</p>

            @endif

        </div>

        <!-- Top Hotel -->

        <div class="performer-card">

            <h3>🏨 Top Hotel</h3>

            @if($topHotel)

                @if($topHotel->image)

                    <img
                    src="{{ asset('uploads/hotels/'.$topHotel->image) }}"
                    alt="{{ $topHotel->name }}">

                @endif

                <h4>{{ $topHotel->name }}</h4>

                <p>

                    {{ $topHotel->bookings_count }} Bookings

                </p>

            @else

                <p>No data available.</p>

            @endif

        </div>

        <!-- Top Restaurant -->

        <div class="performer-card">

            <h3>🍽 Top Restaurant</h3>

            @if($topFood)

                @if($topFood->image)

                    <img
                    src="{{ asset('uploads/foods/'.$topFood->image) }}"
                    alt="{{ $topFood->name }}">

                @endif

                <h4>{{ $topFood->name }}</h4>

                <p>

                    {{ $topFood->bookings_count }} Reservations

                </p>

            @else

                <p>No data available.</p>

            @endif

        </div>

    </div>

</div>
        
<div class="section-title">

    <h2>

        🕒 Recent Activities

    </h2>

    <a href="/admin/activities" class="view-all-btn">

        View All →

    </a>

</div>

    <div class="activity-list">

        @forelse($activities as $activity)

        <div class="activity-card">

           <div class="activity-avatar">

@if(!empty($activity['profile_image']))

<img
src="{{ asset('uploads/profile/'.$activity['profile_image']) }}"
alt="Profile">

@else

<div class="activity-icon">

    {{ $activity['icon'] }}

</div>

@endif

</div>
            <div class="activity-content">

                <h4>

                    {{ $activity['title'] }}

                </h4>

                <p>

                    {{ $activity['message'] }}

                </p>

                <small>

                    {{ \Carbon\Carbon::parse($activity['date'])->diffForHumans() }}

                </small>

            </div>

        </div>

        @empty

        <div class="empty-state">

            No recent activities.

        </div>

        @endforelse

    </div>

</div>

    </main>

</div>

<script>

function toggleSidebar()
{
    document
        .getElementById('sidebar')
        .classList
        .toggle('collapsed');
}

function toggleNotifications(event){

    event.stopPropagation();

    document
        .getElementById('notificationDropdown')
        .classList
        .toggle('show');

}

document.addEventListener('click', function(){

    document
        .getElementById('notificationDropdown')
        .classList
        .remove('show');

});



</script>
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>


<script>

const registrationChart =
document.getElementById('registrationChart');

if(registrationChart){

new Chart(registrationChart,{

    type:'line',

    data:{

        labels:@json($registrationLabels),

        datasets:[

        {

            label:'Users',

            data:@json($userRegistrationData),

            borderColor:'#2563eb',

            backgroundColor:'rgba(37,99,235,.15)',

            fill:true,

            tension:.4

        },

        {

            label:'Owners',

            data:@json($ownerRegistrationData),

            borderColor:'#10b981',

            backgroundColor:'rgba(16,185,129,.15)',

            fill:true,

            tension:.4

        }

        ]

    },

    options:{

        responsive:true,

        plugins:{

            legend:{
                position:'bottom'
            }

        }

    }

});

}

const bookingChart =
document.getElementById('bookingChart');

if(bookingChart){

new Chart(bookingChart,{

    type:'bar',

    data:{

        labels:@json($bookingLabels),

        datasets:[{

            label:'Bookings',

            data:@json($bookingData),

            backgroundColor:[

                '#2563eb',

                '#10b981',

                '#f59e0b'

            ],

            borderRadius:10

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

            borderColor:'#22c55e',

            backgroundColor:'rgba(34,197,94,.15)',

            fill:true,

            tension:.4,

            pointRadius:5,

            pointHoverRadius:7

        }]

    },

    options:{

        responsive:true,

        plugins:{

            legend:{
                position:'bottom'
            }

        },

        scales:{

            y:{

                beginAtZero:true,

                ticks:{

                    callback:function(value){

                        return '₱'+value.toLocaleString();

                    }

                }

            }

        }

    }

});

}

</script>

</body>
</html>