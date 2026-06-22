<!DOCTYPE html>

<html>
<head>

<title>Edit Spot</title>

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

<h1>Edit Spot</h1>

<form
method="POST"
action="/owner/spots/update/{{ $spot->id }}"
enctype="multipart/form-data">

@csrf

<div class="form-group">

<label>Spot Name</label>

<input
type="text"
name="name"
value="{{ $spot->name }}">

</div>

<div class="form-group">

<label>Location</label>

<input
type="text"
name="location"
value="{{ $spot->location }}">

</div>

<div class="form-group">

<label>Description</label>

<textarea
name="description"
rows="5">{{ $spot->description }}</textarea>

</div>

<div class="form-group">

<label>Entrance Fee</label>

<input
type="number"
step="0.01"
name="price"
value="{{ $spot->price }}">

</div>

<div class="form-group">

<label>Google Maps Link</label>

<input
type="text"
name="map_link"
value="{{ $spot->map_link }}">

</div>

<div class="form-group">

<label>Booking Type</label>

<select
name="booking_type"
id="booking_type"
onchange="toggleBookingSection()">

<option
value="none"
{{ $spot->booking_type == 'none' ? 'selected' : '' }}>

No Reservation

</option>

<option
value="cottage"
{{ $spot->booking_type == 'cottage' ? 'selected' : '' }}>

Cottage Reservation

</option>

</select>

</div>

<div
id="cottageSection"
{{ $spot->booking_type != 'cottage' ? 'style=display:none;' : '' }}>

<div class="form-group">

<label>
Available Cottages
</label>

<input
type="number"
name="available_cottages"
value="{{ $spot->available_cottages }}">

</div>

<div class="form-group">

<label>
Cottage Price
</label>

<input
type="number"
step="0.01"
name="cottage_price"
value="{{ $spot->cottage_price }}">

</div>

</div>

@if($spot->image)

<img
src="{{ asset('uploads/spots/'.$spot->image) }}"
style="
width:250px;
margin-bottom:20px;
border-radius:10px;">

@endif

<div class="form-group">

<label>New Image</label>

<input
type="file"
name="image">

</div>

<button
type="submit"
class="save-btn">

Update Spot

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

    let section =
        document.getElementById(
            'cottageSection'
        );

    if(bookingType === 'cottage')
    {
        section.style.display =
            'block';
    }
    else
    {
        section.style.display =
            'none';
    }
}

</script>

</body>
</html>
