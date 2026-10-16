<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Add Hotel</title>
<link rel="stylesheet" href="{{ asset('css/style.css') }}">
</head>
<body class="dashboard-body">
<div class="back-btn"><button type="button" onclick="window.history.back(); return false;">Back</button></div>
<div class="spot-form-container">
<div class="spot-form-card">
<h1>Add Hotel</h1>
@if($errors->any())
<div class="form-error-box"><ul>@foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul></div>
@endif
<form method="POST" action="{{ Auth::user()->role === 'admin' ? '/admin/hotels/store' : '/owner/hotels/store' }}" enctype="multipart/form-data" class="composed-form">
@csrf
<section class="form-block">
<h2>Hotel details</h2>
<p>Name, location, and photos for the property.</p>
@if(Auth::user()->role === 'admin')
<label>Assign to owner
<select name="owner_id" required>
@foreach($owners ?? [] as $owner)
<option value="{{ $owner->id }}" @selected(old('owner_id') == $owner->id)>{{ $owner->name }}</option>
@endforeach
</select>
</label>
@endif
<label>Hotel name<input type="text" name="name" value="{{ old('name') }}" required></label>
<label>Location<input type="text" name="location" value="{{ old('location') }}" required></label>
<label>Description<textarea name="description" rows="4" required>{{ old('description') }}</textarea></label>
<label>Amenities<textarea name="amenities" rows="3" placeholder="WiFi, Pool, Parking, Restaurant">{{ old('amenities') }}</textarea></label>
<label>Bookings accepted per day<input type="number" name="daily_booking_limit" min="1" value="{{ old('daily_booking_limit') }}" placeholder="Leave blank for no daily limit"></label>
<label>Google Maps link<input type="text" name="map_link" value="{{ old('map_link') }}"></label>
<label>Hotel image<input type="file" name="image" accept="image/jpeg,image/png,image/webp"></label>
</section>
<section class="form-block">
<h2>Room types</h2>
<p>Choose the kind of room and how many guests it fits. You can add more rooms later.</p>
<div class="repeat-list" data-repeat>
@foreach(old('rooms', [['type' => '', 'price' => '', 'available' => '']]) as $index => $room)
<div class="repeat-row" data-row>
<label>Room type
<select name="rooms[{{ $index }}][type]" required>
<option value="">Choose a room</option>
@foreach(\App\Models\HotelRoom::types() as $label => $pax)
<option value="{{ $label }}" @selected(($room['type'] ?? '') === $label)>{{ $label }}</option>
@endforeach
</select>
</label>
<label>Price per night<input type="number" name="rooms[{{ $index }}][price]" min="0" step="0.01" value="{{ $room['price'] ?? '' }}" required></label>
<label>How many rooms<input type="number" name="rooms[{{ $index }}][available]" min="1" value="{{ $room['available'] ?? '' }}" required></label>
<label class="wide">Room cover photo<input type="file" name="rooms[{{ $index }}][cover]" accept="image/jpeg,image/png,image/webp"></label>
<label class="wide">Room photo gallery<input type="file" name="rooms[{{ $index }}][gallery][]" accept="image/jpeg,image/png,image/webp" multiple></label>
<button type="button" class="ghost-btn" data-remove-row>Remove room</button>
</div>
@endforeach
</div>
<div class="repeat-actions"><button type="button" class="ghost-btn" data-add-row>Add another room</button></div>
</section>
<button type="submit" class="form-save-btn">Save hotel</button>
</form>
</div>
</div>
<script>
(function () {
    var list = document.querySelector('[data-repeat]');
    var add = document.querySelector('[data-add-row]');
    if (!list || !add) return;
    add.addEventListener('click', function () {
        var rows = list.querySelectorAll('[data-row]');
        var next = rows.length;
        var clone = rows[rows.length - 1].cloneNode(true);
        clone.querySelectorAll('input, select, textarea').forEach(function (field) {
            field.name = field.name.replace(/rooms\[\d+\]/, 'rooms[' + next + ']');
            if (field.type === 'file') field.value = '';
            else if (field.tagName === 'SELECT') field.selectedIndex = 0;
            else field.value = '';
        });
        list.appendChild(clone);
    });
    list.addEventListener('click', function (event) {
        var button = event.target.closest('[data-remove-row]');
        if (!button || list.querySelectorAll('[data-row]').length < 2) return;
        button.closest('[data-row]').remove();
    });
})();
</script>
</body>
</html>
