
<!DOCTYPE html>

<html lang="en">
<head>

<meta charset="UTF-8">

<meta name="viewport"
content="width=device-width, initial-scale=1.0">

<title>{{ $food->name }}</title>

<link rel="stylesheet"
href="{{ asset('css/style.css') }}">

</head>

<body class="dashboard-body">

<div class="spot-details-container">

<!-- BACK -->

<div class="back-btn">
    <button onclick="history.back()">
        ←
    </button>
</div>

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

            🍽 Food Establishment •
            {{ ucfirst($food->status) }}

        </div>

    </div>

</div>

<!-- ACTION BUTTONS -->

<div class="spot-actions-top">

    <a
    href="/owner/foods/edit/{{ $food->id }}"
    class="bookmark-btn">

        ✏️ Edit

    </a>

    <a
    href="/owner/foods/{{ $food->id }}/menus/create"
    class="favorite-btn">

        🍽 Add Menu

    </a>

    <a
    href="/owner/foods/{{ $food->id }}/menus"
    class="favorite-btn">

        📋 Manage Menus

    </a>

    <a
    href="/owner/foods/delete/{{ $food->id }}"
    class="checklist-btn"
    onclick="return confirm('Delete this establishment?')">

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

        🍴 Menu Items:
        {{ $food->menus->count() }}

    </p>

    <p>

        📌 Status:
        {{ ucfirst($food->status) }}

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

        <div class="comment-actions">

            <a
            href="/owner/menus/edit/{{ $menu->id }}">

                ✏️ Edit

            </a>

            <a
            href="/owner/menus/delete/{{ $menu->id }}"
            onclick="return confirm('Delete this menu item?')">

                🗑 Delete

            </a>

        </div>

    </div>

    @empty

    <p>

        No Menu Items Yet

    </p>

    @endforelse

</div>


<div class="spot-card">

    <h2>

        💬 Customer Comments

    </h2>

    @if(session('success'))

    <div class="success-message">

        {{ session('success') }}

    </div>

    @endif

    <form
    method="POST"
    action="/food/comment">

        @csrf

        <input
        type="hidden"
        name="food_id"
        value="{{ $food->id }}">

        <textarea
        name="comment"
        rows="4"
        placeholder="Write your comment..."
        required></textarea>

        <button
        type="submit"
        class="submit-btn">

            Submit Comment

        </button>

    </form>

    <br>

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

        <div class="comment-body">

            <p>

                {{ $comment->comment }}

            </p>

        </div>

    </div>

    @empty

    <p>

        No comments yet.

    </p>

    @endforelse

</div>



</div>

</div>

</body>

</html>
