<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport"
content="width=device-width, initial-scale=1.0">

<title>Edit Reply</title>

<link rel="stylesheet"
href="{{ asset('css/style.css') }}">
<link rel="stylesheet" href="{{ asset('css/listing-details.css') }}">
</head>
<body class="dashboard-body listing-detail">

<div class="back-btn">
    <button type="button" onclick="window.history.back(); return false;">Back</button>
</div>

<div class="dashboard-container">

    <div class="spot-card">

        <h2>Edit Reply</h2>

        <form
        method="POST"
        action="/reply/update/{{ $reply->id }}">

            @csrf

            <textarea
            name="reply"
            rows="5"
            required>{{ $reply->reply }}</textarea>

            <button type="submit">
                Save Changes
            </button>

        </form>

    </div>

</div>

</body>
</html>
