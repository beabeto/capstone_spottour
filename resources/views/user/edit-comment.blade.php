<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport"
content="width=device-width, initial-scale=1.0">

<title>Edit Comment</title>

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

    <div class="spot-card">

        <h2>✏️ Edit Comment</h2>

        <form
        method="POST"
        action="/comments/update/{{ $comment->id }}">

            @csrf

            <textarea
            name="comment"
            rows="6"
            required>{{ $comment->comment }}</textarea>

            <button
            type="submit"
            class="save-btn">

                Save Changes

            </button>

        </form>

    </div>

</div>

</body>
</html>