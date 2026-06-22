<!DOCTYPE html>
<html>
<head>

    <title>Add Tourist Spot</title>

    <link rel="stylesheet"
    href="{{ asset('css/style.css') }}">

</head>

<body class="dashboard-body">

<div class="spot-form-container">

    <div class="spot-form-card">

        <div class="back-btn">
    <button onclick="history.back()">
        ← 
    </button>
</div>

        <h1>Add Tourist Spot</h1>

        <form
            method="POST"
            action="/owner/spots/store"
            enctype="multipart/form-data">

            @csrf

            <!-- SPOT NAME -->

            <div class="form-group">

                <label>Spot Name</label>

                <input
                    type="text"
                    name="name"
                    placeholder="Mabua Pebble Beach"
                    required>

            </div>

            <!-- LOCATION -->

            <div class="form-group">

                <label>Location</label>

                <input
                    type="text"
                    name="location"
                    placeholder="Barangay / Address"
                    required>

            </div>

            <!-- DESCRIPTION -->

            <div class="form-group">

                <label>Description</label>

                <textarea
                    name="description"
                    rows="5"
                    placeholder="Describe your tourist spot..."></textarea>

            </div>

            <!-- ENTRANCE FEE -->

            <div class="form-group">

                <label>Entrance Fee</label>

                <input
                    type="number"
                    step="0.01"
                    name="price"
                    placeholder="0.00">

            </div>

            <!-- MAP -->

            <h3>Google Maps Location</h3>

<div class="form-group">

    <label>

        Google Maps Link

    </label>

    <input
        type="url"
        name="map_link"
        placeholder="Paste Google Maps URL">

</div>

<div class="form-group">

    <label>

        Location Name

    </label>

    <input
        type="text"
        name="location"
        placeholder="Mabua Pebble Beach, Surigao City"
        required>

</div>

<h3>Google Maps Location</h3>

<div class="form-group">

    <label>

        Google Maps URL

    </label>

    <input
        type="url"
        name="map_link"
        placeholder="https://maps.google.com/...">

</div>

<p class="or-text">

    OR

</p>

<div class="form-group">

    <label>Latitude</label>

    <input
        type="text"
        name="latitude">

</div>

<div class="form-group">

    <label>Longitude</label>

    <input
        type="text"
        name="longitude">

</div>

            <!-- BOOKING OPTION -->

            <h3>Reservation Settings</h3>

            <div class="form-group">

                <label>Booking Type</label>

                <select
                    name="booking_type"
                    id="booking_type"
                    onchange="toggleBookingSection()">

                    <option value="none">
                        No Reservation Required
                    </option>

                    <option value="cottage">
                        Cottage Reservation
                    </option>

                </select>

            </div>

            <!-- COTTAGE SETTINGS -->

            <div
                id="cottageSection"
                class="hidden">

                <div class="form-group">

                    <label>
                        Available Cottages
                    </label>

                    <input
                        type="number"
                        name="available_cottages"
                        placeholder="10">

                </div>

                <div class="form-group">

                    <label>
                        Cottage Price
                    </label>

                    <input
                        type="number"
                        step="0.01"
                        name="cottage_price"
                        placeholder="500">

                </div>

            </div>

            <!-- IMAGE -->

            <div class="form-group">

                <label>Spot Image</label>

                <input
                    type="file"
                    name="image">

            </div>

            <button
                type="submit"
                class="save-btn">

                Save Spot

            </button>

        </form>

    </div>

</div>

<script>

function toggleBookingSection()
{
    let bookingType =
        document.getElementById(
            'booking_type'
        ).value;

    let cottageSection =
        document.getElementById(
            'cottageSection'
        );

    if(bookingType === 'cottage')
    {
        cottageSection
            .classList
            .remove('hidden');
    }
    else
    {
        cottageSection
            .classList
            .add('hidden');
    }
}

</script>

</body>
</html>