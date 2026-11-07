<!DOCTYPE html>
<html>
<head>
    <title>Add Tourist Spot</title>
    <link rel="stylesheet" href="{{ asset('css/style.css') }}">
</head>
<body class="dashboard-body">
<div class="spot-form-container">
    <div class="spot-form-card">
        <div class="back-btn">
            <button type="button" onclick="window.history.back(); return false;">Back</button>
        </div>
        <h1>Add Tourist Spot</h1>

        @if(session('error'))
            <div class="form-error-box">{{ session('error') }}</div>
        @endif
        @if($errors->any())
            <div class="form-error-box">
                <ul>@foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul>
            </div>
        @endif

        <form
            method="POST"
            action="{{ Auth::user()?->role === 'admin' ? '/admin/spots/store' : '/owner/spots/store' }}"
            enctype="multipart/form-data">
            @csrf

            @if(Auth::user()?->role === 'admin')
            <div class="form-group">
                <label>Assign to owner</label>
                <select name="owner_id" required>
                    @foreach($owners ?? [] as $owner)
                        <option value="{{ $owner->id }}" @selected(old('owner_id') == $owner->id)>{{ $owner->name }}</option>
                    @endforeach
                </select>
            </div>
            @endif

            <div class="form-group">
                <label>Spot Name</label>
                <input type="text" name="name" value="{{ old('name') }}" placeholder="Mabua Pebble Beach" required>
            </div>

            <div class="form-group">
                <label>Location</label>
                <input type="text" name="location" value="{{ old('location') }}" placeholder="Barangay / Address, Surigao City" required>
            </div>

            <div class="form-group">
                <label>Description</label>
                <textarea name="description" rows="5" placeholder="Describe your tourist spot..." required>{{ old('description') }}</textarea>
            </div>

            <div class="form-group">
                <label>Entrance Fee</label>
                <input type="number" step="0.01" min="0" name="price" value="{{ old('price') }}" placeholder="0.00">
            </div>

            <div class="form-group">
                <label>Bookings accepted per day</label>
                <input type="number" name="daily_booking_limit" min="1" value="{{ old('daily_booking_limit') }}" placeholder="Leave blank for no daily limit">
            </div>

            <h3>Google Maps Location</h3>
            <div class="form-group">
                <label>Google Maps URL</label>
                <input type="text" name="map_link" value="{{ old('map_link') }}" placeholder="https://maps.google.com/...">
            </div>
            <p class="or-text">OR</p>
            <div class="form-group">
                <label>Latitude</label>
                <input type="text" name="latitude" value="{{ old('latitude') }}">
            </div>
            <div class="form-group">
                <label>Longitude</label>
                <input type="text" name="longitude" value="{{ old('longitude') }}">
            </div>

            <h3>Reservation Settings</h3>
            <div class="form-group">
                <label>Booking Type</label>
                <select name="booking_type" id="booking_type" onchange="toggleBookingSection()">
                    <option value="none" @selected(old('booking_type', 'none') === 'none')>No Reservation Required</option>
                    <option value="cottage" @selected(old('booking_type') === 'cottage')>Cottage Reservation</option>
                </select>
            </div>

            <div id="cottageSection" class="{{ old('booking_type') === 'cottage' ? '' : 'hidden' }}">
                <div class="form-group">
                    <label>Available Cottages</label>
                    <input type="number" name="available_cottages" min="0" value="{{ old('available_cottages') }}" placeholder="10">
                </div>
                <div class="form-group">
                    <label>Cottage Price</label>
                    <input type="number" step="0.01" min="0" name="cottage_price" value="{{ old('cottage_price') }}" placeholder="500">
                </div>
            </div>

            <div class="form-group">
                <label>Spot Image</label>
                <input type="file" name="image" accept="image/jpeg,image/png,image/webp">
            </div>
            <div class="form-group">
                <label>Spot Gallery</label>
                <input type="file" name="gallery_images[]" accept="image/jpeg,image/png,image/webp" multiple>
                <small>Add multiple photos of the cottages, amenities, and views. The image above remains the cover.</small>
            </div>

            <button type="submit" class="form-save-btn">Save Spot</button>
        </form>
    </div>
</div>
<script>
function toggleBookingSection() {
    const cottageSection = document.getElementById('cottageSection');
    if (document.getElementById('booking_type').value === 'cottage') {
        cottageSection.classList.remove('hidden');
    } else {
        cottageSection.classList.add('hidden');
    }
}
</script>
</body>
</html>
