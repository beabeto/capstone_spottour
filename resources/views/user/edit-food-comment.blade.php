<!DOCTYPE html>

<html>

<head>

<title>Edit Comment</title>

<link rel="stylesheet" href="{{ asset('css/style.css') }}">
<link rel="stylesheet" href="{{ asset('css/listing-details.css') }}">

</head>

<body class="dashboard-body listing-detail">

<div class="page-container">

<h2>Edit Comment</h2>

<form method="POST"
action="/food/comments/update/{{ $comment->id }}">

    @csrf

    <textarea
    name="comment"
    rows="6"
    required>{{ $comment->comment }}</textarea>

    <br><br>

    <button type="submit">

        Update Comment

    </button>

</form>

</div>

</body>

</html>
