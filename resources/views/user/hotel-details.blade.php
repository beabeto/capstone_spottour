<!DOCTYPE html>

<html lang="en">
<head>

<meta charset="UTF-8">

<meta name="viewport"
content="width=device-width, initial-scale=1.0">

<title>{{ $hotel->name }}</title>

<link rel="stylesheet"
href="{{ asset('css/style.css') }}">

</head>

<body class="dashboard-body">

@if(session('success'))

<div id="toast" class="toast-success">

    {{ session('success') }}

</div>

@endif

<!-- BACK -->

<div class="back-btn">
    <button onclick="history.back()">
        ← Back
    </button>
</div>

<div class="spot-details-container">

<!-- HERO -->

<div class="spot-hero">

    @if($hotel->image)

    <img
    src="{{ asset('uploads/hotels/'.$hotel->image) }}"
    class="hero-image">

    @endif

    <div class="hero-overlay">

        <h1>

            {{ $hotel->name }}

        </h1>

        <p>

            📍 {{ $hotel->location }}

        </p>

        <div class="hero-rating">

            ⭐

            {{ number_format(
                $hotel->ratings()->avg('rating') ?? 0,
                1
            ) }}

            / 5.0

        </div>

    </div>

</div>

<!-- FAVORITE BOOKMARK CHECKLIST -->

<div class="spot-actions-top">

    <a
    href="/hotel/favorite/{{ $hotel->id }}"
    class="favorite-btn">

        ❤️ Favorite

    </a>

    <a
    href="/hotel/bookmark/{{ $hotel->id }}"
    class="bookmark-btn">

        🔖 Bookmark

    </a>

    <a
    href="/hotel/checklist/{{ $hotel->id }}"
    class="checklist-btn">

        ✅ Checklist

    </a>

</div>

<!-- ABOUT -->

<div class="spot-card">

    <h2>

        About This Hotel

    </h2>

    <p>

        {{ $hotel->description }}

    </p>

</div>

<!-- ROOM INFORMATION -->

<div class="spot-card">

    <h2>

        Room Information

    </h2>

    <p>

        💰 Price Per Night:

        ₱{{ number_format(
            $hotel->price_per_night,
            2
        ) }}

    </p>

    <p>

        🛏 Available Rooms:

        {{ $hotel->available_rooms }}

    </p>

</div>

<!-- AMENITIES -->

<div class="spot-card">

    <h2>

        Amenities

    </h2>

    <p>

        {{ $hotel->amenities }}

    </p>

</div>

<!-- LOCATION -->

<div class="spot-card">

    <h2>

        Location

    </h2>

    <p>

        📍 {{ $hotel->location }}

    </p>

    @if($hotel->map_link)

    <a
    href="{{ $hotel->map_link }}"
    target="_blank"
    class="map-btn">

        📍 Open In Google Maps

    </a>

    @endif

</div>

<!-- HOTEL RESERVATION -->

<div class="spot-card">

    <h2>

        Reserve This Hotel

    </h2>

    <form
method="GET"
action="/hotel/booking/confirm">

        <input
        type="hidden"
        name="hotel_id"
        value="{{ $hotel->id }}">

        <label>

            Check In

        </label>

        <input
        type="date"
        name="check_in"
        min="{{ date('Y-m-d') }}"
        required>

        <label>

            Check Out

        </label>

        <input
        type="date"
        name="check_out"
        min="{{ date('Y-m-d') }}"
        required>

        <label>

            Number Of Rooms

        </label>

        <input
        type="number"
        name="rooms"
        min="1"
        max="{{ $hotel->available_rooms }}"
        value="1">

        <div class="booking-info">

            <p>

                Available Rooms:
                {{ $hotel->available_rooms }}

            </p>

            <p>

                Room Rate:
                ₱{{ number_format(
                    $hotel->price_per_night,
                    2
                ) }}

                / Night

            </p>

        </div>

        <button
        type="submit">

            Proceed To Reservation

        </button>

    </form>

</div>

<!-- RATINGS -->

<div class="spot-card">

    <h2>

        Ratings

    </h2>

    <div class="rating-big">

        ⭐

        {{ number_format(
            $hotel->ratings->avg('rating') ?? 0,
            1
        ) }}

    </div>

    <p>

        {{ $hotel->ratings->count() }}
        Ratings

    </p>

    <form
    method="POST"
    action="/hotel/rate">

        @csrf

        <input
        type="hidden"
        name="hotel_id"
        value="{{ $hotel->id }}">

        <select name="rating">

            <option value="1">⭐ 1 Star</option>
            <option value="2">⭐⭐ 2 Stars</option>
            <option value="3">⭐⭐⭐ 3 Stars</option>
            <option value="4">⭐⭐⭐⭐ 4 Stars</option>
            <option value="5">⭐⭐⭐⭐⭐ 5 Stars</option>

        </select>

        <button>

            Submit Rating

        </button>

    </form>

</div>

<!-- COMMENTS -->

<div class="spot-card">

    <h2>

        Comments & Reviews

    </h2>

    <form
    method="POST"
    action="/hotel/comment">

        @csrf

        <input
        type="hidden"
        name="hotel_id"
        value="{{ $hotel->id }}">

        <textarea
        name="comment"
        rows="4"
        placeholder="Write your review..."
        required></textarea>

        <button type="submit">

            Submit Comment

        </button>

    </form>

    <br>

   @forelse($hotel->comments as $comment)

<div
id="comment{{ $comment->id }}"
class="comment-card">

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

                {{ ucfirst($comment->user->role ?? 'user') }}

            </small>

        </div>

        <div class="comment-menu">

            <button
            type="button"
            class="menu-btn"
            onclick="toggleMenu({{ $comment->id }})">

                ⋮

            </button>

            <div
            id="menu{{ $comment->id }}"
            class="comment-dropdown">

                <a
                href="javascript:void(0)"
                onclick="toggleReplyBox({{ $comment->id }})">

                    Reply

                </a>

                @if(Auth::id() == $comment->user_id)

                <a
                href="/hotel/comments/edit/{{ $comment->id }}">

                    Edit

                </a>

                <a
                href="/hotel/comments/delete/{{ $comment->id }}">

                    Delete

                </a>

                @endif

            </div>

        </div>

    </div>

    <div class="comment-body">

    <p>

        {{ $comment->comment }}

    </p>

    <small>

        {{ $comment->created_at->diffForHumans() }}

    </small>

</div>

<div class="comment-rating">

    ⭐ Comment Rating:

    {{ number_format(
        $comment->ratings->avg('rating') ?? 0,
        1
    ) }}

</div>

<div class="comment-actions">

    <a href="/hotel/comment/{{ $comment->id }}/like">

        👍

        {{ $comment->reactions
            ->where('reaction','like')
            ->count() }}

    </a>

    <a href="/hotel/comment/{{ $comment->id }}/dislike">

        👎

        {{ $comment->reactions
            ->where('reaction','dislike')
            ->count() }}

    </a>

</div>



    <form
    method="POST"
    action="/hotel/comment/rate">

        @csrf

        <input
        type="hidden"
        name="comment_id"
        value="{{ $comment->id }}">

        <select
        name="rating">

            <option value="1">⭐</option>
            <option value="2">⭐⭐</option>
            <option value="3">⭐⭐⭐</option>
            <option value="4">⭐⭐⭐⭐</option>
            <option value="5">⭐⭐⭐⭐⭐</option>

        </select>

        <button type="submit">

            Rate

        </button>

    </form>

    <div
id="replyBox{{ $comment->id }}"
class="reply-box"
style="display:none;">

<form
method="POST"
action="/hotel/comment/reply">

    @csrf

    <input
    type="hidden"
    name="comment_id"
    value="{{ $comment->id }}">

    <textarea
    name="reply"
    rows="2"
    placeholder="Write a reply..."
    required></textarea>

    <button type="submit">

        Reply

    </button>

</form>

</div>

    @foreach($comment->replies as $reply)

    <div class="reply-card">

    <div class="reply-header">

    <img
    src="{{ $reply->user && $reply->user->profile_image
    ? asset('uploads/profile/'.$reply->user->profile_image)
    : 'https://cdn-icons-png.flaticon.com/512/3135/3135715.png' }}"
    class="reply-avatar">

    <div>

        <strong>

            {{ $reply->user->name ?? 'Unknown User' }}

        </strong>

        <p>

    {{ $reply->reply }}

</p>

<small>

    {{ $reply->created_at->diffForHumans() }}

</small>

    </div>

    <div class="comment-menu">

        <button
        type="button"
        class="menu-btn"
        onclick="toggleReplyMenu({{ $reply->id }})">

            ⋮

        </button>

        <div
        id="replyMenu{{ $reply->id }}"
        class="comment-dropdown">

            <a
            href="javascript:void(0)"
            onclick="toggleReplyToReply({{ $reply->id }})">

                Reply

            </a>

            @if(Auth::id() == $reply->user_id)

            <a
            href="/hotel/reply/edit/{{ $reply->id }}">

                Edit

            </a>

            <a
            href="/hotel/reply/delete/{{ $reply->id }}">

                Delete

            </a>

            @endif

        </div>

    </div>

    </div>

<div
id="replyToReply{{ $reply->id }}"
class="reply-box"
style="display:none;">

    <form
    method="POST"
    action="/hotel/comment/reply">

        @csrf

        <input
        type="hidden"
        name="comment_id"
        value="{{ $comment->id }}">

        <textarea
        name="reply"
        rows="2"
        placeholder="Reply to {{ $reply->user->name }}"
        required></textarea>

        <button type="submit">

            Reply

        </button>

    </form>

</div>

</div>

        

    </div>

    @endforeach



@empty

<p>

    No comments yet.

</p>

@endforelse
</div>

</div>

<script>

function toggleMenu(id)
{
    let menu =
    document.getElementById(
        'menu' + id
    );

    if(menu)
    {
        menu.classList.toggle(
            'show'
        );
    }
}

function toggleReplyMenu(id)
{
    let menu =
    document.getElementById(
        'replyMenu' + id
    );

    if(menu)
    {
        menu.classList.toggle(
            'show'
        );
    }
}

function toggleReplyBox(id)
{
    let box =
    document.getElementById(
        'replyBox' + id
    );

    if(box)
    {
        box.style.display =
        box.style.display === 'none'
        ? 'block'
        : 'none';
    }
}

function toggleReplyToReply(id)
{
    let box =
    document.getElementById(
        'replyToReply' + id
    );

    if(box)
    {
        box.style.display =
        box.style.display === 'none'
        ? 'block'
        : 'none';
    }
}

</script>

</body>

</html>