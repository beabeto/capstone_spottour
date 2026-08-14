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

            <h3>

                Owner Information

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
            placeholder="Email Address">

            <h3>

                Business Information

h3>

            <input
            type="text"
            value="{{ Auth::user()->business_type }}"
            readonly>

            <h3>

                Payment Information

            </h3>

            <input
            type="text"
            name="gcash_number"
            value="{{ Auth::user()->gcash_number }}"
            placeholder="GCash Number">

            <input
            type="text"
            name="gcash_name"
            value="{{ Auth::user()->gcash_name }}"
            placeholder="GCash Account Name">

            <input
            type="text"
            name="bank_name"
            value="{{ Auth::user()->bank_name }}"
            placeholder="Bank Name">

            <input
            type="text"
            name="bank_account_number"
            value="{{ Auth::user()->bank_account_number }}"
            placeholder="Bank Account Number">

            <h3>

                Change Password

            </h3>

            <input
            type="password"
            name="password"
            placeholder="New Password">

            <button type="submit">

                Save Changes

            </button>

        </form>

    </div>
        

    <div class="business-card">

        <h2>

            Business Information

        </h2>

        <table class="owner-table">

            <tr>

                <td>

                    <strong>

                        Owner Name

                    </strong>

                </td>

                <td>

                    {{ Auth::user()->name }}

                </td>

            </tr>

            <tr>

                <td>

                    <strong>

                        Email

                    </strong>

                </td>

                <td>

                    {{ Auth::user()->email }}

                </td>

            </tr>

            <tr>

                <td>

                    <strong>

                        Role

                    </strong>

                </td>

                <td>

                    {{ Auth::user()->role }}

                </td>

            </tr>

            <tr>

                <td>

                    <strong>

                        Business Type

                    </strong>

                </td>

                <td>

                    {{ Auth::user()->business_type }}

                </td>

            </tr>

        </table>

    </div>

</div>

</body>
</html>