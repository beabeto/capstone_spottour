<!DOCTYPE html>

<html>
<head>

<title>Feedback</title>

<link rel="stylesheet"
href="{{ asset('css/style.css') }}">

</head>

<body class="dashboard-body">

<div class="back-btn">

    <button onclick="history.back()">

        ←

    </button>

</div>

<div class="dashboard-container">

<h1>

💬 Send Feedback

</h1>

@if(session('success'))

<div class="toast-success">

    {{ session('success') }}

</div>

@endif

<form
action="/owner/feedback"
method="POST"
class="spot-card">

@csrf

<label>

Subject

</label>

<input
type="text"
name="subject"
required>

<label>

Message

</label>

<textarea
name="message"
rows="6"
required></textarea>

<button
type="submit"
class="view-btn">

Send Feedback

</button>

</form>

<br>

<h2>

📨 My Feedback

</h2>

@forelse($feedbacks as $feedback)

<div class="notification-card">

<div class="notification-content">

<h3>

{{ $feedback->subject }}

</h3>

<p>

{{ $feedback->message }}

</p>

<p>

<strong>Status:</strong>

@if($feedback->status=='pending')

<span class="badge-pending">

Pending

</span>

@elseif($feedback->status=='replied')

<span class="badge-approved">

Replied

</span>

@else

<span class="badge-approved">

Resolved

</span>

@endif

</p>

@if($feedback->reply)

<div class="spot-card">

<strong>

Admin Reply

</strong>

<p>

{{ $feedback->reply }}

</p>

</div>

@endif

<small>

{{ $feedback->created_at->diffForHumans() }}

</small>

</div>

</div>

@empty

<div class="empty-card">

No feedback submitted.

</div>

@endforelse

</div>

</body>

</html>