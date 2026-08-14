<!DOCTYPE html>

<html>
<head>

<title>Feedback Management</title>

<link rel="stylesheet"
href="{{ asset('css/style.css') }}">

</head>

<body class="dashboard-body">

<div class="back-btn">

<button onclick="history.back()">

←

</button>

</div>

<div class="page-container">

<h1>

💬 Feedback Management

</h1>

@if(session('success'))

<div class="toast-success">

{{ session('success') }}

</div>

@endif

@forelse($feedbacks as $feedback)

<div class="admin-booking-card">

<div class="booking-header">

<h2>

{{ $feedback->subject }}

</h2>

@if($feedback->status=='pending')

<span class="status-pending">

Pending

</span>

@elseif($feedback->status=='replied')

<span class="status-approved">

Replied

</span>

@else

<span class="status-approved">

Resolved

</span>

@endif

</div>

<div class="booking-grid">

<div>

<strong>User</strong>

<p>

{{ $feedback->user->name }}

</p>

</div>

<div>

<strong>Email</strong>

<p>

{{ $feedback->user->email }}

</p>

</div>

</div>

<div class="spot-card">

{{ $feedback->message }}

</div>

@if($feedback->reply)

<div class="spot-card">

<strong>

Reply

</strong>

<p>

{{ $feedback->reply }}

</p>

</div>

@endif

@if($feedback->status=='pending')

<form
method="POST"
action="/admin/feedback/reply/{{ $feedback->id }}">

@csrf

<textarea
name="reply"
rows="4"
placeholder="Write your reply..."
required></textarea>

<br><br>

<button
class="approve-btn">

Send Reply

</button>

</form>

@endif

<br>

<a
href="/admin/feedback/resolve/{{ $feedback->id }}"
class="view-btn">

Mark Resolved

</a>

<a
href="/admin/feedback/delete/{{ $feedback->id }}"
class="reject-btn"
onclick="return confirm('Delete feedback?')">

Delete

</a>

</div>

@empty

<div class="empty-card">

No feedback available.

</div>

@endforelse

</div>

</body>

</html>