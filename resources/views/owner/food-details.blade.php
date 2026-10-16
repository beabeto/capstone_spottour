
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
    <button type="button" onclick="window.history.back(); return false;">Back</button>
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

             {{ $food->location }}

        </p>

        <div class="hero-rating">
            @include('partials.score-stars', ['score' => $food->ratings->avg('rating') ?? 0])
        </div>

    </div>

</div>

<!-- ACTION BUTTONS -->

<div class="spot-actions-top">

    <a
    href="/owner/foods/edit/{{ $food->id }}"
    class="bookmark-btn">

         Edit

    </a>

    <a
    href="/owner/foods/{{ $food->id }}/menus/create"
    class="favorite-btn">

         Add Menu

    </a>

    <a
    href="/owner/foods/{{ $food->id }}/menus"
    class="favorite-btn">

         Manage Menus

    </a>

    <a
    href="/owner/foods/delete/{{ $food->id }}"
    class="checklist-btn"
    onclick="return confirm('Delete this establishment?')">

         Delete

    </a>

</div>

<div class="spot-card">
    <h2>Guest ratings</h2>
    @include('partials.rating-board', ['ratings' => $food->ratings])
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

         Available Tables:
        {{ $food->available_tables }}

    </p>

    <p>

         Venue Capacity:
        {{ $food->venue_capacity }}

    </p>

    <form method="POST" action="/owner/foods/{{ $food->id }}/daily-limit" class="arrival-checkin">
        @csrf
        <label>Bookings accepted per day
            <input type="number" name="daily_booking_limit" min="1" value="{{ $food->daily_booking_limit }}" placeholder="No daily limit">
        </label>
        <button type="submit">Save</button>
    </form>

    <p>

         Menu Items:
        {{ $food->menus->count() }}

    </p>

    <p>

         Status:
        {{ ucfirst($food->status) }}

    </p>



</div>

<!-- LOCATION -->

<div class="spot-card">

    <h2>

        Location

    </h2>

    <p>

         {{ $food->location }}

    </p>

    @if($food->map_link)

    <a
    href="{{ $food->map_link }}"
    target="_blank"
    class="map-btn">

         Open In Google Maps

    </a>

    @endif

</div>

<!-- MENUS -->

<div class="spot-card">

    <h2>

         Food Menus

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

            PHP {{ number_format(
                $menu->price,
                2
            ) }}

        </h3>

        <div class="comment-actions">

            <a
            href="/owner/menus/edit/{{ $menu->id }}">

                 Edit

            </a>

            <a
            href="/owner/menus/delete/{{ $menu->id }}"
            onclick="return confirm('Delete this menu item?')">

                 Delete

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

         Customer Comments

    </h2>

    @if(session('success'))

    <div class="success-message">

        {{ session('success') }}

    </div>

    @endif

    <form
    method="POST"
    action="/food/comment" enctype="multipart/form-data">

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

        <label class="comment-image-field">Add photo (optional)<input type="file" name="image" accept="image/jpeg,image/png,image/webp"></label>

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

            @if($comment->image)<img src="{{ asset('uploads/comments/'.$comment->image) }}" class="review-image" alt="Review photo">@endif

            @foreach($comment->replies as $reply)
            <div class="reply-card">
                <div class="reply-header">
                    <img src="{{ $reply->user && $reply->user->profile_image ? asset('uploads/profile/'.$reply->user->profile_image) : 'https://cdn-icons-png.flaticon.com/512/3135/3135715.png' }}" class="reply-avatar" alt="">
                    <div>
                        <strong>{{ $reply->user->name ?? 'Guest' }}</strong>
                        <small>{{ $reply->created_at->diffForHumans() }}</small>
                    </div>
                </div>
                <div class="reply-content">
                    {{ $reply->reply }}
                    @if($reply->image)<img src="{{ asset('uploads/comments/'.$reply->image) }}" class="review-image" alt="Reply photo">@endif
                </div>
            </div>
            @endforeach

            <form method="POST" action="/food/comment/reply" enctype="multipart/form-data">
                @csrf
                <input type="hidden" name="comment_id" value="{{ $comment->id }}">
                <textarea name="reply" rows="2" placeholder="Write a reply..." required></textarea>
                <label class="comment-image-field">Add photo (optional)<input type="file" name="image" accept="image/jpeg,image/png,image/webp"></label>
                <button type="submit">Reply</button>
            </form>

            <div class="comment-rating">
                @include('partials.score-stars', ['score' => $comment->ratings->avg('rating') ?? 0])
            </div>

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
