<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reset password | SpotTour</title>
    <link rel="stylesheet" href="{{ asset('css/style.css') }}">
</head>
<body class="auth-page auth-refresh">
<main class="auth-layout">
    <section class="auth-intro">
        <a class="auth-brand" href="/">Spot<span>Tour</span></a>
        <div>
            <span class="eyebrow">Account recovery</span>
            <h1>Reset your password securely.</h1>
            <p>We send a six-digit code to your registered email. The code expires after 15 minutes.</p>
        </div>
        <div class="auth-feature-list">
            <span>Email verification</span><span>15-minute code</span><span>Private to your account</span>
        </div>
    </section>
    <section class="auth-panel">
        @if(session('success'))<div class="auth-alert success">{{ session('success') }}</div>@endif
        @if($errors->any())
            <div class="auth-alert error"><ul>@foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul></div>
        @endif

        @if(request('email'))
            <div class="auth-panel-header">
                <span class="eyebrow">Step 2 of 2</span>
                <h2>Enter your reset code</h2>
                <p>Check {{ request('email') }} for the six-digit code.</p>
            </div>
            <form method="POST" action="/password/reset" class="auth-form">
                @csrf
                <label>Email address<input type="email" name="email" value="{{ old('email', request('email')) }}" required></label>
                <label>Six-digit code<input type="text" inputmode="numeric" pattern="[0-9]{6}" maxlength="6" name="code" placeholder="000000" required></label>
                <div class="auth-two-col">
                    <label>New password<input type="password" name="password" minlength="8" placeholder="At least 8 characters" required></label>
                    <label>Confirm password<input type="password" name="password_confirmation" minlength="8" placeholder="Repeat password" required></label>
                </div>
                <button type="submit">Change password</button>
                <p class="auth-switch"><a href="/password/forgot">Send a new code</a></p>
            </form>
        @else
            <div class="auth-panel-header">
                <span class="eyebrow">Step 1 of 2</span>
                <h2>Find your account</h2>
                <p>Enter the email address you use to sign in.</p>
            </div>
            <form method="POST" action="/password/code" class="auth-form">
                @csrf
                <label>Email address<input type="email" name="email" value="{{ old('email') }}" placeholder="Email address" required autofocus></label>
                <button type="submit">Send reset code</button>
                <p class="auth-switch"><a href="/">Back to sign in</a></p>
            </form>
        @endif
    </section>
</main>
</body>
</html>
