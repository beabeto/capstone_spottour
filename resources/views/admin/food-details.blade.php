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

<!-- ADMIN ACTIONS -->

<div class="owner-listing-actions">
<a href="/admin/foods/{{ $food->id }}/menus" class="view-btn">Manage menus</a>
<a href="/admin/foods/{{ $food->id }}/menus/create" class="view-btn">Add menu</a>
@if($food->status === 'pending')
<a
href="/admin/foods/approve/{{ $food->id }}"
class="approve-btn">

         Approve

    </a>

    <a
href="/admin/foods/reject/{{ $food->id }}"
class="reject-btn">

     Reject

</a>
@endif

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

    <p>

         Status:
        {{ ucfirst($food->status) }}

    </p>

    <p>

         Owner:
        {{ $food->owner->name ?? 'N/A' }}

    </p>

    <p>

         Owner Email:
        {{ $food->owner->email ?? 'N/A' }}

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

         Owner Payment Information

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

<!-- FOOD RATING -->

<div class="spot-card">

    <h2>

         Food Rating

    </h2>

    @include('partials.rating-board', ['ratings' => $food->ratings])

    <form method="POST" action="/food/rate" class="rating-compose">
        @csrf
        <input type="hidden" name="food_id" value="{{ $food->id }}">
        <span>Your rating</span>
        @include('partials.star-input', ['inputId' => 'admin-food-'.$food->id, 'value' => $food->ratings->where('user_id', Auth::id())->sortByDesc('id')->first()?->rating, 'label' => 'Rate this restaurant'])
        <p class="rating-help">Select a star to submit your rating.</p>
    </form>

</div>

<!-- CUSTOMER COMMENTS -->

<div class="spot-card">

    <h2>

         Customer Reviews

    </h2>

    <form method="POST" action="/food/comment" enctype="multipart/form-data">
        @csrf
        <input type="hidden" name="food_id" value="{{ $food->id }}">
        <textarea name="comment" rows="4" placeholder="Write an administrator review..." required></textarea>
        <label class="comment-image-field">Add photo (optional)<input type="file" name="image" accept="image/jpeg,image/png,image/webp"></label>
        <button type="submit">Submit Comment</button>
    </form>

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
