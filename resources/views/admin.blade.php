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

    <div class="sidebar-profile">

    @if($user && $user->profile_image)

        <img
        src="{{ asset('uploads/profile/'.$user->profile_image) }}"
        alt="Profile">

    @else

        <img
        src="https://cdn-icons-png.flaticon.com/512/3135/3135715.png"
        alt="Profile">

    @endif

    <h4>

        {{ $user->name ?? 'Guest' }}

    </h4>

    <small>

        {{ ucfirst($user->role ?? 'User') }}

    </small>

</div>

</div>

        <ul class="sidebar-menu">

            <li><a href="#">🏠 <span>Dashboard</span></a></li>
            <li><a href="admin/spots">📍 <span>Spot</span></a></li>
            <li><a href="admin/hotels">🏨 <span>Hotel</span></a></li>
            <li><a href="admin/food">🍽 <span>Food</span></a></li>
            <li><a href="admin/users">👥 <span>Users</span></a></li>
            <li><a href="admin/owners">🧑‍💼 <span>Owners</span></a></li>
            <li><a href="admin/bookings">📖 <span>Booking</span></a></li>
            <li><a href="admin/profile">👤 <span>Profile</span></a></li>
            <li><a href="#">⭐ <span>Feedback</span></a></li>
            <li><a href="/logout">🚪 <span>Logout</span></a></li>

        </ul>

    </aside>

    <!-- MAIN CONTENT -->

    <main class="admin-main">

        <!-- TOPBAR -->

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

            {{ $user->name ?? 'Guest' }}

        </span>

        <small class="admin-role">

            {{ ucfirst($user->role ?? 'User') }}

        </small>

    </div>

</div>

        <!-- CARDS -->

        <div class="dashboard-cards">

            <div class="stat-card">
                <h3>Total Users</h3>
    <p>
        {{ \App\Models\User::where('role','user')->count() }}
    </p>
            </div>

            <div class="stat-card">
                <h3>Total Owners</h3>
    <p>
        {{ \App\Models\User::where('role','owner')->count() }}
    </p>
            </div>

            <div class="stat-card">
                <h3>Total Bookings</h3>
                <p>350</p>
            </div>

            <div class="stat-card">
                <h3>Feedback</h3>
                <p>89</p>
            </div>

        </div>

        <!-- TABLE -->

        <div class="recent-section">

            <h2>Recent Activities</h2>

            <table>

                <thead>
                    <tr>
                        <th>ID</th>
                        <th>User</th>
                        <th>Action</th>
                        <th>Date</th>
                    </tr>
                </thead>

                <tbody>

                    <tr>
                        <td>1</td>
                        <td>John Doe</td>
                        <td>Booked Hotel</td>
                        <td>2025-08-13</td>
                    </tr>

                    <tr>
                        <td>2</td>
                        <td>Jane Smith</td>
                        <td>Registered Account</td>
                        <td>2025-08-13</td>
                    </tr>

                </tbody>

            </table>

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

</script>
</body>
</html>