<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">

<title>SpotTour Authentication</title>

<link rel="stylesheet" href="{{ asset('css/style.css') }}">

</head>
<body class="auth-page">

<div class="container">

    <h2>SpotTour</h2>

    @if ($errors->any())
<div class="error">
    <ul>
        @foreach ($errors->all() as $error)
            <li>{{ $error }}</li>
        @endforeach
    </ul>
</div>
@endif

    <!-- LOGIN -->

    <div id="login">

        <form method="POST" action="/login">
        @csrf

            <input
                type="email"
                name="email"
                placeholder="Email Address"
                required>

            <input
                type="password"
                name="password"
                placeholder="Password"
                required>

            <button type="submit">
                Login
            </button>

        </form>

        <p>
            Don't have an account?
            <a href="#" onclick="showRegister()">
                Register
            </a>
        </p>

        <p>
            <a href="#" onclick="showForgot()">
                Forgot Password?
            </a>
        </p>

    </div>

    <!-- REGISTER -->

    <div id="register" class="hidden">

        <form method="POST" action="/register">
        @csrf

            <input
                type="text"
                name="name"
                placeholder="Full Name"
                required>

            <input
                type="email"
                name="email"
                placeholder="Email Address"
                required>

            <select
                name="role"
                id="role"
                onchange="toggleBusinessType()"
                required>

                <option value="">
                    Select Role
                </option>

                <option value="user">
                    User
                </option>

                <option value="owner">
                    Owner
                </option>

            </select>

            <!-- OWNER BUSINESS TYPE -->

            <div id="businessSection" class="hidden">

                <select name="business_type" id="business_type">

                    <option value="">
                        Select Business Type
                    </option>

                    <option value="spot_cottage">
                        Spot / Cottage Booking
                    </option>

                    <option value="hotel_inn_lounge">
                        Hotel / Inn / Lounge Booking
                    </option>

                    <option value="food_establishment">
                        Food Establishment Booking
                    </option>

                    <option value="spot_hotel">
                        Spot + Hotel Booking
                    </option>

                    <option value="spot_food">
                        Spot + Food Establishment
                    </option>

                    <option value="hotel_food">
                        Hotel + Food Establishment
                    </option>

                    <option value="spot_hotel_food">
                        Spot + Hotel + Food Establishment
                    </option>

                </select>

            </div>

            <input
                type="password"
                name="password"
                placeholder="Password"
                required>

            <input
                type="password"
                name="password_confirmation"
                placeholder="Confirm Password"
                required>

            <button type="submit">
                Register
            </button>

        </form>

        <p>
            Already have an account?
            <a href="#" onclick="showLogin()">
                Login
            </a>
        </p>

    </div>

    <!-- FORGOT PASSWORD -->

    <div id="forgot" class="hidden">

        <form>

            <input
                type="email"
                placeholder="Enter Your Email"
                required>

            <button type="submit">
                Send Reset Link
            </button>

        </form>

        <p>
            <a href="#" onclick="showLogin()">
                Back To Login
            </a>
        </p>

    </div>

</div>

<script>

function showRegister()
{
    document.getElementById('login')
        .classList.add('hidden');

    document.getElementById('forgot')
        .classList.add('hidden');

    document.getElementById('register')
        .classList.remove('hidden');
}

function showLogin()
{
    document.getElementById('register')
        .classList.add('hidden');

    document.getElementById('forgot')
        .classList.add('hidden');

    document.getElementById('login')
        .classList.remove('hidden');
}

function showForgot()
{
    document.getElementById('login')
        .classList.add('hidden');

    document.getElementById('register')
        .classList.add('hidden');

    document.getElementById('forgot')
        .classList.remove('hidden');
}

function toggleBusinessType()
{
    const role =
        document.getElementById('role').value;

    const businessSection =
        document.getElementById('businessSection');

    const businessType =
        document.getElementById('business_type');

    if(role === 'owner')
    {
        businessSection.classList.remove('hidden');
        businessType.required = true;
    }
    else
    {
        businessSection.classList.add('hidden');
        businessType.required = false;
        businessType.value = '';
    }
}

</script>

</body>
</html>