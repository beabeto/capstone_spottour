<!doctype html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Traveller chat | SpotTour</title>
<link rel="stylesheet" href="{{ asset('css/style.css') }}">
<link rel="stylesheet" href="{{ asset('css/traveller-chat.css') }}?v={{ filemtime(public_path('css/traveller-chat.css')) }}">
</head>
<body class="dashboard-body chat-inbox-page">
@php
    $unreadTotal = $threads->sum('staff_unread');
    $openTotal = $threads->count();
@endphp
<div class="back-btn"><button type="button" onclick="window.history.back(); return false;">Back</button></div>
<main class="chat-shell">
    <header class="chat-page-head">
        <span>{{ $workspace === 'admin' ? 'Admin workspace' : 'Owner workspace' }}</span>
        <h1>Traveller chat</h1>
        <p>Reply when a traveller asks to be referred. They get a web notification and email.</p>
    </header>

    @if(session('success'))
        <div class="chat-toast">{{ session('success') }}</div>
    @endif

    <div class="chat-summary">
        <span>{{ $openTotal }} conversation{{ $openTotal === 1 ? '' : 's' }}</span>
        <span>{{ $unreadTotal }} unread</span>
        @if($active)
            <span>Open: {{ $active->traveller->name ?? 'Traveller' }}</span>
        @endif
    </div>

    <div class="chat-inbox-grid">
        <aside class="chat-thread-list">
            <div class="chat-list-toolbar">
                <label>
                    Search conversations
                    <input type="search" id="threadSearch" placeholder="Traveller or listing">
                </label>
            </div>
            <div class="chat-thread-scroll" id="threadList">
                @forelse($threads as $thread)
                    @php
                        $preview = optional($thread->latestMessage)->body ?? 'No messages yet';
                        $search = strtolower(trim(
                            ($thread->traveller->name ?? '').' '.
                            ($thread->traveller->email ?? '').' '.
                            ($thread->listing_name ?? '').' '.
                            $preview
                        ));
                    @endphp
                    <a href="/{{ $workspace }}/chats/{{ $thread->id }}"
                       class="chat-thread-item {{ optional($active)->id === $thread->id ? 'is-current' : '' }}"
                       data-search="{{ $search }}">
                        <div class="chat-thread-top">
                            <strong>{{ $thread->traveller->name ?? 'Traveller' }}</strong>
                            @if($thread->staff_unread > 0)
                                <b>{{ $thread->staff_unread }}</b>
                            @endif
                        </div>
                        <span class="chat-thread-meta">
                            {{ $thread->listing_name ? 'About '.$thread->listing_name : 'General help' }}
                            @if($thread->last_message_at)
                                · {{ $thread->last_message_at->diffForHumans() }}
                            @endif
                        </span>
                        <span class="chat-thread-preview">{{ \Illuminate\Support\Str::limit($preview, 90) }}</span>
                    </a>
                @empty
                    <p class="chat-empty-list">No traveller has asked to be referred yet.</p>
                @endforelse
                <p class="chat-empty-list" id="threadFilterEmpty" hidden>No conversations match this search.</p>
            </div>
        </aside>

        <section class="chat-thread">
            @if($active)
                <header class="chat-thread-head">
                    <div>
                        <strong>{{ $active->traveller->name ?? 'Traveller' }}</strong>
                        <small>
                            {{ $active->traveller->email ?: 'No email' }}
                            · {{ $active->listing_name ? 'About '.$active->listing_name : 'General help' }}
                        </small>
                    </div>
                </header>
                <div class="chat-log is-staff">
                    @foreach($active->messages as $message)
                        @php
                            $label = match ($message->sender_role) {
                                'traveller' => $active->traveller->name ?? 'Traveller',
                                'bot' => 'SpotTour helper',
                                'admin' => 'Admin'.($message->sender?->name ? ' · '.$message->sender->name : ''),
                                'owner' => 'Owner'.($message->sender?->name ? ' · '.$message->sender->name : ''),
                                default => ucfirst((string) $message->sender_role),
                            };
                        @endphp
                        <article class="chat-bubble is-{{ $message->sender_role }} {{ (int) $message->sender_id === (int) Auth::id() ? 'is-mine' : '' }}">
                            <small>{{ $label }}</small>
                            <p>{{ $message->body }}</p>
                        </article>
                    @endforeach
                </div>
                <form method="POST" action="/{{ $workspace }}/chats/{{ $active->id }}" class="chat-form">
                    @csrf
                    <input type="text" name="message" maxlength="1000" placeholder="Write a reply to the traveller" required autocomplete="off">
                    <button type="submit">Send</button>
                </form>
            @else
                <div class="chat-empty-panel">
                    <strong>Choose a conversation</strong>
                    <p>Pick a traveller on the left to read the thread and send a reply.</p>
                </div>
            @endif
        </section>
    </div>
</main>
<script>
(function () {
    var search = document.getElementById('threadSearch');
    var empty = document.getElementById('threadFilterEmpty');
    var items = Array.prototype.slice.call(document.querySelectorAll('.chat-thread-item'));
    var log = document.querySelector('.chat-log.is-staff');

    if (log) log.scrollTop = log.scrollHeight;

    function applyFilters() {
        var query = (search && search.value ? search.value : '').trim().toLowerCase();
        var visible = 0;
        items.forEach(function (item) {
            var hay = item.getAttribute('data-search') || '';
            var show = !query || hay.indexOf(query) !== -1;
            item.hidden = !show;
            if (show) visible += 1;
        });
        if (empty) empty.hidden = visible > 0 || items.length === 0;
    }

    if (search) search.addEventListener('input', applyFilters);
})();
</script>
</body>
</html>
