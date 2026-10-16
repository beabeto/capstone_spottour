<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Owner Profile</title>
<link rel="stylesheet" href="{{ asset('css/style.css') }}">
<link rel="stylesheet" href="{{ asset('css/account-pages.css') }}">
</head>
<body class="dashboard-body account-page">

@include('user.partials.account-bar')

@if(session('success'))
<div class="toast-success">{{ session('success') }}</div>
@endif

<div class="profile-container">

    <header class="account-hero">
        <p class="account-kicker">Your account</p>
        <h1>Profile</h1>
        <p>Update your photo, contact details, payout information, and password.</p>
    </header>

    <div class="profile-card">

        <div class="profile-header">
            @if(Auth::user()->profile_image)
                <img src="{{ asset('uploads/profile/' . Auth::user()->profile_image) }}" class="profile-image" alt="">
            @else
                <img src="https://cdn-icons-png.flaticon.com/512/3135/3135715.png" class="profile-image" alt="">
            @endif

            <div class="profile-identity">
                <h2>{{ Auth::user()->name }}</h2>
                <p>{{ Auth::user()->email }}</p>
                <span class="profile-role">{{ $storeName ?: 'Business owner' }}</span>
                @if(Auth::user()->business_type)
                    <span class="profile-business">{{ ucwords(str_replace('_', ' ', Auth::user()->business_type)) }}</span>
                @endif
                <small>Member since {{ Auth::user()->created_at->format('F d, Y') }}</small>
            </div>
        </div>

        <form action="/profile/update" method="POST" enctype="multipart/form-data">
            @csrf

            <h3>Profile information</h3>

            <label>Profile photo
                <input type="file" name="profile_image" accept="image/jpeg,image/png,image/webp">
            </label>
            @error('profile_image')<p class="form-error">{{ $message }}</p>@enderror

            <label>Full name
                <input type="text" name="name" value="{{ old('name', Auth::user()->name) }}" placeholder="Full name" required>
            </label>
            @error('name')<p class="form-error">{{ $message }}</p>@enderror

            <label>Email
                <input type="email" name="email" value="{{ old('email', Auth::user()->email) }}" placeholder="Email" required>
            </label>
            @error('email')<p class="form-error">{{ $message }}</p>@enderror

            <div class="auth-two-col">
                <label>Gender
                    <select name="gender">
                        <option value="">Select gender</option>
                        <option value="male" @selected(old('gender', Auth::user()->gender) === 'male')>Male</option>
                        <option value="female" @selected(old('gender', Auth::user()->gender) === 'female')>Female</option>
                        <option value="prefer_not_to_say" @selected(old('gender', Auth::user()->gender) === 'prefer_not_to_say')>Prefer not to say</option>
                    </select>
                </label>
                <label>Birth date
                    <input type="date" name="birth_date" value="{{ old('birth_date', Auth::user()->birth_date) }}">
                </label>
            </div>

            <h3>Business information</h3>

            <label>Business type
                <input type="text" value="{{ Auth::user()->business_type ? ucwords(str_replace('_', ' ', Auth::user()->business_type)) : 'Not set' }}" readonly>
            </label>

            <h3>Payment information</h3>

            <div class="auth-two-col">
                <label>GCash number
                    <input type="text" name="gcash_number" value="{{ old('gcash_number', Auth::user()->gcash_number) }}" placeholder="09XXXXXXXXX">
                </label>
                <label>GCash account name
                    <input type="text" name="gcash_name" value="{{ old('gcash_name', Auth::user()->gcash_name) }}" placeholder="Account name">
                </label>
            </div>
            @error('gcash_number')<p class="form-error">{{ $message }}</p>@enderror
            @error('gcash_name')<p class="form-error">{{ $message }}</p>@enderror

            <label class="payment-qr-upload">
                <strong>GCash payment QR</strong>
                <span>Guests scan this image when they pay with GCash.</span>
                <input type="file" name="gcash_qr" accept="image/jpeg,image/png,image/webp">
            </label>
            @error('gcash_qr')<p class="form-error">{{ $message }}</p>@enderror

            @if(Auth::user()->gcash_qr)
                <img src="{{ asset('uploads/gcash-qr/'.Auth::user()->gcash_qr) }}" class="owner-gcash-qr" alt="Your GCash payment QR">
            @endif

            <div class="auth-two-col">
                <label>Bank name
                    <input type="text" name="bank_name" value="{{ old('bank_name', Auth::user()->bank_name) }}" placeholder="Bank name">
                </label>
                <label>Bank account number
                    <input type="text" name="bank_account_number" value="{{ old('bank_account_number', Auth::user()->bank_account_number) }}" placeholder="Account number">
                </label>
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
