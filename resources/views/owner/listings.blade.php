<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Listings</title>
    <link rel="stylesheet" href="{{ asset('css/style.css') }}">
    <link rel="stylesheet" href="{{ asset('css/responsive.css') }}">
</head>
<body class="owner-listing-page admin-listing-page">
<main class="page-container">
    <div class="back-btn">
        <button type="button" onclick="window.history.back(); return false;">Back</button>
    </div>

    <header class="management-heading">
        <span>Owner listings</span>
        <h1>My Listings</h1>
        <p>All {{ $listings->count() }} spots, hotels, and restaurants under your account.</p>
    </header>

    <section class="admin-listing-grid">
        @forelse($listings as $listing)
            <article class="admin-listing-card is-organized">
                <img src="{{ $listing->image }}" alt="{{ $listing->name }}">
                <div class="listing-primary">
                    <span class="listing-type">{{ $listing->type }}</span>
                    <h2>{{ $listing->name }}</h2>
                    <p>{{ $listing->location }}</p>
                </div>
                <div class="listing-facts">
                    <div class="listing-metric">
                        <small>{{ $listing->metric_label }}</small>
                        <strong>{{ $listing->metric_value }}</strong>
                    </div>
                </div>
                <div class="listing-status">
                    <span class="status-{{ $listing->status }}">{{ ucfirst($listing->status) }}</span>
                </div>
                <div class="listing-actions">
                    <a href="{{ $listing->url }}" class="view-btn">View</a>
                    <a href="{{ $listing->manage_url }}" class="view-btn">Manage</a>
                </div>
            </article>
        @empty
            <p class="empty-card">No listings found.</p>
        @endforelse
    </section>
</main>
</body>
</html>
