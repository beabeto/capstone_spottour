<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Welcome to SpotTour</title>
    <link rel="stylesheet" href="{{ asset('css/style.css') }}">
</head>
<body class="auth-page auth-refresh">
    <main class="auth-layout">
        <section class="auth-intro">
            <a class="auth-brand" href="/">Spot<span>Tour</span></a>
            <div>
                <span class="eyebrow">Plan with confidence</span>
                <h1>Discover, reserve, and enjoy the local experience.</h1>
                <p>One simple place for spots, stays, restaurants, and direct booking with local owners.</p>
            </div>
            <div class="auth-feature-list">
                <span>Verified booking details</span><span>Direct GCash payments</span><span>Clear owner updates</span>
            </div>
        </section>
        <section class="auth-panel" aria-live="polite">
            <div class="auth-panel-header"><span class="eyebrow" id="authKicker">Welcome back</span><h2 id="authTitle">Sign in to SpotTour</h2><p id="authSubtitle">Use your account to manage your bookings.</p></div>
            @if(session('error'))<div class="auth-alert error">{{ session('error') }}</div>@endif
            @if ($errors->any())<div class="auth-alert error"><ul>@foreach ($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul></div>@endif

            <form id="login" method="POST" action="/login" class="auth-form">
                @csrf
                <label>Email address<input type="email" name="email" value="{{ old('email') }}" placeholder="Email address" required autocomplete="email"></label>
                <label>Password<input type="password" name="password" placeholder="Enter your password" required autocomplete="current-password"></label>
                <button type="submit">Sign in <span>&rarr;</span></button>
                <p class="auth-switch"><a href="/password/forgot">Forgot your password?</a></p>
                <p class="auth-switch">New to SpotTour? <button type="button" class="text-button" onclick="showRegister()">Create an account</button></p>
            </form>

            <form id="register" method="POST" action="/register" class="auth-form hidden">
                @csrf
                <label>Full name<input type="text" name="name" value="{{ old('name') }}" placeholder="Your full name" required autocomplete="name"></label>
                <label>Email address<input type="email" name="email" value="{{ old('email') }}" placeholder="Email address" required autocomplete="email"></label>
                <div class="auth-two-col"><label>Gender<select name="gender" required><option value="">Select gender</option><option value="male" @selected(old('gender') === 'male')>Male</option><option value="female" @selected(old('gender') === 'female')>Female</option><option value="prefer_not_to_say" @selected(old('gender') === 'prefer_not_to_say')>Prefer not to say</option></select></label><label>Birth date<input type="date" name="birth_date" value="{{ old('birth_date') }}" required autocomplete="bday"></label></div>
                <label>Account type<select name="role" id="role" onchange="toggleBusinessType()" required><option value="">Select an account type</option><option value="user">Traveller</option><option value="owner">Business owner</option></select></label>
                <div id="businessSection" class="hidden"><label>Business type<select name="business_type" id="business_type"><option value="">Select your business type</option><option value="spot_cottage">Spot or cottage</option><option value="hotel_inn_lounge">Hotel, inn, or lounge</option><option value="food_establishment">Food establishment</option><option value="spot_hotel">Spot and hotel</option><option value="spot_food">Spot and food establishment</option><option value="hotel_food">Hotel and food establishment</option><option value="spot_hotel_food">Spot, hotel, and food establishment</option></select></label></div>
                <div class="auth-two-col"><label>Password<input type="password" name="password" placeholder="At least 8 characters" required autocomplete="new-password"></label><label>Confirm password<input type="password" name="password_confirmation" placeholder="Repeat password" required autocomplete="new-password"></label></div>
                <button type="submit">Create account <span>&rarr;</span></button>
                <p class="auth-switch">Already have an account? <button type="button" class="text-button" onclick="showLogin()">Sign in</button></p>
            </form>
        </section>
    </main>
<script>
const copy = {login:['Welcome back','Sign in to SpotTour','Use your account to manage your bookings.'],register:['Create your account','Start exploring with SpotTour','Choose an account type and complete your details.']};
function changePanel(name){document.getElementById('login').classList.toggle('hidden',name!=='login');document.getElementById('register').classList.toggle('hidden',name!=='register');document.getElementById('authKicker').textContent=copy[name][0];document.getElementById('authTitle').textContent=copy[name][1];document.getElementById('authSubtitle').textContent=copy[name][2];}
function showRegister(){changePanel('register')} function showLogin(){changePanel('login')}
function toggleBusinessType(){const isOwner=document.getElementById('role').value==='owner';document.getElementById('businessSection').classList.toggle('hidden',!isOwner);document.getElementById('business_type').required=isOwner;}
@if($errors->any() && old('role')) changePanel('register'); toggleBusinessType(); @endif
</script>
</body></html>
