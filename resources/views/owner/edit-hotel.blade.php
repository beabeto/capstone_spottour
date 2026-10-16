<!DOCTYPE html>

<html>
<head>

<title>Edit Hotel</title>

<link rel="stylesheet"
href="{{ asset('css/style.css') }}">

</head>

<body class="dashboard-body">

<div class="back-btn">
    <button type="button" onclick="window.history.back(); return false;">Back</button>
</div>

<div class="spot-form-container">

<div class="spot-form-card">

<h1>

 Edit Hotel

</h1>

<form
method="POST"
action="/owner/hotels/update/{{ $hotel->id }}"
enctype="multipart/form-data">

@csrf

<label>

Hotel Name

</label>

<input
type="text"
name="name"
value="{{ $hotel->name }}"
required>

<label>

Location

</label>

<input
type="text"
name="location"
value="{{ $hotel->location }}"
required>

<label>

Description

</label>

<textarea
name="description"
rows="5"
required>{{ $hotel->description }}</textarea>

<label>

Price Per Night

</label>

<input
type="number"
step="0.01"
name="price_per_night"
value="{{ $hotel->price_per_night }}"
required>

<label>

Available Rooms

</label>

<input
type="number"
name="available_rooms"
value="{{ $hotel->available_rooms }}"
required>

<label>Bookings accepted per day</label>
<input type="number" name="daily_booking_limit" min="1" value="{{ old('daily_booking_limit', $hotel->daily_booking_limit) }}" placeholder="Leave blank for no daily limit">

<label>

Amenities

</label>

<textarea
name="amenities"
rows="4">{{ $hotel->amenities }}</textarea>

<label>

Google Maps Link

</label>

<input
type="text"
name="map_link"
value="{{ $hotel->map_link }}">

@if($hotel->image)

<br><br>

<img
src="{{ asset('uploads/hotels/'.$hotel->image) }}"
style="
width:200px;
border-radius:12px;
">

<br><br>

@endif

<label>

Replace Image

</label>

<input
type="file"
name="image">

<button
type="submit">

Update Hotel

</button>

</form>

</div>

</div>

</body>

</html>
