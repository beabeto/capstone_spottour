<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">

<title>All Activities</title>

<link rel="stylesheet"
href="{{ asset('css/style.css') }}">

</head>
<body class="admin-body">

<div class="admin-main-full">

<div class="back-btn">

    <button onclick="history.back()">

        ← 

    </button>

</div>

<h1>

    🕒 Recent Activities

</h1>

<div class="activity-list">

@forelse($activities as $activity)

<div class="activity-card">

    <div class="activity-avatar">

@if(!empty($activity['profile_image']))

<img
src="{{ asset('uploads/profile/'.$activity['profile_image']) }}"
alt="Profile">

@else

<div class="activity-icon">

    {{ $activity['icon'] }}

</div>

@endif

</div>

    <div class="activity-content">

        <h4>

            {{ $activity['title'] }}

        </h4>

        <p>

            {{ $activity['message'] }}

        </p>

        <small>

            {{ \Carbon\Carbon::parse($activity['date'])->diffForHumans() }}

        </small>

    </div>

</div>

@empty

<div class="empty-state">

    No activities found.

</div>

@endforelse

</div>

</div>

</body>
</html>