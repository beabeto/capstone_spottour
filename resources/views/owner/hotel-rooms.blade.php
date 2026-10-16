<!doctype html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Room types — {{ $hotel->name }}</title>
<link rel="stylesheet" href="{{ asset('css/style.css') }}">
</head>
<body class="dashboard-body">
@php $workspace = Auth::user()->role === 'admin' ? 'admin' : 'owner'; @endphp
<div class="back-btn"><button type="button" onclick="window.history.back()">Back</button></div>
<main class="page-container room-management-page">
    <header class="room-page-head">
        <div>
            <span>Rooms</span>
            <h1>{{ $hotel->name }}</h1>
            <p>Add another room type. Pick the kind of room and how many guests it fits.</p>
        </div>
        <a class="view-btn" href="/{{ $workspace }}/hotels/{{ $hotel->id }}">View hotel</a>
    </header>
    @if(session('success'))<div class="toast-success">{{ session('success') }}</div>@endif
    @if($errors->any())<div class="form-error-box"><ul>@foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul></div>@endif
    <section class="spot-form-card room-form-card">
        <h2>Add a room type</h2>
        <form method="POST" action="/{{ $workspace }}/hotels/{{ $hotel->id }}/rooms" enctype="multipart/form-data" class="composed-form">
            @csrf
            <div class="repeat-row">
                <label>Room type
                    <select name="name" required>
                        <option value="">Choose a room</option>
                        @foreach(\App\Models\HotelRoom::types() as $label => $pax)
                            <option value="{{ $label }}" @selected(old('name') === $label)>{{ $label }}</option>
                        @endforeach
                    </select>
                </label>
                <label>Price per night<input type="number" step="0.01" min="0" name="price_per_night" value="{{ old('price_per_night') }}" required></label>
                <label>How many rooms<input type="number" min="1" name="available_rooms" value="{{ old('available_rooms') }}" required></label>
                <label class="wide">Room cover photo<input type="file" name="cover_image" accept="image/jpeg,image/png,image/webp"></label>
                <label class="wide">Room photo gallery<input type="file" name="gallery_images[]" accept="image/jpeg,image/png,image/webp" multiple></label>
            </div>
            <button type="submit" class="form-save-btn">Save room type</button>
        </form>
    </section>
    <section class="room-types-grid">
    @forelse($hotel->rooms as $room)
        <article>
            @if($room->cover_image)<img src="{{ asset('uploads/hotel-rooms/'.$room->cover_image) }}" alt="{{ $room->name }}">@endif
            <strong>{{ $room->name }}</strong>
            <span>PHP {{ number_format($room->price_per_night, 2) }} / night</span>
            <small>{{ $room->available_rooms }} available</small>
            @if($room->gallery_images)
                <div class="room-gallery">@foreach($room->gallery_images as $image)<img src="{{ asset('uploads/hotel-rooms/'.$image) }}" alt="{{ $room->name }} photo">@endforeach</div>
            @endif
        </article>
    @empty
        <p class="empty-room-note">No room types added yet.</p>
    @endforelse
    </section>
</main>
</body>
</html>
