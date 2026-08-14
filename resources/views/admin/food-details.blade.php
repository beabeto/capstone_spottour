<!DOCTYPE html>

<html>
<head>

<meta charset="UTF-8">

<meta name="viewport"
content="width=device-width, initial-scale=1.0">

<title>{{ $food->name }}</title>

<link rel="stylesheet"
href="{{ asset('css/style.css') }}">

</head>

<body class="dashboard-body">

<div class="back-btn">
    <button onclick="history.back()">
        ←
    </button>
</div>

@if(session('success'))

<div class="success">

    {{ session('success') }}

</div>

@endif

<div class="spot-details-container">

<!-- HERO -->

<div class="spot-hero">

    @if($food->image)

    <img
    src="{{ asset('uploads/foods/'.$food->image) }}"
    class="hero-image">

    @endif

    <div class="hero-overlay">

        <h1>

            {{ $food->name }}

        </h1>

        <p>

            📍 {{ $food->location }}

        </p>

        <div class="hero-rating">

            🍽 Food Establishment

        </div>

    </div>

</div>

<!-- ADMIN ACTIONS -->

<div class="spot-actions-top">

    <a
    href="/admin/foods/approve/{{ $food->id }}"
    class="favorite-btn">

        ✅ Approve

    </a>

    <a
    href="/admin/foods/reject/{{ $food->id }}"
    class="bookmark-btn">

        ❌ Reject

    </a>

    <a
    href="/admin/foods/delete/{{ $food->id }}"
    class="checklist-btn"
    onclick="return confirm('Delete this food establishment?')">

        🗑 Delete

    </a>

</div>

<!-- ABOUT -->

<div class="spot-card">

    <h2>

        About This Food Establishment

    </h2>

    <p>

        {{ $food->description }}

    </p>

</div>

<!-- DETAILS -->

<div class="spot-card">

    <h2>

        Establishment Information

    </h2>

    <p>

        🍽 Available Tables:
        {{ $food->available_tables }}

    </p>

    <p>

        🎉 Venue Capacity:
        {{ $food->venue_capacity }}

    </p>

    <p>

        📌 Status:
        {{ ucfirst($food->status) }}

    </p>

    <p>

        👤 Owner:
        {{ $food->owner->name ?? 'N/A' }}

    </p>

    <p>

        📧 Owner Email:
        {{ $food->owner->email ?? 'N/A' }}

    </p>

</div>

<!-- LOCATION -->

<div class="spot-card">

    <h2>

        Location

    </h2>

    <p>

        📍 {{ $food->location }}

    </p>

    @if($food->map_link)

    <a
    href="{{ $food->map_link }}"
    target="_blank"
    class="map-btn">

        📍 Open In Google Maps

    </a>

    @endif

</div>

<!-- MENUS -->

<div class="spot-card">

    <h2>

        🍴 Food Menus

    </h2>

    @forelse($food->menus as $menu)

    <div class="comment-card">

        <div class="comment-header">

            @if($menu->image)

            <img
            src="{{ asset('uploads/menus/'.$menu->image) }}"
            class="comment-avatar">

            @endif

            <div>

                <h4>

                    {{ $menu->name }}

                </h4>

                <small>

                    {{ ucfirst($menu->category) }}

                </small>

            </div>

        </div>

        <p>

            {{ $menu->description }}

        </p>

        <h3>

            ₱{{ number_format(
                $menu->price,
                2
            ) }}

        </h3>

    </div>

    @empty

    <p>

        No Menu Items Available

    </p>

    @endforelse

</div>

<!-- OWNER PAYMENT INFO -->

<div class="spot-card">

    <h2>

        💳 Owner Payment Information

    </h2>

    <p>

        GCash Number:
        {{ $food->owner->gcash_number ?? 'Not Set' }}

    </p>

    <p>

        GCash Name:
        {{ $food->owner->gcash_name ?? 'Not Set' }}

    </p>

    <p>

        Bank:
        {{ $food->owner->bank_name ?? 'Not Set' }}

    </p>

    <p>

        Account Number:
        {{ $food->owner->bank_account_number ?? 'Not Set' }}

    </p>

</div>

```blade
<!-- FOOD RATING -->

<div class="spot-card">

    <h2>

        ⭐ Food Rating

    </h2>

    <div class="rating-big">

        ⭐

        {{ number_format(
            $food->ratings->avg('rating') ?? 0,
            1
        ) }}

        / 5

    </div>

    <p>

        Total Ratings:
        {{ $food->ratings->count() }}

    </p>

</div>

<!-- CUSTOMER COMMENTS -->

<div class="spot-card">

    <h2>

        💬 Customer Reviews

    </h2>

    @forelse($food->comments ?? collect() as $comment)

    <div class="comment-card">

        <div class="comment-header">

            <img
            src="{{ $comment->user && $comment->user->profile_image
                ? asset('uploads/profile/'.$comment->user->profile_image)
                : 'https://cdn-icons-png.flaticon.com/512/3135/3135715.png' }}"
            class="comment-avatar">

            <div>

                <h4>

                    {{ $comment->user->name ?? 'Unknown User' }}

                </h4>

                <small>

                    {{ $comment->created_at->diffForHumans() }}

                </small>

            </div>

        </div>

        <p>

            {{ $comment->comment }}

        </p>

        <div class="comment-rating">

            ⭐

            {{ number_format(
                $comment->ratings->avg('rating') ?? 0,
                1
            ) }}

        </div>

        @if($comment->ratings->count())

        <small>

            Based on

            {{ $comment->ratings->count() }}

            votes

        </small>

        @endif

    </div>

    @empty

    <p>

        No Reviews Yet.

    </p>

    @endforelse

</div>
```


</div>

</body>

</html>