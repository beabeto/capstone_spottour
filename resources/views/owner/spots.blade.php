<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Spots</title>
    <link rel="stylesheet" href="{{ asset('css/style.css') }}?v={{ filemtime(public_path('css/style.css')) }}">
</head>
<body class="owner-listing-page admin-listing-page">
<main class="page-container">
    <div class="back-btn"><button type="button" onclick="window.history.back(); return false;">Back</button></div>
    <header class="management-heading">
        <span>Owner listings</span>
        <h1>My Spots</h1>
        <p>Manage your tourist spots, details, and booking status.</p>
        <a class="add-btn" href="/owner/spots/create">+ Add Spot</a>
    </header>
    <section class="admin-listing-grid">
        @forelse($spots as $spot)
            <article class="admin-listing-card">
                <img src="{{ \App\Support\ListingImage::url($spot->image, 'spots') }}" alt="{{ $spot->name }}">
                <div class="listing-primary">
                    <span class="listing-type">Tourist spot</span>
                    <h2>{{ $spot->name }}</h2>
                    <p>{{ $spot->location }}</p>
                </div>
                <div class="listing-owner">
                    <small>Owner</small>
                    <strong>{{ Auth::user()->name }}</strong>
                    <span>{{ Auth::user()->email }}</span>
                </div>
                <div class="listing-metric">
                    <small>Entry fee</small>
                    <strong>PHP {{ number_format((float) $spot->price, 2) }}</strong>
                </div>
                <div class="listing-metric">
                    <small>Booking type</small>
                    <strong>{{ ucfirst($spot->booking_type ?: 'Standard') }}</strong>
                </div>
                <div class="listing-status">
                    <span class="status-{{ $spot->status }}">{{ ucfirst($spot->status) }}</span>
                </div>
                <div class="listing-actions">
                    <div class="listing-action-row"><a href="/owner/spots/{{ $spot->id }}" class="view-btn">View</a></div>
                    <div class="listing-action-row">
                        <a href="/owner/spots/edit/{{ $spot->id }}" class="edit-btn">Edit</a>
                        <form method="POST" action="/owner/spots/delete/{{ $spot->id }}" onsubmit="return confirm('Delete this spot?')">
                            @csrf
                            <button type="submit" class="deletes-btn">Delete</button>
                        </form>
                    </div>
                </div>
            </article>
        @empty
            <p class="empty-card">No tourist spots found.</p>
        @endforelse
    </section>
</main>
</body>
</html>
