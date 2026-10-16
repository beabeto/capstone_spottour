<!DOCTYPE html>
<html>
<head>
<title>Feedback</title>
<link rel="stylesheet" href="{{ asset('css/style.css') }}">
</head>
<body class="admin-body feedback-page">
<div class="back-btn">
    <button type="button" onclick="window.history.back(); return false;">Back</button>
</div>
<main class="page-container">
    <header class="management-heading">
        <span>Administrator</span>
        <h1>Feedback</h1>
        <p>{{ $feedbacks->where('status', 'pending')->count() }} waiting, {{ $feedbacks->where('status', 'replied')->count() }} replied, {{ $feedbacks->where('status', 'resolved')->count() }} resolved.</p>
    </header>
    @if(session('success'))
        <p class="feedback-note">{{ session('success') }}</p>
    @endif
    @forelse($feedbacks as $feedback)
        <article class="feedback-card">
            <header>
                <div>
                    <h3>{{ $feedback->subject }}</h3>
                    <small>{{ $feedback->user?->name ?? 'Deleted user' }} · {{ $feedback->user?->email ?? 'No email' }} · {{ $feedback->created_at->diffForHumans() }}</small>
                </div>
                <span class="feedback-status is-{{ $feedback->status }}">{{ ucfirst($feedback->status) }}</span>
            </header>
            <p>{{ $feedback->message }}</p>
            @if($feedback->reply)
                <div class="feedback-reply">
                    <strong>Admin{{ $feedback->admin?->name ? ' · '.$feedback->admin->name : '' }}</strong>
                    @if($feedback->replied_at)<small style="display:block;margin:2px 0 8px;color:#607772">{{ $feedback->replied_at->diffForHumans() }}</small>@endif
                    <p>{{ $feedback->reply }}</p>
                </div>
            @endif
            @if(in_array($feedback->status, ['pending', 'replied'], true))
                <form method="POST" action="/admin/feedback/reply/{{ $feedback->id }}" class="feedback-reply-form">
                    @csrf
                    <label>{{ $feedback->reply ? 'Update reply' : 'Reply' }}<textarea name="reply" rows="3" placeholder="Write your reply..." required>{{ $feedback->reply }}</textarea></label>
                    <button type="submit">{{ $feedback->reply ? 'Update reply' : 'Send reply' }}</button>
                </form>
            @endif
            <div class="feedback-actions">
                @if($feedback->status !== 'resolved')
                    <a href="/admin/feedback/resolve/{{ $feedback->id }}">Mark resolved</a>
                @endif
                <details class="feedback-edit">
                    <summary>Edit</summary>
                    <form method="POST" action="/admin/feedback/update/{{ $feedback->id }}">
                        @csrf
                        <label>Subject<input name="subject" value="{{ $feedback->subject }}" required></label>
                        <label>Message<textarea name="message" rows="3" required>{{ $feedback->message }}</textarea></label>
                        <button type="submit">Save changes</button>
                    </form>
                </details>
                <a href="/admin/feedback/delete/{{ $feedback->id }}" class="is-danger" onclick="return confirm('Delete feedback?')">Delete</a>
            </div>
        </article>
    @empty
        <p class="feedback-empty">No feedback yet.</p>
    @endforelse
</main>
</body>
</html>
