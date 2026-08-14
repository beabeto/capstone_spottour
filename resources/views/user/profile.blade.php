```blade
<!DOCTYPE html>

<html lang="en">
<head>

<meta charset="UTF-8">

<meta name="viewport"
content="width=device-width, initial-scale=1.0">

<title>My Profile</title>

<link rel="stylesheet"
href="{{ asset('css/style.css') }}">

</head>

<body class="dashboard-body">

<div class="back-btn">

    <button onclick="history.back()">

        ←

    </button>

</div>

@if(session('success'))

<div class="toast-success">

    {{ session('success') }}

</div>

@endif

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

            <span class="profile-role">

                {{ ucfirst(Auth::user()->role) }}

            </span>

            @if(Auth::user()->business_type)

            <span class="profile-business">

                {{ str_replace('_',' ',Auth::user()->business_type) }}

            </span>

            @endif

            <small>

                Member Since
                {{ Auth::user()->created_at->format('F d, Y') }}

            </small>

        </div>

        <form
        action="/profile/update"
        method="POST"
        enctype="multipart/form-data">

            @csrf

            <h3>

                Profile Information

            </h3>

            <input
            type="file"
            name="profile_image">

            <input
            type="text"
            name="name"
            value="{{ Auth::user()->name }}"
            placeholder="Full Name">

            <input
            type="email"
            name="email"
            value="{{ Auth::user()->email }}"
            placeholder="Email">

            <h3>

                Change Password

            </h3>

            <input
            type="password"
            name="password"
            placeholder="New Password">

            <button type="submit">

                💾 Save Changes

            </button>

        </form>

    </div>

    <!-- PROFILE STATS -->

    <div class="profile-stats">

        <div class="profile-box">

            <h3>

                📖 Bookings

            </h3>

            <span>

                {{ $bookings ?? 0 }}

            </span>

        </div>

        <div class="profile-box">

            <h3>

                ❤️ Favorites

            </h3>

            <span>

                {{ $favorites ?? 0 }}

            </span>

        </div>

        <div class="profile-box">

            <h3>

                🔖 Bookmarks

            </h3>

            <span>

                {{ $bookmarks ?? 0 }}

            </span>

        </div>

        <div class="profile-box">

    <h3>

        ✅ Checklist

    </h3>

    <span>

        {{ $checklists ?? 0 }}

    </span>

</div>

        <div class="profile-box">

            <h3>

                ⭐ Reviews

            </h3>

            <span>

                {{ $reviews ?? 0 }}

            </span>

        </div>

    </div>

</div>

</body>

</html>
```
