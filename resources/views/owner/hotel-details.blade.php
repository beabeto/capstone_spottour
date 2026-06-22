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

<div class="spot-details-container">

<!-- BACK -->

<div class="back-btn">
    <button onclick="history.back()">
        ← 
    </button>
</div>

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

    <p>

        🏨 Amenities:

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

<!-- BOOKING -->

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

        <p>

            Available Rooms:
            {{ $hotel->available_rooms }}

        </p>

        <p>

            Price Per Night:
            ₱{{ number_format(
                $hotel->price_per_night,
                2
            ) }}

        </p>

        <button
        type="submit">

            Proceed To Reservation

        </button>

    </form>

</div>

<!-- HOTEL RATING -->

<div class="spot-card">

    <h2>

        Rate This Hotel

    </h2>

    <form
    method="POST"
    action="/hotel/rate">

        @csrf

        <input
        type="hidden"
        name="hotel_id"
        value="{{ $hotel->id }}">

        <select
        name="rating">

            <option value="1">⭐ 1 Star</option>
            <option value="2">⭐⭐ 2 Stars</option>
            <option value="3">⭐⭐⭐ 3 Stars</option>
            <option value="4">⭐⭐⭐⭐ 4 Stars</option>
            <option value="5">⭐⭐⭐⭐⭐ 5 Stars</option>

        </select>

        <button
        type="submit">

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

        <button
        type="submit">

            Submit Comment

        </button>

    </form>

    <br>

    @forelse($hotel->comments as $comment)

        <div class="comment-card">

        <div class="comment-header">

            <img
            src="{{ $comment->user->profile_image
            ? asset('uploads/profile/'.$comment->user->profile_image)
            : 'https://cdn-icons-png.flaticon.com/512/3135/3135715.png' }}"
            class="comment-avatar">

            <div>

                <h4>

                    {{ $comment->user->name }}

                </h4>

                <small>

                    {{ ucfirst(
                        $comment->user->role
                    ) }}

                </small>

            </div>

            <div class="comment-menu">

<button
class="menu-btn"
onclick="toggleMenu({{ $comment->id }})">

⋮

</button>

<div
id="menu{{ $comment->id }}"
class="comment-dropdown">

    <a href="javascript:void(0)"
       onclick="toggleReplyBox({{ $comment->id }})">
        Reply
    </a>

    @if(Auth::id() == $comment->user_id)

    <a href="/hotel/comments/edit/{{ $comment->id }}">
        Edit
    </a>

    <a href="/hotel/comments/delete/{{ $comment->id }}">
        Delete
    </a>

    @endif

</div>

</div>

        </div>

        <p>

            {{ $comment->comment }}

        </p>

        <small>

            {{ $comment->created_at->diffForHumans() }}

        </small>

        <div class="comment-rating">

            ⭐

            {{ number_format(
                $comment->ratings->avg('rating') ?? 0,
                1
            ) }}

        </div>

        <div class="comment-actions">

            <a
            href="/hotel/comment/{{ $comment->id }}/like">

                👍

                {{ $comment->reactions
                ->where('type','like')
                ->count() }}

            </a>

            <a
            href="/hotel/comment/{{ $comment->id }}/dislike">

                👎

                {{ $comment->reactions
                ->where('type','dislike')
                ->count() }}

            </a>

        </div>

        <!-- RATE COMMENT -->

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

            <button>

                Rate Comment

            </button>

        </form>

        <!-- REPLY -->

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
        src="{{ $reply->user->profile_image
            ? asset('uploads/profile/'.$reply->user->profile_image)
            : 'https://cdn-icons-png.flaticon.com/512/3135/3135715.png' }}"
        class="reply-avatar">

        <div>

            <strong>

                {{ $reply->user->name }}

            </strong>

        </div>

        <div class="comment-menu">

            <button
            class="menu-btn"
            onclick="toggleReplyMenu({{ $reply->id }})">

                ⋮

            </button>

            <div
            id="replyMenu{{ $reply->id }}"
            class="comment-dropdown">

                <a href="javascript:void(0)"
                   onclick="toggleReplyReply({{ $reply->id }})">
                    Reply
                </a>

                @if(Auth::id() == $reply->user_id)

                <a href="/hotel/reply/edit/{{ $reply->id }}">
                    Edit
                </a>

                <a href="/hotel/reply/delete/{{ $reply->id }}">
                    Delete
                </a>

                @endif

            </div>

        </div>

        

    </div>

    <div class="reply-content">

        {{ $reply->reply }}

    </div>

    <small class="reply-time">

        {{ $reply->created_at->diffForHumans() }}

    </small>

    <div
    id="replyReplyBox{{ $reply->id }}"
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

            <input
            type="hidden"
            name="parent_reply_id"
            value="{{ $reply->id }}">

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

@endforeach

    </div>

    @empty

    <p>

        No comments yet.

    </p>

    @endforelse

</div>

</div>

</body>

</html>