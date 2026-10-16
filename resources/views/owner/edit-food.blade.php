<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Edit Food Establishment</title>
<link rel="stylesheet" href="{{ asset('css/style.css') }}">
</head>
<body class="dashboard-body">
<div class="back-btn"><button type="button" onclick="window.history.back(); return false;">Back</button></div>
<div class="spot-form-container"><div class="spot-form-card">
<h1>Edit Food Establishment</h1>
@if($errors->any())
<div class="form-error-box"><ul>@foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul></div>
@endif
@if(session('success'))
<div class="toast-success">{{ session('success') }}</div>
@endif
<form action="/owner/foods/update/{{ $food->id }}" method="POST" enctype="multipart/form-data" class="composed-form">
@csrf
<section class="form-block">
<h2>Restaurant details</h2>
<p>Update the restaurant information. Manage dishes separately from Menus.</p>
@if(Auth::user()->role === 'admin')
<label>Assign to owner
<select name="owner_id" required>
@foreach($owners ?? [] as $owner)
<option value="{{ $owner->id }}" @selected(old('owner_id', $food->owner_id) == $owner->id)>{{ $owner->name }}</option>
@endforeach
</select>
</label>
@endif
<label>Restaurant name<input type="text" name="name" value="{{ old('name', $food->name) }}" required></label>
<label>Location<input type="text" name="location" value="{{ old('location', $food->location) }}" required></label>
<label>Description<textarea name="description" rows="4" required>{{ old('description', $food->description) }}</textarea></label>
<label>Available tables<input type="number" name="available_tables" min="1" value="{{ old('available_tables', $food->available_tables) }}" required></label>
<label>Venue capacity<input type="number" name="venue_capacity" min="1" value="{{ old('venue_capacity', $food->venue_capacity) }}" required></label>
<label>Price per guest<input type="number" name="price_per_guest" min="0" step="0.01" value="{{ old('price_per_guest', $food->price_per_guest) }}" placeholder="Table fee per guest"></label>
<label>Bookings accepted per day<input type="number" name="daily_booking_limit" min="1" value="{{ old('daily_booking_limit', $food->daily_booking_limit) }}" placeholder="Leave blank for no daily limit"></label>
<label>Google Maps link<input type="url" name="map_link" value="{{ old('map_link', $food->map_link) }}" placeholder="https://maps.google.com/..."></label>
@if($food->image)
<div class="current-image">
<img src="{{ asset('uploads/foods/'.$food->image) }}" alt="{{ $food->name }}" style="width:200px;border-radius:12px;">
</div>
@endif
<label>Replace cover image<input type="file" name="image" accept="image/jpeg,image/png,image/webp"></label>
@if(!empty($food->gallery_images))
<div class="listing-gallery" style="margin:10px 0;">
@foreach($food->gallery_images as $image)
<img src="{{ asset('uploads/foods/'.$image) }}" alt="Gallery photo" style="width:90px;height:70px;object-fit:cover;border-radius:8px;">
@endforeach
</div>
@endif
<label>Add gallery photos<input type="file" name="gallery_images[]" accept="image/jpeg,image/png,image/webp" multiple></label>
</section>
<button type="submit" class="form-save-btn">Update restaurant</button>
</form>
<p style="margin-top:14px;color:#607772;font-size:13px;">
Want to change dishes?
<a href="/{{ Auth::user()->role === 'admin' ? 'admin' : 'owner' }}/foods/{{ $food->id }}/menus">Manage menus</a>
</p>
</div></div>
</body>
</html>
