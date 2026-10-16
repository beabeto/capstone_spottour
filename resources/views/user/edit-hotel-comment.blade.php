<!DOCTYPE html>

<html>
<head>

<title>Edit Comment</title>

<link rel="stylesheet"
href="{{ asset('css/style.css') }}">
<link rel="stylesheet" href="{{ asset('css/listing-details.css') }}">

</head>

<body class="dashboard-body listing-detail">

<div class="back-btn">
    <button type="button" onclick="window.history.back(); return false;">Back</button>
</div>

<div class="spot-card">

<h2>

Edit Comment

</h2>

<form
method="POST"
action="/hotel/comments/update/{{ $comment->id }}">

    @csrf

    <textarea
    name="comment"
    rows="5"
    required>{{ $comment->comment }}</textarea>

    <br><br>

    <button type="submit">

        Update Comment

    </button>

</form>

</div>

</body>
</html>
