<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard</title>

    <link rel="stylesheet" href="{{ asset('css/style.css') }}?v={{ filemtime(public_path('css/style.css')) }}">
</head>

@php
$user = Auth::user();
@endphp

<body class="admin-body">

<div class="admin-container">

    <!-- SIDEBAR -->

    <aside class="sidebar workspace-sidebar" id="sidebar">

       <div class="sidebar-logo">

    <div class="logo-header">

        <h2>Spot<span>Tour</span></h2>

        <button
            class="menu-toggle"
            onclick="toggleSidebar()"
            aria-label="Toggle sidebar"
            aria-expanded="true">

        </button>

    </div>



</div>

        <ul class="sidebar-menu">

            <li><a href="/admin" class="workspace-nav dashboard-nav"><i></i><span>Dashboard</span></a></li>
            <li><a href="/admin/spots" class="workspace-nav spots-nav"><i></i><span>Spots</span></a></li>
            <li><a href="/admin/hotels" class="workspace-nav hotels-nav"><i></i><span>Hotels</span></a></li>
            <li><a href="/admin/foods" class="workspace-nav foods-nav"><i></i><span>Restaurants</span></a></li>
            <li><a href="/admin/users" class="workspace-nav users-nav"><i></i><span>Travellers</span></a></li>
            <li><a href="/admin/owners" class="workspace-nav owners-nav"><i></i><span>Owners</span></a></li>
            <li><a href="/admin/bookings" class="workspace-nav bookings-nav"><i></i><span>Bookings</span></a></li>
            <li><a href="/admin/day-plans" class="workspace-nav calendar-nav"><i></i><span>Day plans</span></a></li>
            <li><a href="/admin/reports" class="workspace-nav calendar-nav"><i></i><span>Visitor report</span></a></li>
            <li><a href="/admin/calendar" class="workspace-nav calendar-nav"><i></i><span>Calendar</span></a></li>
            <li>

    <a href="/admin/feedback" class="workspace-nav feedback-nav">

        <i></i>



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
            <li><a href="/admin/chats" class="workspace-nav"><i></i><span>Traveller chat</span>@php $chatWaiting = \App\Models\ChatThread::where('assignee_role', 'admin')->where('staff_unread', '>', 0)->count(); @endphp@if($chatWaiting > 0)<span class="menu-badge">{{ $chatWaiting }}</span>@endif</a></li>
            <li><a href="/logout" onclick="localStorage.removeItem('spottour-admin-welcome-closed')" class="logout-link workspace-nav logout-nav"><i></i><span>Logout</span></a></li>

        </ul>

    </aside>

    <!-- MAIN CONTENT -->

    <main class="admin-main">

        <!-- TOPBAR -->

<div class="topbar-right">
    <button type="button" class="header-menu" onclick="toggleSidebar()" aria-label="Open menu"></button>
    <div class="workspace-title"><h1>Admin Dashboard</h1><p class="workspace-subtitle">Control center</p></div>

    <div class="topbar-actions">

    @include('partials.notification-menu', [
        'notifications' => $notifications,
        'unreadCount' => $unreadCount,
        'allUrl' => '/admin/notifications',
        'pendingSummary' => [
            ['url' => '/admin/bookings', 'label' => $newBookings.' bookings waiting'],
            ['url' => '/admin/spots', 'label' => $newSpots.' spots to review'],
            ['url' => '/admin/hotels', 'label' => $newHotels.' hotels to review'],
            ['url' => '/admin/foods', 'label' => $newFoods.' restaurants to review'],
            ['url' => '/admin/feedback', 'label' => $newFeedback.' feedback notes'],
        ],
    ])

    <div class="admin-user account-chip is-admin" role="link" tabindex="0" onclick="window.location='/admin/profile'" onkeydown="if(event.key==='Enter')window.location='/admin/profile'">

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

            <small class="admin-role">Administrator</small>

        </div>

    </div>

    </div>

</div>

        <section class="owner-welcome owner-welcome-top" id="adminWelcome">
            <button type="button" class="welcome-close" aria-label="Close welcome message" onclick="closeAdminWelcome()">&times;</button>
            <h2>Welcome back, {{ $user->name }}!</h2>
            <p>Review listings, bookings, and feedback from your administrator workspace.</p>
        </section>

        <!-- CARDS -->

        @php
            $spotBookingTotal = $bookingPlaces->where('kind', 'Tourist spot')->sum('bookings');
            $hotelBookingTotal = $bookingPlaces->where('kind', 'Hotel')->sum('bookings');
            $foodBookingTotal = $bookingPlaces->where('kind', 'Restaurant')->sum('bookings');
        @endphp
        <div class="dashboard-cards">

            <a class="stat-card" href="/admin/users">
                <h3>Travellers</h3>
                <p>{{ $totalUsers }}</p>
                <small>Traveller accounts only. Owners are not included here.</small>
            </a>

            <a class="stat-card" href="/admin/owners">
                <h3>Total Owners</h3>
                <p>{{ $totalOwners }}</p>
                <small>Business accounts that manage spots, hotels, or restaurants.</small>
            </a>

            <a class="stat-card" href="/admin/bookings">
                <h3>Total Bookings</h3>
                <p>{{ $totalBookings }}</p>
                <small>{{ $spotBookingTotal }} spots · {{ $hotelBookingTotal }} hotels · {{ $foodBookingTotal }} restaurants</small>
            </a>

            <a class="stat-card" href="/admin/feedback">
                <h3>Feedback</h3>
                <p>{{ $newFeedback }}</p>
                <small>Messages that still need a reply.</small>
            </a>

        </div>

        <section class="place-bookings" aria-label="Bookings by establishment">
            <div class="section-title"><h2>Bookings by establishment</h2></div>
            <div class="table-card">
                <table>
                    <thead><tr><th>Establishment</th><th>Type</th><th>Bookings</th><th>Arrived</th><th>Units</th><th>Approved revenue</th></tr></thead>
                    <tbody>
                    @forelse($bookingPlaces as $place)
                        <tr>
                            <td><strong>{{ $place['name'] }}</strong></td>
                            <td>{{ $place['kind'] }}</td>
                            <td>{{ $place['bookings'] }}</td>
                            <td>{{ $place['arrived'] }}</td>
                            <td>{{ $place['units'] }}</td>
                            <td>PHP {{ number_format($place['revenue'], 2) }}</td>
                        </tr>
                    @empty
                        <tr><td colspan="6">No bookings yet.</td></tr>
                    @endforelse
                    </tbody>
                </table>
            </div>
        </section>

        <section class="admin-revenue" aria-label="Revenue">
            <div>
                <span>Today</span>
                <strong>PHP {{ number_format($todayRevenue, 2) }}</strong>
            </div>
            <div>
                <span>This month</span>
                <strong>PHP {{ number_format($monthlyRevenue, 2) }}</strong>
            </div>
            <div>
                <span>Total revenue</span>
                <strong>PHP {{ number_format($totalRevenue, 2) }}</strong>
            </div>
        </section>

        <section class="dashboard-section">
            <div class="section-header">
                <h2>Upcoming reservations</h2>
                <a href="/admin/calendar">View calendar</a>
            </div>
            <div class="schedule-list">
                @forelse($upcomingBookings as $booking)
                    <a class="schedule-card" href="/admin/bookings">
                        <div class="schedule-date">
                            <h3>{{ \Carbon\Carbon::parse($booking->date)->format('d') }}</h3>
                            <span>{{ \Carbon\Carbon::parse($booking->date)->format('M') }}</span>
                        </div>
                        <div class="schedule-content">
                            <h4>{{ $booking->customer }}</h4>
                            <p>{{ $booking->listing }}</p>
                            <small>{{ $booking->category }} · {{ \Carbon\Carbon::parse($booking->date)->format('l') }}</small>
                        </div>
                        <div class="schedule-status">
                            <span class="status {{ $booking->status }}">{{ ucfirst(str_replace('_', ' ', $booking->status)) }}</span>
                        </div>
                    </a>
                @empty
                    <div class="empty-review">No upcoming reservations.</div>
                @endforelse
            </div>
        </section>

        <section class="dashboard-section">
            <div class="section-header">
                <h2>Traveller day plans</h2>
                <a href="/admin/day-plans">View all</a>
            </div>
            <div class="schedule-list">
                @forelse($dayPlans as $plan)
                    <a class="schedule-card" href="/admin/day-plans">
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
                            <small>{{ $plan->note ?: 'Surigao day plan' }}</small>
                        </div>
                    </a>
                @empty
                    <div class="empty-review">No upcoming day plans yet.</div>
                @endforelse
            </div>
        </section>

        <div class="approval-section">

    <div class="section-title">

        <h2>

             Pending Approvals

        </h2>

    </div>

    <div class="approval-grid">

        <a href="/admin/spots" class="approval-card">

            <h3>

                 Spots

            </h3>

            <span>

                {{ $pendingSpots }}

            </span>

        </a>

        <a href="/admin/hotels" class="approval-card">

            <h3>

                 Hotels

            </h3>

            <span>

                {{ $pendingHotels }}

            </span>

        </a>



        <a href="/admin/foods" class="approval-card">

            <h3>

                 Restaurants

            </h3>

            <span>

                {{ $pendingFoods }}

            </span>

        </a>



    </div>

</div>

<div class="dashboard-graphs">

    <div class="graph-card">

        <div class="graph-heading"><h2>Monthly Registrations</h2><select class="analytics-period" aria-label="Registration reporting period"><option>This month</option><option>This year</option></select></div>

        <canvas id="registrationChart"></canvas>

    </div>

    <div class="graph-card">

        <div class="graph-heading"><h2>Booking Analytics</h2><select class="analytics-period" aria-label="Booking reporting period"><option>All listings</option><option>Approved only</option></select></div>

        <canvas id="bookingChart"></canvas>

    </div>

    <div class="graph-card">

        <div class="graph-heading"><h2>Monthly Revenue</h2><select class="analytics-period" aria-label="Revenue reporting period"><option>This year</option><option>This month</option><option>Today</option></select></div>

        <canvas id="revenueChart"></canvas>

    </div>

</div>

<div class="top-performers">

    <div class="section-title">

        <h2>

             Top Performing Listings

        </h2>

    </div>

    <div class="performer-grid">

        <!-- Top Spot -->

        <div class="performer-card">

            <h3> Top Tourist Spot</h3>

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

            <h3> Top Hotel</h3>

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

            <h3> Top Restaurant</h3>

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

         Recent Activities

    </h2>

    <a href="/admin/activities" class="view-all-btn">

        View All

    </a>

</div>

    <div class="activity-list">

        @forelse($activities as $activity)
        @include('admin.partials.activity-row', ['activity' => $activity])
        @empty

        <div class="empty-state">

            No recent activities.

        </div>

        @endforelse

    </div>

        <footer class="dashboard-footer">
            <div class="footer-left">
                <h3>Spot<span>Tour</span></h3>
                <p>Explore - Book - Travel</p>
            </div>
            <div class="footer-right">
                <a href="/admin/spots">Tourist Spots</a>
                <a href="/admin/hotels">Hotels</a>
                <a href="/admin/foods">Restaurants</a>
                <a href="/admin/feedback">Feedback</a>
            </div>
        </footer>
    </main>

</div>

<script>

function closeAdminWelcome(){ document.getElementById('adminWelcome')?.remove(); localStorage.setItem('spottour-admin-welcome-closed','1'); }
if(localStorage.getItem('spottour-admin-welcome-closed') === '1') document.getElementById('adminWelcome')?.remove();

function toggleSidebar()
{
    const sidebar = document.getElementById('sidebar');
    const trigger = document.querySelector('.menu-toggle');
    if (window.matchMedia('(max-width: 900px)').matches) {
        sidebar.classList.toggle('show');
        trigger.setAttribute('aria-expanded', sidebar.classList.contains('show'));
    } else {
        sidebar.classList.toggle('collapsed');
        trigger.setAttribute('aria-expanded', !sidebar.classList.contains('collapsed'));
    }
}

document.querySelectorAll('.sidebar-menu .workspace-nav').forEach((link) => {
    const path = new URL(link.href).pathname;
    const current = window.location.pathname;
    if (current === path || (path !== '/admin' && current.startsWith(path + '/'))) link.classList.add('is-active');
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

            label:'Travellers',
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

                        return 'PHP '+value.toLocaleString();

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
