<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $page->title }}</title>
    <link rel="stylesheet" href="{{ asset('css/style.css') }}?v={{ filemtime(public_path('css/style.css')) }}">
    <link rel="stylesheet" href="{{ asset('css/responsive.css') }}">
</head>
<body class="owner-listing-page admin-listing-page">
<main class="page-container">
    <div class="back-btn">
        <button type="button" onclick="window.history.back(); return false;">Back</button>
    </div>

    <header class="management-heading">
        <span>{{ $page->kicker }}</span>
        <h1>{{ $page->title }}</h1>
        <p>{{ $page->intro }} Showing {{ $items->count() }} {{ \Illuminate\Support\Str::plural('item', $items->count()) }}.</p>
    </header>

    <section class="admin-listing-grid">
        @forelse($items as $item)
            <article class="admin-listing-card is-organized">
                <img src="{{ $item->image }}" alt="{{ $item->title }}">
                <div class="listing-primary">
                    <span class="listing-type">{{ $item->type }}</span>
                    <h2>{{ $item->title }}</h2>
                    <p>{{ $item->subtitle }}</p>
                </div>
                <div class="listing-facts">
                    <div class="listing-metric">
                        <small>{{ $item->detail }}</small>
                        <strong>{{ $item->amount }}</strong>
                    </div>
                </div>
                <div class="listing-status">
                    <span class="status-{{ $item->status }}">{{ ucfirst(str_replace('_', ' ', $item->status)) }}</span>
                </div>
                <div class="listing-actions">
                    <a href="{{ $item->url }}" class="view-btn">View</a>
                </div>
            </article>
        @empty
            <p class="empty-card">{{ $page->empty }}</p>
        @endforelse
    </section>
</main>
</body>
</html>
