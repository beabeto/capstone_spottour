<!DOCTYPE html>

<html lang="en">
<head>

<meta charset="UTF-8">

<meta name="viewport"
content="width=device-width, initial-scale=1.0">

<title>{{ $spot->name }}</title>

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

    @if($spot->image)

    <img
    src="{{ asset('uploads/spots/'.$spot->image) }}"
    class="hero-image">

    @endif

    <div class="hero-overlay">

        <h1>

            {{ $spot->name }}

        </h1>

        <p>

            📍 {{ $spot->location }}

        </p>

        <div class="hero-rating">

            ⭐

            {{ number_format(
                $spot->ratings()->avg('rating') ?? 0,
                1
            ) }}

            / 5.0

        </div>

    </div>

</div>

<!-- FAVORITE BOOKMARK CHECKLIST -->

<div class="spot-actions-top">

    <a
    href="/favorite/{{ $spot->id }}"
    class="favorite-btn">

        ❤️ Favorite

    </a>

    <a
    href="/bookmark/{{ $spot->id }}"
    class="bookmark-btn">

        🔖 Bookmark

    </a>

    <a
    href="/checklist/{{ $spot->id }}"
    class="checklist-btn">

        ✅ Checklist

    </a>

</div>

<!-- ABOUT -->

<div class="spot-card">

    <h2>

        About This Spot

    </h2>

    <p>

        {{ $spot->description }}

    </p>

    @if($spot->price > 0)

    <p>

        Entrance Fee:

        ₱{{ number_format(
            $spot->price,
            2
        ) }}

    </p>

    @endif

</div>

<!-- LOCATION -->

<div class="spot-card">

    <h2>

        Location

    </h2>

    <p>

        📍 {{ $spot->location }}

    </p>

    @if($spot->map_link)

    <a
    href="{{ $spot->map_link }}"
    target="_blank"
    class="map-btn">

        📍 Open In Google Maps

    </a>

    @endif

</div>

<!-- BOOKING -->

@if($spot->booking_type == 'cottage')

<div class="spot-card">

    <h2>

        Book Cottage

    </h2>

    <form
    method="GET"
    action="/booking/confirm">

        <input
        type="hidden"
        name="spot_id"
        value="{{ $spot->id }}">

        <label>

            Booking Date

        </label>

        <input
        type="date"
        name="booking_date"
        min="{{ date('Y-m-d') }}"
        required>

        <label>

            Number Of Cottages

        </label>

        <input
        type="number"
        name="quantity"
        min="1"
        max="{{ $spot->available_cottages }}"
        value="1">

        <p>

            Available:
            {{ $spot->available_cottages }}

        </p>

        <p>

            Cottage Price:
            ₱{{ number_format(
                $spot->cottage_price,
                2
            ) }}

        </p>

        <button
        type="submit">

            Proceed To Booking

        </button>

    </form>

</div>

@endif

<!-- SPOT RATING -->

<div class="spot-card">

    <h2>

        Rate This Spot

    </h2>

    <form
    method="POST"
    action="/spot/rate">

        @csrf

        <input
        type="hidden"
        name="spot_id"
        value="{{ $spot->id }}">

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
    action="/spot/comment">

        @csrf

        <input
        type="hidden"
        name="spot_id"
        value="{{ $spot->id }}">

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

    @forelse($spot->comments as $comment)

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

    <a href="/comments/edit/{{ $comment->id }}">
        Edit
    </a>

    <a href="/comments/delete/{{ $comment->id }}">
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
            href="/comment/{{ $comment->id }}/like">

                👍

                {{ $comment->reactions
                ->where('type','like')
                ->count() }}

            </a>

            <a
            href="/comment/{{ $comment->id }}/dislike">

                👎

                {{ $comment->reactions
                ->where('type','dislike')
                ->count() }}

            </a>

        </div>

        <!-- RATE COMMENT -->

        <form
        method="POST"
        action="/comment/rate">

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
action="/comment/reply">

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

                <a href="/reply/edit/{{ $reply->id }}">
                    Edit
                </a>

                <a href="/reply/delete/{{ $reply->id }}">
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
        action="/comment/reply">

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

<script>

function toggleMenu(id)
{
    let menu =
        document.getElementById(
            'menu' + id
        );

    if(menu)
    {
        menu.classList.toggle('show');
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
        menu.classList.toggle('show');
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

function toggleReplyReply(id)
{
    let box =
        document.getElementById(
            'replyReplyBox' + id
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
