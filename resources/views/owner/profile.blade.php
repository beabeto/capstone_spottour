<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport"
content="width=device-width, initial-scale=1.0">

<title>Owner Profile</title>

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

            <span class="owner-badge">
                Business Owner
            </span>

        </div>

        <form
            action="/profile/update"
            method="POST"
            enctype="multipart/form-data">

            @csrf

            <h3>Owner Information</h3>

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

            <h3>Business Information</h3>

            <input
            type="text"
            value="{{ Auth::user()->business_type }}"
            readonly>

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

    <!-- OWNER STATS -->

    <div class="profile-stats">

        <div class="owner-stat-box">
            <h3>Total Listings</h3>
            <span>0</span>
        </div>

        <div class="owner-stat-box">
            <h3>Total Bookings</h3>
            <span>0</span>
        </div>

        <div class="owner-stat-box">
            <h3>Reviews</h3>
            <span>0</span>
        </div>

        <div class="owner-stat-box">
            <h3>Revenue</h3>
            <span>₱0</span>
        </div>

    </div>

    <!-- BUSINESS TYPE -->

    <div class="business-card">

        <h2>Business Information</h2>

        <table class="owner-table">

            <tr>
                <td><strong>Owner Name</strong></td>
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
                <td><strong>Business Type</strong></td>
                <td>{{ Auth::user()->business_type }}</td>
            </tr>

        </table>

        <h3>Payment Information</h3>

<div class="form-group">

    <label>GCash Number</label>

    <input
    type="text"
    name="gcash_number"
    value="{{ Auth::user()->gcash_number }}">

</div>

<div class="form-group">

    <label>GCash Account Name</label>

    <input
    type="text"
    name="gcash_name"
    value="{{ Auth::user()->gcash_name }}">

</div>

<div class="form-group">

    <label>Bank Name</label>

    <input
    type="text"
    name="bank_name"
    value="{{ Auth::user()->bank_name }}">

</div>

<div class="form-group">

    <label>Bank Account Number</label>

    <input
    type="text"
    name="bank_account_number"
    value="{{ Auth::user()->bank_account_number }}">

</div>

    </div>

    <div class="back-btn">

        <a href="/owner">
            ← Back to Dashboard
        </a>

    </div>

</div>

</body>
</html>