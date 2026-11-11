<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Hotels</title>
    <link rel="stylesheet" href="{{ asset('css/style.css') }}?v={{ filemtime(public_path('css/style.css')) }}">
</head>
<body class="owner-listing-page admin-listing-page">
<main class="page-container">
    <div class="back-btn"><button type="button" onclick="window.history.back(); return false;">Back</button></div>
    <header class="management-heading">
        <span>Owner listings</span>
        <h1>My Hotels</h1>
        <p>Manage your hotel details, rooms, and booking status.</p>
        <a class="add-btn" href="/owner/hotels/create">+ Add Hotel</a>
    </header>
    <section class="admin-listing-grid">
        @forelse($hotels as $hotel)
            <article class="admin-listing-card is-organized">
                <img src="{{ \App\Support\ListingImage::url($hotel->image, 'hotels') }}" alt="{{ $hotel->name }}">
                <div class="listing-primary">
                    <span class="listing-type">Hotel</span>
                    <h2>{{ $hotel->name }}</h2>
                    <p>{{ $hotel->location }}</p>
                </div>
                <div class="listing-facts">
                    <div class="listing-owner">
                        <small>Owner</small>
                        <strong>{{ Auth::user()->name }}</strong>
                        <span>{{ Auth::user()->email }}</span>
                    </div>
                    <div class="listing-metric">
                        <small>Rate / night</small>
                        <strong>PHP {{ number_format((float) $hotel->price_per_night, 2) }}</strong>
                    </div>
                    <div class="listing-metric">
                        <small>Rooms</small>
                        <strong>{{ $hotel->available_rooms }}</strong>
                    </div>
                </div>
                <div class="listing-status">
                    <span class="status-{{ $hotel->status }}">{{ ucfirst($hotel->status) }}</span>
                </div>
                <div class="listing-actions">
                    <div class="listing-action-row">
                        <a href="/owner/hotels/{{ $hotel->id }}" class="view-btn">View</a>
                        <a href="/owner/hotels/{{ $hotel->id }}/rooms" class="listing-menu-btn">Rooms</a>
                    </div>
                    <div class="listing-action-row">
                        <a href="/owner/hotels/edit/{{ $hotel->id }}" class="edit-btn">Edit</a>
                        <a href="/owner/hotels/delete/{{ $hotel->id }}" class="deletes-btn" onclick="return confirm('Delete this hotel?')">Delete</a>
                    </div>
                </div>
            </article>
        @empty
            <p class="empty-card">No hotels found.</p>
        @endforelse
    </section>
</main>
</body>
</html>
