<!DOCTYPE html>
<html>
<head>
<title>Feedback</title>
<link rel="stylesheet" href="{{ asset('css/style.css') }}">
<link rel="stylesheet" href="{{ asset('css/user-dashboard.css') }}">
<link rel="stylesheet" href="{{ asset('css/account-pages.css') }}">
</head>
<body class="dashboard-body account-page feedback-page">
@include('user.partials.account-bar')
<main class="page-container">
    <header class="management-heading">
        <span>Your account</span>
        <h1>Feedback</h1>
        <p>Send a note to the administrator and follow the reply here.</p>
    </header>
    @if(session('success'))
        <p class="feedback-note">{{ session('success') }}</p>
    @endif
    <form action="/user/feedback" method="POST" class="feedback-form">
        @csrf
        <label>Subject<input type="text" name="subject" placeholder="What is this about?" required></label>
        <label>Message<textarea name="message" rows="5" placeholder="Write your feedback..." required></textarea></label>
        <button type="submit">Send feedback</button>
    </form>
    <section class="feedback-list">
        <h2>My messages</h2>
        @forelse($feedbacks as $feedback)
            <article class="feedback-card">
                <header>
                    <div>
                        <h3>{{ $feedback->subject }}</h3>
                        <small>{{ $feedback->created_at->diffForHumans() }}</small>
                    </div>
                    <span class="feedback-status is-{{ $feedback->status }}">{{ ucfirst($feedback->status) }}</span>
                </header>
                <p>{{ $feedback->message }}</p>
                @if($feedback->reply)
                    <div class="feedback-reply">
                        <strong>Admin{{ $feedback->admin?->name ? ' · '.$feedback->admin->name : '' }}</strong>
                        @if($feedback->replied_at)
                            <small style="display:block;margin:2px 0 8px;color:#607772">{{ $feedback->replied_at->diffForHumans() }}</small>
                        @endif
                        <p>{{ $feedback->reply }}</p>
                    </div>
                @endif
            </article>
        @empty
            <p class="feedback-empty">No feedback submitted yet.</p>
        @endforelse
    </section>
</main>
</body>
</html>
