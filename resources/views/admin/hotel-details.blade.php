<!DOCTYPE html>

<html>
<head>

<meta charset="UTF-8">

<meta name="viewport"
content="width=device-width, initial-scale=1.0">

<title>{{ $hotel->name }}</title>

<link rel="stylesheet"
href="{{ asset('css/style.css') }}">

</head>

<body class="dashboard-body admin-detail-page">

<div class="back-btn">
    <button type="button" onclick="window.history.back(); return false;">Back</button>
</div>

@if(session('success'))

<div class="success">

    {{ session('success') }}

</div>

@endif

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

             {{ $hotel->location }}

        </p>

        <div class="hero-rating">
            @include('partials.score-stars', ['score' => $hotel->ratings->avg('rating') ?? 0])
        </div>

    </div>

</div>

<!-- ADMIN ACTIONS -->

<div class="owner-listing-actions">
    <a href="/admin/hotels/{{ $hotel->id }}/rooms" class="view-btn">Manage rooms</a>
@if($hotel->status === 'pending')
    <a
    href="/admin/hotels/approve/{{ $hotel->id }}"
    class="approve-btn">

         Approve

    </a>

    <a
    href="/admin/hotels/reject/{{ $hotel->id }}"
    class="reject-btn">

         Reject

    </a>
@endif

</div>

<!-- HOTEL INFO -->

<div class="spot-card">

    <h2>

        About This Hotel

    </h2>

    <p>

        {{ $hotel->description }}

    </p>

    <br>

    <p>

         Price Per Night:

        PHP {{ number_format(
            $hotel->price_per_night,
            2
        ) }}

    </p>

    <p>

         Available Rooms:

        {{ $hotel->available_rooms }}

    </p>

    <p>

         Amenities:

        {{ $hotel->amenities }}

    </p>

    <p>

        Status:

        {{ ucfirst($hotel->status) }}

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

<!-- ROOM INFO -->

<div class="reservation-card">

    <h2>

        Reservation Information

    </h2>

    <div class="reservation-grid">

        <div>

            <small>

                Available Rooms

            </small>

            <h3>

                {{ $hotel->available_rooms }}

            </h3>

        </div>

        <div>

            <small>

                Price Per Night

            </small>

            <h3>

                PHP {{ number_format(
                    $hotel->price_per_night,
                    2
                ) }}

            </h3>

        </div>

    </div>

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
        @include('partials.star-input', ['inputId' => 'admin-hotel-'.$hotel->id, 'value' => $hotel->ratings->where('user_id', Auth::id())->sortByDesc('id')->first()?->rating, 'label' => 'Rate this hotel'])
        <p class="rating-help">Select a star to submit your rating.</p>
    </form>

</div>

<!-- COMMENTS -->

<div class="spot-card">

    <h2>

        Comments & Reviews

    </h2>

    <form method="POST" action="/hotel/comment" enctype="multipart/form-data">

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

        <label class="comment-image-field">Add photo (optional)<input type="file" name="image" accept="image/jpeg,image/png,image/webp"></label>

        <button type="submit">
            Submit Comment
        </button>

    </form>

    <br>

    @forelse($hotel->comments as $comment)

    <div class="comment-card">

        <div class="comment-header">

            <img
            src="{{ $comment->user && $comment->user->profile_image
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

        </div>

        <div class="comment-body">

            <p>

                {{ $comment->comment }}

            </p>

            @if($comment->image)<img src="{{ asset('uploads/comments/'.$comment->image) }}" class="review-image" alt="Review photo">@endif

            <small>

                {{ $comment->created_at->diffForHumans() }}

            </small>

        </div>

        <div class="comment-rating">
            @include('partials.score-stars', ['score' => $comment->ratings->avg('rating') ?? 0])
        </div>

        <form method="POST" action="/hotel/comment/reply" enctype="multipart/form-data">
            @csrf
            <input type="hidden" name="comment_id" value="{{ $comment->id }}">
            <textarea name="reply" rows="2" placeholder="Write a reply..." required></textarea>
            <label class="comment-image-field">Add photo (optional)<input type="file" name="image" accept="image/jpeg,image/png,image/webp"></label>
            <button type="submit">Reply</button>
        </form>

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

                {{ $reply->user->name }}

            </strong>

            <small>

                {{ ucfirst($reply->user->role) }}

            </small>

        </div>

    </div>

    <div class="reply-body">

        <p>

            {{ $reply->reply }}

        </p>

        @if($reply->image)<img src="{{ asset('uploads/comments/'.$reply->image) }}" class="review-image" alt="Reply photo">@endif

        <small>

            {{ $reply->created_at->diffForHumans() }}

        </small>

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
