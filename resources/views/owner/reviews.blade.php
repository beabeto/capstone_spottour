<!DOCTYPE html>

<html>
<head>

<title>Reviews</title>

<link rel="stylesheet"
href="{{ asset('css/style.css') }}">

</head>

<body class="dashboard-body">

<div class="back-btn">
    <button onclick="history.back()">
        ← 
    </button>
</div>

<div class="notifications-container">

<h1>

⭐ Reviews & Feedback

</h1>

@forelse($comments as $comment)

<a

@if(isset($comment->spot))

href="/owner/spots/{{ $comment->spot_id }}"

@else

href="/owner/hotels/{{ $comment->hotel_id }}"

@endif

class="review-link">

<div class="notification-card">

    <div class="notification-avatar">

        <img
        src="{{ $comment->user && $comment->user->profile_image
            ? asset('uploads/profile/'.$comment->user->profile_image)
            : 'https://cdn-icons-png.flaticon.com/512/3135/3135715.png' }}">

    </div>

    <div class="notification-content">

        <h3>

            {{ $comment->user->name }}

        </h3>

        <p>

            @if(isset($comment->spot))

                📍

                <strong>

                    {{ $comment->spot->name }}

                </strong>

            @else

                🏨

                <strong>

                    {{ $comment->hotel->name }}

                </strong>

            @endif

        </p>

        <div class="review-stats">

            <span>

                ⭐ Listing Rating:

                @if(isset($comment->spot))

                    {{ number_format(
                        $comment->spot->ratings->avg('rating') ?? 0,
                        1
                    ) }}

                @else

                    {{ number_format(
                        $comment->hotel->ratings->avg('rating') ?? 0,
                        1
                    ) }}

                @endif

            </span>

            <span>

                ⭐ Comment Rating:

                {{ number_format(
                    $comment->ratings->avg('rating') ?? 0,
                    1
                ) }}

            </span>

        </div>

        <div class="review-reactions">

            <span>

                👍

                {{ $comment->reactions
                    ->where('reaction','like')
                    ->count() }}

            </span>

            <span>

                👎

                {{ $comment->reactions
                    ->where('reaction','dislike')
                    ->count() }}

            </span>

        </div>

        <div class="review-text">

            "{{ $comment->comment }}"

        </div>

        <small>

            {{ $comment->created_at->diffForHumans() }}

        </small>

    </div>

    <div class="notification-arrow">

        →

    </div>

</div>

</a>

@empty

<div class="empty-state">

    No reviews yet.

</div>

@endforelse

</div>

</body>

</html>