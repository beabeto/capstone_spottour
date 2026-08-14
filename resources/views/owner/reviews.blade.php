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

<div class="spot-actions-top">

    <a
    href="/owner/reviews"
    class="view-btn">

        ⭐ Reviews

    </a>

    <a
    href="/owner/feedback"
    class="bookmark-btn">

        💬 Feedback Inbox

    </a>

</div>

<br>

@forelse($comments as $comment)

<a

@if(isset($comment->food))

href="/owner/foods/{{ $comment->food_id }}"

@elseif(isset($comment->hotel))

href="/owner/hotels/{{ $comment->hotel_id }}"

@else

href="/owner/spots/{{ $comment->spot_id }}"

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

            @if(isset($comment->food))

                🍽

                <strong>

                    {{ $comment->food->name }}

                </strong>

            @elseif(isset($comment->hotel))

                🏨

                <strong>

                    {{ $comment->hotel->name }}

                </strong>

            @else

                📍

                <strong>

                    {{ $comment->spot->name }}

                </strong>

            @endif

        </p>

        <div class="review-stats">

            <span>

                ⭐ Listing Rating:

                @if(isset($comment->food))

                    {{ number_format(
                        $comment->food->ratings->avg('rating') ?? 0,
                        1
                    ) }}

                @elseif(isset($comment->hotel))

                    {{ number_format(
                        $comment->hotel->ratings->avg('rating') ?? 0,
                        1
                    ) }}

                @else

                    {{ number_format(
                        $comment->spot->ratings->avg('rating') ?? 0,
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
                    ->where('type','like')
                    ->count() }}

            </span>

            <span>

                👎

                {{ $comment->reactions
                    ->where('type','dislike')
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