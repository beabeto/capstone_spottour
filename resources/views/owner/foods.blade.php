<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Restaurants</title>
    <link rel="stylesheet" href="{{ asset('css/style.css') }}?v={{ filemtime(public_path('css/style.css')) }}">
</head>
<body class="owner-listing-page admin-listing-page">
<main class="page-container">
    <div class="back-btn"><button type="button" onclick="window.history.back(); return false;">Back</button></div>
    <header class="management-heading">
        <span>Owner listings</span>
        <h1>My Food Establishments</h1>
        <p>Manage restaurant details, menus, and booking status.</p>
        <a class="add-btn" href="/owner/foods/create">+ Add Restaurant</a>
    </header>
    <section class="admin-listing-grid">
        @forelse($foods as $food)
            <article class="admin-listing-card">
                <img src="{{ \App\Support\ListingImage::url($food->image, 'foods') }}" alt="{{ $food->name }}">
                <div class="listing-primary">
                    <span class="listing-type">Restaurant</span>
                    <h2>{{ $food->name }}</h2>
                    <p>{{ $food->location }}</p>
                </div>
                <div class="listing-owner">
                    <small>Owner</small>
                    <strong>{{ Auth::user()->name }}</strong>
                    <span>{{ Auth::user()->email }}</span>
                </div>
                <div class="listing-metric">
                    <small>Tables</small>
                    <strong>{{ $food->available_tables }}</strong>
                </div>
                <div class="listing-metric">
                    <small>Capacity</small>
                    <strong>{{ $food->venue_capacity }}</strong>
                </div>
                <div class="listing-status">
                    <span class="status-{{ $food->status }}">{{ ucfirst($food->status) }}</span>
                </div>
                <div class="listing-actions">
                    <div class="listing-action-row">
                        <a href="/owner/foods/{{ $food->id }}" class="view-btn">View</a>
                        <a href="/owner/foods/{{ $food->id }}/menus" class="listing-menu-btn">Menu</a>
                    </div>
                    <div class="listing-action-row">
                        <a href="/owner/foods/edit/{{ $food->id }}" class="edit-btn">Edit</a>
                        <a href="/owner/foods/delete/{{ $food->id }}" class="deletes-btn" onclick="return confirm('Delete this restaurant?')">Delete</a>
                    </div>
                </div>
            </article>
        @empty
            <p class="empty-card">No restaurants found.</p>
        @endforelse
    </section>
</main>
</body>
</html>
