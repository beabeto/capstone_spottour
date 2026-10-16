<!DOCTYPE html>

<html lang="en">
<head>

<meta charset="UTF-8">

<meta name="viewport"
content="width=device-width, initial-scale=1.0">

<title>{{ $hotel->name }}</title>

<link rel="stylesheet"
href="{{ asset('css/style.css') }}">
<link rel="stylesheet" href="{{ asset('css/listing-details.css') }}">

</head>

<body class="dashboard-body listing-detail">

@php
    $isFavorite = \App\Models\HotelFavorite::where('user_id', Auth::id())->where('hotel_id', $hotel->id)->exists();
    $isBookmarked = \App\Models\HotelBookmark::where('user_id', Auth::id())->where('hotel_id', $hotel->id)->exists();
    $isChecklist = \App\Models\HotelChecklist::where('user_id', Auth::id())->where('hotel_id', $hotel->id)->exists();
@endphp

@if(session('success'))

<div id="toast" class="toast-success">

    {{ session('success') }}

</div>

@endif

<!-- BACK -->

<div class="back-btn">
    <button onclick="window.history.back(); return false;">
         Back
    </button>
</div>

<div class="spot-details-container">

<!-- HERO -->

<div class="spot-hero">

    @if($hotel->image)

    <img
    src="{{ asset('uploads/hotels/'.$hotel->image) }}"
    class="hero-image" onclick="this.classList.toggle('image-zoomed')" title="Click to enlarge">

    @endif

    <div class="hero-overlay">

        <h1>

            {{ $hotel->name }}

        </h1>

        <p>

             {{ $hotel->location }}

        </p>

        <div class="hero-rating">
            @include('partials.score-stars', ['score' => $hotel->ratings->avg('rating') ?? 0])
        </div>

    </div>

</div>

<!-- FAVORITE BOOKMARK CHECKLIST -->

<div class="spot-actions-top">

    <a
    href="/hotel/favorite/{{ $hotel->id }}"
    class="favorite-btn{{ $isFavorite ? ' is-saved' : '' }}">

         {{ $isFavorite ? 'Remove Favorite' : 'Favorite' }}

    </a>

    <a
    href="/hotel/bookmark/{{ $hotel->id }}"
    class="bookmark-btn{{ $isBookmarked ? ' is-saved' : '' }}">

         {{ $isBookmarked ? 'Remove Bookmark' : 'Bookmark' }}

    </a>

    <a
    href="/hotel/checklist/{{ $hotel->id }}"
    class="checklist-btn{{ $isChecklist ? ' is-saved' : '' }}">

         {{ $isChecklist ? 'Remove Checklist' : 'Checklist' }}

    </a>

    <a href="/booking/hotel/{{ $hotel->id }}" class="book-now-btn">Book now</a>

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

    @if($hotel->rooms->isNotEmpty())<div class="room-types-grid">@foreach($hotel->rooms as $room)<article>@if($room->cover_image)<img src="{{ asset('uploads/hotel-rooms/'.$room->cover_image) }}" alt="{{ $room->name }}">@endif<strong>{{ $room->name }}</strong><span>PHP {{ number_format($room->price_per_night, 2) }} / night</span><small>{{ $room->available_rooms }} room(s) available</small>@if($room->gallery_images)<div class="room-gallery">@foreach($room->gallery_images as $image)<img src="{{ asset('uploads/hotel-rooms/'.$image) }}" alt="{{ $room->name }} photo" onclick="this.classList.toggle('image-zoomed')">@endforeach</div>@endif</article>@endforeach</div>@endif

    @if($hotel->rooms->isEmpty())
    <p>

         Price Per Night:

        PHP {{ number_format(
            $hotel->price_per_night,
            2
        ) }}

    </p>
    @endif

    <p>

         Available Rooms:

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

         {{ $hotel->location }}

    </p>

    @if($hotel->map_link)

    <a
    href="{{ $hotel->map_link }}"
    target="_blank"
    class="map-btn">

         Open In Google Maps

    </a>

    @endif

</div>

<!-- HOTEL RESERVATION -->

<div class="spot-card" id="booking">

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
        class="availability-date"
        data-listing-type="hotel"
        data-listing-id="{{ $hotel->id }}"
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
                PHP {{ number_format(
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

    @include('partials.rating-board', ['ratings' => $hotel->ratings])

    <form method="POST" action="/hotel/rate" class="rating-compose">
        @csrf
        <input type="hidden" name="hotel_id" value="{{ $hotel->id }}">
        <span>Your rating</span>
        @include('partials.star-input', ['inputId' => 'user-hotel-'.$hotel->id, 'value' => $hotel->ratings->where('user_id', Auth::id())->sortByDesc('id')->first()?->rating, 'label' => 'Rate this hotel'])
        <p class="rating-help">Select a star to submit your rating.</p>
    </form>

</div>

<!-- COMMENTS -->

<div class="spot-card">

    <h2>

        Comments & Reviews

    </h2>

    <form
    method="POST"
    action="/hotel/comment" enctype="multipart/form-data">

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

    @if($comment->image)<img src="{{ asset('uploads/comments/'.$comment->image) }}" class="review-image" alt="Review photo">@endif

    </p>

    <small>

        {{ $comment->created_at->diffForHumans() }}

    </small>

</div>

<div class="comment-rating">

     Comment Rating:

    {{ number_format(
        $comment->ratings->avg('rating') ?? 0,
        1
    ) }}

</div>

<div class="comment-actions">

    <a href="/hotel/comment/{{ $comment->id }}/like">



        {{ $comment->reactions
            ->where('reaction','like')
            ->count() }}

    </a>

    <a href="/hotel/comment/{{ $comment->id }}/dislike">



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

        <div class="star-rating star-rating-small" role="radiogroup" aria-label="Rate this review">
            @for($rating = 5; $rating >= 1; $rating--)
                <input id="hotel-comment-{{ $comment->id }}-{{ $rating }}" type="radio" name="rating" value="{{ $rating }}" onchange="this.form.submit()"><label for="hotel-comment-{{ $comment->id }}-{{ $rating }}" title="{{ $rating }} star{{ $rating > 1 ? 's' : '' }}">&#9733;</label>
            @endfor
        </div>

    </form>

    <div
id="replyBox{{ $comment->id }}"
class="reply-box"
style="display:none;">

<form
method="POST"
action="/hotel/comment/reply" enctype="multipart/form-data">

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

    <label class="comment-image-field">Add photo (optional)<input type="file" name="image" accept="image/jpeg,image/png,image/webp"></label>

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

    @if($reply->image)<img src="{{ asset('uploads/comments/'.$reply->image) }}" class="review-image" alt="Reply photo">@endif

</p>

<form method="POST" action="/hotel/reply/rate" class="reply-rating-form" aria-label="Rate this reply">
    @csrf<input type="hidden" name="reply_id" value="{{ $reply->id }}"><span>Rate reply</span>
    @include('partials.star-input', ['inputId' => 'hotel-reply-'.$reply->id, 'value' => $reply->ratings->firstWhere('user_id', Auth::id())?->rating, 'small' => true, 'label' => 'Rate this reply'])
</form>

<small>

    {{ $reply->created_at->diffForHumans() }}

</small>

    </div>

    <div class="comment-menu">

        <button
        type="button"
        class="menu-btn"
        onclick="toggleReplyMenu({{ $reply->id }})">



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
action="/hotel/comment/reply" enctype="multipart/form-data">

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

    <label class="comment-image-field">Add photo (optional)<input type="file" name="image" accept="image/jpeg,image/png,image/webp"></label>

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

<script src="{{ asset('js/availability-calendar.js') }}"></script>
</body>

</html>
