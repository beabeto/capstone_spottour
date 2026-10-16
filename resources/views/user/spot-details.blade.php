<!DOCTYPE html>

<html lang="en">
<head>

<meta charset="UTF-8">

<meta name="viewport"
content="width=device-width, initial-scale=1.0">

<title>{{ $spot->name }}</title>

<link rel="stylesheet"
href="{{ asset('css/style.css') }}">
<link rel="stylesheet" href="{{ asset('css/listing-details.css') }}">

</head>

<body class="dashboard-body listing-detail">

@php
    $isFavorite = \App\Models\Favorite::where('user_id', Auth::id())->where('spot_id', $spot->id)->exists();
    $isBookmarked = \App\Models\Bookmark::where('user_id', Auth::id())->where('spot_id', $spot->id)->exists();
    $isChecklist = \App\Models\Checklist::where('user_id', Auth::id())->where('spot_id', $spot->id)->exists();
@endphp

@if(session('success'))

<div id="toast" class="toast-success">

    {{ session('success') }}

</div>

@endif

<!-- BACK -->

<div class="back-btn">
    <button type="button" onclick="window.history.back(); return false;">Back</button>
</div>

<div class="spot-details-container">

<!-- HERO -->

<div class="spot-hero">

    @if($spot->image)

        <img
        src="{{ asset('uploads/spots/'.$spot->image) }}"
        class="hero-image" onclick="this.classList.toggle('image-zoomed')" title="Click to enlarge">

    @endif

    <div class="hero-overlay">

        <h1>

            {{ $spot->name }}

        </h1>

        <p>

             {{ $spot->location }}

        </p>

        <div class="hero-rating">
            @include('partials.score-stars', ['score' => $spot->ratings->avg('rating') ?? 0])
        </div>

    </div>

</div>

<!-- FAVORITE BOOKMARK CHECKLIST -->

<div class="spot-actions-top">

    <a
    href="/favorite/{{ $spot->id }}"
    class="favorite-btn{{ $isFavorite ? ' is-saved' : '' }}">

         {{ $isFavorite ? 'Remove Favorite' : 'Favorite' }}

    </a>

    <a
    href="/bookmark/{{ $spot->id }}"
    class="bookmark-btn{{ $isBookmarked ? ' is-saved' : '' }}">

         {{ $isBookmarked ? 'Remove Bookmark' : 'Bookmark' }}

    </a>

    <a
    href="/checklist/{{ $spot->id }}"
    class="checklist-btn{{ $isChecklist ? ' is-saved' : '' }}">

         {{ $isChecklist ? 'Remove Checklist' : 'Checklist' }}

    </a>

    <a href="/booking/spot/{{ $spot->id }}" class="book-now-btn">Book now</a>

</div>

<!-- DETAILS -->

@if(!empty($spot->gallery_images))<section class="listing-gallery" aria-label="Spot photos">@foreach($spot->gallery_images as $galleryImage)<img src="{{ asset('uploads/spots/'.$galleryImage) }}" alt="{{ $spot->name }} photo" onclick="this.classList.toggle('image-zoomed')">@endforeach</section>@endif

<div class="spot-card">

    <h2>

        About This Spot

    </h2>

    <p>

        {{ $spot->description }}

    </p>

    <br>

    @if($spot->price > 0)

<p>

    Entrance Fee:

    PHP {{ number_format($spot->price,2) }}

</p>

@endif

</div>

<!-- LOCATION -->

<div class="spot-card">

    <h2>

        Location

    </h2>

    <p>

         {{ $spot->location }}

    </p>

    @if($spot->map_link)

    <a
    href="{{ $spot->map_link }}"
    target="_blank"
    class="map-btn">

         Open In Google Maps

    </a>

    @endif

</div>


<div class="spot-card" id="booking">

    <h2>Book This Spot</h2>

    <form method="GET" action="/booking/confirm">

        <input type="hidden"
               name="spot_id"
               value="{{ $spot->id }}">

        <label>Booking Date</label>

        <input type="date"
               name="booking_date"
               class="availability-date"
               data-listing-type="spot"
               data-listing-id="{{ $spot->id }}"
               min="{{ date('Y-m-d') }}"
               required>

        @if($spot->booking_type == 'cottage')

        <label>Number Of Cottages</label>

        <input type="number"
               name="quantity"
               min="1"
               max="{{ $spot->available_cottages }}"
               value="1">

        <div class="booking-info">

            <p>
                Available Cottages:
                {{ $spot->available_cottages }}
            </p>

            <p>
                Cottage Price:
                PHP {{ number_format($spot->cottage_price,2) }}
            </p>

        </div>

        <div class="booking-estimate" aria-live="polite">
            Estimated total <strong id="spotBookingTotal" data-price="{{ $spot->cottage_price }}">PHP {{ number_format($spot->cottage_price, 2) }}</strong>
        </div>

        @else

        <input type="hidden" name="quantity" value="1">

        <div class="booking-info">
            <p>Entrance Fee: PHP {{ number_format($spot->price, 2) }}</p>
            <p>One guest reservation</p>
        </div>

        <div class="booking-estimate">
            Amount to pay <strong>PHP {{ number_format($spot->price, 2) }}</strong>
        </div>

        @endif

        <button type="submit">
            Proceed To Booking
        </button>

    </form>

</div>

<!-- RATINGS -->

<div class="spot-card">

    <h2>

        Ratings

    </h2>

    @include('partials.rating-board', ['ratings' => $spot->ratings])

    <form method="POST" action="/spot/rate" class="rating-compose">
        @csrf
        <input type="hidden" name="spot_id" value="{{ $spot->id }}">
        <span>Your rating</span>
        @include('partials.star-input', ['inputId' => 'user-spot-'.$spot->id, 'value' => $spot->ratings->where('user_id', Auth::id())->sortByDesc('id')->first()?->rating, 'label' => 'Rate this spot'])
        <p class="rating-help">Select a star to submit your rating.</p>
    </form>

</div>

<!-- COMMENTS -->

<div class="spot-card">

<h2>Comments & Reviews</h2>

<form method="POST" action="/spot/comment" enctype="multipart/form-data">

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

    <label class="comment-image-field">Add photo (optional)<input type="file" name="image" accept="image/jpeg,image/png,image/webp"></label>

    <button type="submit">
        Submit Comment
    </button>

</form>

<br>

@forelse($spot->comments as $comment)

<div
id="comment{{ $comment->id }}"
class="comment-card">

    <!-- COMMENT HEADER -->

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

    <!-- COMMENT BODY -->

    <div class="comment-body">

        <p>{{ $comment->comment }}</p>

        @if($comment->image)<img src="{{ asset('uploads/comments/'.$comment->image) }}" class="review-image" alt="Review photo">@endif

        <small>
            {{ $comment->created_at->diffForHumans() }}
        </small>

    </div>

    <!-- COMMENT RATING -->

    <div class="comment-rating">

         Comment Rating:

        {{ number_format(
            $comment->ratings->avg('rating') ?? 0,
            1
        ) }}

    </div>

    <!-- LIKE DISLIKE -->

    <div class="comment-actions">

        <a href="/comment/{{ $comment->id }}/like">



            {{ $comment->reactions
                ->where('type','like')
                ->count() }}

        </a>

        <a href="/comment/{{ $comment->id }}/dislike">



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

        <div class="star-rating star-rating-small" role="radiogroup" aria-label="Rate this review">
            @for($rating = 5; $rating >= 1; $rating--)
                <input id="spot-comment-{{ $comment->id }}-{{ $rating }}" type="radio" name="rating" value="{{ $rating }}" onchange="this.form.submit()"><label for="spot-comment-{{ $comment->id }}-{{ $rating }}" title="{{ $rating }} star{{ $rating > 1 ? 's' : '' }}">&#9733;</label>
            @endfor
        </div>

    </form>

    <!-- REPLY TO COMMENT -->

    <div
    id="replyBox{{ $comment->id }}"
    class="reply-box"
    style="display:none;">

        <form
        method="POST"
        action="/comment/reply" enctype="multipart/form-data">

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

    <!-- REPLIES -->

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

                    <!-- EVERYONE CAN REPLY -->

                    <a href="javascript:void(0)"
                       onclick="toggleReplyToReply({{ $reply->id }})">

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

            @if($reply->image)<img src="{{ asset('uploads/comments/'.$reply->image) }}" class="review-image" alt="Reply photo">@endif

        </div>

        <form method="POST" action="/reply/rate" class="reply-rating-form" aria-label="Rate this reply">
            @csrf<input type="hidden" name="reply_id" value="{{ $reply->id }}"><span>Rate reply</span>
            @include('partials.star-input', ['inputId' => 'spot-reply-'.$reply->id, 'value' => $reply->ratings->firstWhere('user_id', Auth::id())?->rating, 'small' => true, 'label' => 'Rate this reply'])
        </form>

        <small class="reply-time">

            {{ $reply->created_at->diffForHumans() }}

        </small>

        <!-- REPLY TO REPLY -->

        <div
        id="replyToReply{{ $reply->id }}"
        class="reply-box"
        style="display:none;">

            <form
            method="POST"
            action="/comment/reply" enctype="multipart/form-data">

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

    @endforeach

</div>

@empty

<p>No comments yet.</p>

@endforelse

</div>

<script>

function toggleMenu(id)
{
    let menu = document.getElementById(
        'menu' + id
    );

    if(menu)
    {
        menu.classList.toggle('show');
    }
}

function toggleReplyMenu(id)
{
    let menu = document.getElementById(
        'replyMenu' + id
    );

    if(menu)
    {
        menu.classList.toggle('show');
    }
}

function toggleReplyBox(id)
{
    let box = document.getElementById(
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
    let box = document.getElementById(
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

const cottageQuantity = document.querySelector('input[name="quantity"]');
const spotBookingTotal = document.getElementById('spotBookingTotal');

if (cottageQuantity && spotBookingTotal)
{
    const refreshSpotTotal = () => {
        const total = Math.max(1, Number(cottageQuantity.value || 1)) * Number(spotBookingTotal.dataset.price || 0);
        spotBookingTotal.textContent = 'PHP ' + total.toLocaleString('en-PH', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
    };

    cottageQuantity.addEventListener('input', refreshSpotTotal);
    refreshSpotTotal();
}


</script>

<script src="{{ asset('js/availability-calendar.js') }}"></script>
</body>
</html>
