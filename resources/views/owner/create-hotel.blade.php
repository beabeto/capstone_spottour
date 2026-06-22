<!DOCTYPE html>

<html>
<head>

<title>Add Hotel</title>

<link rel="stylesheet"
href="{{ asset('css/style.css') }}">

</head>

<body class="dashboard-body">

<div class="back-btn">
    <button onclick="history.back()">
        ← 
    </button>
</div>

<div class="spot-form-container">

<div class="spot-form-card">

<h1>

🏨 Add Hotel

</h1>

<form
method="POST"
action="/owner/hotels/store"
enctype="multipart/form-data">

@csrf

<label>

Hotel Name

</label>

<input
type="text"
name="name"
required>

<label>

Location

</label>

<input
type="text"
name="location"
required>

<label>

Description

</label>

<textarea
name="description"
rows="5"
required></textarea>

<label>

Price Per Night

</label>

<input
type="number"
step="0.01"
name="price_per_night"
required>

<label>

Available Rooms

</label>

<input
type="number"
name="available_rooms"
required>

<label>

Amenities

</label>

<textarea
name="amenities"
rows="4"
placeholder="WiFi, Pool, Parking, Restaurant"></textarea>

<label>

Google Maps Link

</label>

<input
type="text"
name="map_link">

<label>

Hotel Image

</label>

<input
type="file"
name="image">

<button
type="submit">

Create Hotel

</button>

</form>

</div>

</div>

</body>

</html>