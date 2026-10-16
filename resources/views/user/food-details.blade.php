<!DOCTYPE html>

<html>
<head>

<meta charset="UTF-8">

<meta name="viewport"
content="width=device-width, initial-scale=1.0">

<title>{{ $food->name }}</title>

<link rel="stylesheet"
href="{{ asset('css/style.css') }}">
<link rel="stylesheet" href="{{ asset('css/listing-details.css') }}">

</head>

<body class="dashboard-body listing-detail">

@php
    $isFavorite = \App\Models\FoodFavorite::where('user_id', Auth::id())->where('food_id', $food->id)->exists();
    $isBookmarked = \App\Models\FoodBookmark::where('user_id', Auth::id())->where('food_id', $food->id)->exists();
    $isChecklist = \App\Models\FoodChecklist::where('user_id', Auth::id())->where('food_id', $food->id)->exists();
@endphp

<div class="back-btn">

    <button type="button" onclick="window.history.back(); return false;">Back</button>

</div>

@if(session('success'))

<div id="toast" class="toast-success">

    {{ session('success') }}

</div>

@endif

<div class="spot-details-container">

<!-- HERO -->

<div class="spot-hero">

    @if($food->image)

    <img
    src="{{ asset('uploads/foods/'.$food->image) }}"
    alt="{{ $food->name }}"
    class="hero-image" onclick="this.classList.toggle('image-zoomed')" title="Click to enlarge">

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
    href="/food/favorite/{{ $food->id }}"
    class="favorite-btn{{ $isFavorite ? ' is-saved' : '' }}">

         {{ $isFavorite ? 'Remove Favorite' : 'Favorite' }}

    </a>

    <a
    href="/food/bookmark/{{ $food->id }}"
    class="bookmark-btn{{ $isBookmarked ? ' is-saved' : '' }}">

         {{ $isBookmarked ? 'Remove Bookmark' : 'Bookmark' }}

    </a>

    <a
    href="/food/checklist/{{ $food->id }}"
    class="checklist-btn{{ $isChecklist ? ' is-saved' : '' }}">

         {{ $isChecklist ? 'Remove Checklist' : 'Checklist' }}

    </a>

    <a href="/booking/food/{{ $food->id }}" class="book-now-btn">Book now</a>

</div>

<!-- ABOUT -->

@if(!empty($food->gallery_images))<section class="listing-gallery" aria-label="Restaurant photos">@foreach($food->gallery_images as $galleryImage)<img src="{{ asset('uploads/foods/'.$galleryImage) }}" alt="{{ $food->name }} photo" onclick="this.classList.toggle('image-zoomed')">@endforeach</section>@endif

<div class="spot-card">

    <h2>

        About This Food Establishment

    </h2>

    <p>

        {{ $food->description }}

    </p>

</div>

<!-- INFORMATION -->

<div class="spot-card">

    <h2>

        Restaurant Information

    </h2>

    <p>

         Available Tables:

        {{ $food->available_tables }}

    </p>

    <p>

         Venue Capacity:

        {{ $food->venue_capacity }}

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

    @else

    <p>

        No location link provided.

    </p>

    @endif

</div>

<!-- MENUS -->

<div class="spot-card">

    <h2>

         Food Menu

    </h2>

    @forelse($food->menus as $menu)

    <div class="comment-card menu-card">

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

        <div class="comment-body">

            <p>

                {{ $menu->description }}

            </p>

            <h3>

                PHP {{ number_format(
                    $menu->price,
                    2
                ) }}

            </h3>

        </div>

    </div>

    @empty

    <p>

        No Menu Items Available.

    </p>

    @endforelse

</div>

<div class="spot-card" id="booking">

<h2>

Reserve a Table

</h2>

<form
method="GET"
action="/food/booking/confirm">

<input
type="hidden"
name="food_id"
value="{{ $food->id }}">

<label>

Reservation Date

</label>

<input
type="date"
name="reservation_date"
class="availability-date"
data-listing-type="food"
data-listing-id="{{ $food->id }}"
min="{{ date('Y-m-d') }}"
required>

<label>

Reservation Time

</label>

<input
type="time"
name="reservation_time"
required>

<label>

Guests

</label>

<input
type="number"
name="guests"
min="1"
max="{{ $food->available_tables }}"
value="1">

<button>

Proceed To Reservation

</button>

</form>

</div>

<!-- RATINGS -->

<div class="spot-card">

    <h2>

        Ratings

    </h2>

    @include('partials.rating-board', ['ratings' => $food->ratings])

    <form method="POST" action="/food/rate" class="rating-compose">
        @csrf
        <input type="hidden" name="food_id" value="{{ $food->id }}">
        <span>Your rating</span>
        @include('partials.star-input', ['inputId' => 'user-food-'.$food->id, 'value' => $food->ratings->where('user_id', Auth::id())->sortByDesc('id')->first()?->rating, 'label' => 'Rate this restaurant'])
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
    action="/food/comment" enctype="multipart/form-data">

        @csrf

        <input
        type="hidden"
        name="food_id"
        value="{{ $food->id }}">

        <textarea
        name="comment"
        rows="4"
        placeholder="Write your review..."
        required></textarea>

        <label class="comment-image-field">Add photo (optional)<input type="file" name="image" accept="image/jpeg,image/png,image/webp"></label>

        <button type="submit">

            Submit Review

        </button>

    </form>

    <br>

    @forelse($food->comments as $comment)

    <div class="comment-card">

        <!-- COMMENT HEADER -->

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

                    {{ ucfirst($comment->user->role) }}

                </small>

            </div>

            <!-- MENU -->

            <div class="comment-menu">

                <button
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

                    <a href="/food/comments/edit/{{ $comment->id }}">

                        Edit

                    </a>

                    <a href="/food/comments/delete/{{ $comment->id }}">

                        Delete

                    </a>

                    @endif

                </div>

            </div>

        </div>

        <!-- BODY -->

        <div class="comment-body">

            <p>

            {{ $comment->comment }}

            @if($comment->image)<img src="{{ asset('uploads/comments/'.$comment->image) }}" class="review-image" alt="Review photo">@endif

            </p>

            <small>

                {{ $comment->created_at->diffForHumans() }}

            </small>

        </div>

        <!-- LIKE / DISLIKE -->

        <div class="comment-actions">

            <a href="/food/comment/{{ $comment->id }}/like">



                {{ $comment->reactions->where('type','like')->count() }}

            </a>

            <a href="/food/comment/{{ $comment->id }}/dislike">



                {{ $comment->reactions->where('type','dislike')->count() }}

            </a>

        </div>

        <!-- COMMENT RATING -->

        <div class="comment-rating">



            {{ number_format(
                $comment->ratings->avg('rating') ?? 0,
                1
            ) }}

        </div>

        <!-- RATE COMMENT -->

        <form
        method="POST"
        action="/food/comment/rate">

            @csrf

            <input
            type="hidden"
            name="comment_id"
            value="{{ $comment->id }}">

            <div class="star-rating star-rating-small" role="radiogroup" aria-label="Rate this review">
                @for($rating = 5; $rating >= 1; $rating--)
                    <input id="food-comment-{{ $comment->id }}-{{ $rating }}" type="radio" name="rating" value="{{ $rating }}" onchange="this.form.submit()"><label for="food-comment-{{ $comment->id }}-{{ $rating }}" title="{{ $rating }} star{{ $rating > 1 ? 's' : '' }}">&#9733;</label>
                @endfor
            </div>

        </form>

        <!-- REPLY BOX -->

        <div
        id="replyBox{{ $comment->id }}"
        class="reply-box"
        style="display:none;">

            <form
            method="POST"
            action="/food/comment/reply" enctype="multipart/form-data">

                @csrf

                <input
                type="hidden"
                name="comment_id"
                value="{{ $comment->id }}">

                <textarea
                name="reply"
                rows="2"
                placeholder="Write your reply..."
                required></textarea>

                <label class="comment-image-field">Add photo (optional)<input type="file" name="image" accept="image/jpeg,image/png,image/webp"></label>

                <button type="submit">

                    Reply

                </button>

            </form>

        </div>

        <!-- REPLIES -->

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

            </div>

            <div class="reply-content">

                {{ $reply->reply }}

                @if($reply->image)<img src="{{ asset('uploads/comments/'.$reply->image) }}" class="review-image" alt="Reply photo">@endif

            </div>

            <form method="POST" action="/food/reply/rate" class="reply-rating-form" aria-label="Rate this reply">
                @csrf<input type="hidden" name="reply_id" value="{{ $reply->id }}"><span>Rate reply</span>
                @include('partials.star-input', ['inputId' => 'food-reply-'.$reply->id, 'value' => $reply->ratings->firstWhere('user_id', Auth::id())?->rating, 'small' => true, 'label' => 'Rate this reply'])
            </form>

            <small class="reply-time">

                {{ $reply->created_at->diffForHumans() }}

            </small>

        </div>

        @endforeach

    </div>

    @empty

    <p>

        No reviews yet.

    </p>

    @endforelse

</div>

</div>

<script>

function toggleMenu(id)
{
    document
        .getElementById('menu' + id)
        .classList
        .toggle('show');
}

function toggleReplyBox(id)
{
    let box =
        document.getElementById(
            'replyBox' + id
        );

    box.style.display =
        box.style.display === 'none'
        ? 'block'
        : 'none';
}


</script>

</body>

</html>
