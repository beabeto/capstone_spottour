<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport"
content="width=device-width, initial-scale=1.0">

<title>Admin Profile</title>

<link rel="stylesheet"
href="{{ asset('css/style.css') }}">

</head>
<body class="dashboard-body">

<div class="back-btn">
    <button onclick="history.back()">
        ← 
    </button>
</div>

<div class="profile-container">

    <!-- PROFILE CARD -->

    <div class="profile-card">

        <div class="profile-header">

            @if(Auth::user()->profile_image)

                <img
                src="{{ asset('uploads/profile/' . Auth::user()->profile_image) }}"
                class="profile-image">

            @else

                <img
                src="https://cdn-icons-png.flaticon.com/512/3135/3135715.png"
                class="profile-image">

            @endif

            <h2>
                {{ Auth::user()->name }}
            </h2>

            <p>
                {{ Auth::user()->email }}
            </p>

            <span class="admin-badge">
                System Administrator
            </span>

        </div>

        <form
            action="/profile/update"
            method="POST"
            enctype="multipart/form-data">

            @csrf

            <h3>Administrator Information</h3>

            <input
            type="file"
            name="profile_image">

            <input
            type="text"
            name="name"
            value="{{ Auth::user()->name }}">

            <input
            type="email"
            name="email"
            value="{{ Auth::user()->email }}">

            <h3>Change Password</h3>

            <input
            type="password"
            name="password"
            placeholder="New Password">

            <button type="submit">
                Save Changes
            </button>

        </form>

    </div>

    
    <!-- SYSTEM INFO -->

    <div class="system-card">

        <h2>System Information</h2>

        <table class="admin-table">

            <tr>
                <td><strong>Name</strong></td>
                <td>{{ Auth::user()->name }}</td>
            </tr>

            <tr>
                <td><strong>Email</strong></td>
                <td>{{ Auth::user()->email }}</td>
            </tr>

            <tr>
                <td><strong>Role</strong></td>
                <td>{{ Auth::user()->role }}</td>
            </tr>

            <tr>
                <td><strong>System</strong></td>
                <td>SpotTour Booking Management System</td>
            </tr>

            <tr>
                <td><strong>Access Level</strong></td>
                <td>Full Administrator Access</td>
            </tr>

        </table>

    </div>

    

</div>

</body>
</html>