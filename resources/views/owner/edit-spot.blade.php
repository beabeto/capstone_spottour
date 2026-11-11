<!DOCTYPE html>

<html>
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">

<title>Edit Spot</title>

<link rel="stylesheet"
href="{{ asset('css/style.css') }}?v={{ filemtime(public_path('css/style.css')) }}">

</head>

<body class="dashboard-body">
@include('partials.admin-notification-float')

<div class="spot-form-container">

<div class="spot-form-card">

<div class="back-btn">
    <button type="button" onclick="window.history.back(); return false;">Back</button>
</div>

<h1>Edit Spot</h1>

<form
method="POST"
action="/{{ Auth::user()->role === 'admin' ? 'admin' : 'owner' }}/spots/update/{{ $spot->id }}"
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
<label>Bookings accepted per day</label>
<input type="number" name="daily_booking_limit" min="1" value="{{ old('daily_booking_limit', $spot->daily_booking_limit) }}" placeholder="Leave blank for no daily limit">
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
src="{{ \App\Support\ListingImage::url($spot->image, 'spots') }}"
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

<div class="form-group"><label>Add gallery photos</label><input type="file" name="gallery_images[]" accept="image/jpeg,image/png,image/webp" multiple></div>

<button
type="submit"
class="form-save-btn">

Update Spot

</button>

</form>

@php($spotGallery = \App\Support\GalleryImages::normalize($spot->gallery_images))
@if($spotGallery)
<div class="gallery-image-grid">
@foreach($spotGallery as $galleryImage)
<div class="gallery-image-item"><img src="{{ \App\Support\ListingImage::url($galleryImage, 'spots') }}" alt="Spot gallery photo"><form method="POST" action="/{{ Auth::user()->role === 'admin' ? 'admin' : 'owner' }}/spots/{{ $spot->id }}/gallery/delete" onsubmit="return confirm('Delete this gallery photo?')">@csrf<input type="hidden" name="image" value="{{ $galleryImage }}"><button class="gallery-image-delete" type="submit">Delete photo</button></form></div>
@endforeach
</div>
@endif

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
