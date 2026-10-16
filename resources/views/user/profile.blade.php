<!DOCTYPE html>

<html lang="en">
<head>

<meta charset="UTF-8">

<meta name="viewport"
content="width=device-width, initial-scale=1.0">

<title>My Profile</title>

<link rel="stylesheet" href="{{ asset('css/style.css') }}">
<link rel="stylesheet" href="{{ asset('css/user-dashboard.css') }}">
<link rel="stylesheet" href="{{ asset('css/account-pages.css') }}">

</head>

<body class="dashboard-body account-page">

@include('user.partials.account-bar')

@if(session('success'))

<div class="toast-success">

    {{ session('success') }}

</div>

@endif

<div class="profile-container">

    <header class="account-hero">
        <p class="account-kicker">Your account</p>
        <h1>Profile</h1>
        <p>Update your photo, contact details, and password.</p>
    </header>

    <section class="profile-stats-card">
        <div class="profile-stats">
            <div class="profile-box"><span>{{ $bookings ?? 0 }}</span><h3>Bookings</h3></div>
            <div class="profile-box"><span>{{ $favorites ?? 0 }}</span><h3>Favorites</h3></div>
            <div class="profile-box"><span>{{ $bookmarks ?? 0 }}</span><h3>Bookmarks</h3></div>
            <div class="profile-box"><span>{{ $checklists ?? 0 }}</span><h3>Checklist</h3></div>
            <div class="profile-box"><span>{{ $reviews ?? 0 }}</span><h3>Reviews</h3></div>
        </div>
    </section>

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

            <div class="profile-identity">
                <h2>{{ Auth::user()->name }}</h2>
                <p>{{ Auth::user()->email }}</p>
                <span class="profile-role">{{ ucfirst(Auth::user()->role) }}</span>
                @if(Auth::user()->business_type)
                    <span class="profile-business">{{ str_replace('_',' ',Auth::user()->business_type) }}</span>
                @endif
                <small>Member since {{ Auth::user()->created_at->format('F d, Y') }}</small>
            </div>

        </div>

        <form
        action="/profile/update"
        method="POST"
        enctype="multipart/form-data">

            @csrf

            <h3>Profile information</h3>

            <label>Profile photo
                <input type="file" name="profile_image" accept="image/jpeg,image/png,image/webp">
            </label>

            @error('profile_image')<p class="form-error">{{ $message }}</p>@enderror

            <label>Full name
                <input type="text" name="name" value="{{ Auth::user()->name }}" placeholder="Full name" required>
            </label>

            <label>Email
                <input type="email" name="email" value="{{ Auth::user()->email }}" placeholder="Email" required>
            </label>

            <div class="auth-two-col">
                <label>Gender<select name="gender"><option value="">Select gender</option><option value="male" @selected(Auth::user()->gender === 'male')>Male</option><option value="female" @selected(Auth::user()->gender === 'female')>Female</option><option value="prefer_not_to_say" @selected(Auth::user()->gender === 'prefer_not_to_say')>Prefer not to say</option></select></label>
                <label>Birth date<input type="date" name="birth_date" value="{{ Auth::user()->birth_date }}"></label>
            </div>

            <div class="password-security-note">
                <h3>Change Password</h3>
                <p>For your security, SpotTour sends a six-digit code to your email before your password can be changed.</p>
                <a href="/password/forgot" class="view-btn">Change password with email code</a>
            </div>

            <button type="submit">Save changes</button>

        </form>

    </div>

</div>

</body>

</html>
