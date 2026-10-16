@if(Auth::check() && Auth::user()?->role === 'user')
<link rel="stylesheet" href="{{ asset('css/traveller-chat.css') }}?v={{ filemtime(public_path('css/traveller-chat.css')) }}">
<div id="traveller-chat" data-csrf="{{ csrf_token() }}" data-open="{{ request()->boolean('chat') ? '1' : '0' }}">
    <button type="button" class="chat-launcher" aria-label="Help" aria-expanded="false" aria-controls="travellerChatPanel">
        <svg viewBox="0 0 24 24" aria-hidden="true"><path fill="currentColor" d="M5 4h14a2 2 0 0 1 2 2v9a2 2 0 0 1-2 2H9l-4 3v-3H5a2 2 0 0 1-2-2V6a2 2 0 0 1 2-2z"/></svg>
        <b class="chat-badge" hidden>0</b>
    </button>
    <section id="travellerChatPanel" class="chat-panel" hidden>
        <header>
            <div>
                <strong>SpotTour help</strong>
                <small class="chat-status">Tap a suggestion or type your own question.</small>
            </div>
            <button type="button" class="chat-close" aria-label="Close chat">×</button>
        </header>
        <div class="chat-log" role="log"></div>
        <div class="chat-suggestions-wrap">
            <button type="button" class="chat-suggestions-toggle" aria-expanded="true">Hide suggestions</button>
            <div class="chat-suggestions" aria-label="Suggested questions"></div>
        </div>
        <form class="chat-form">
            <input type="text" name="message" maxlength="1000" placeholder="Or type your own question" autocomplete="off" required>
            <button type="submit">Send</button>
        </form>
    </section>
</div>
<script src="{{ asset('js/traveller-chat.js') }}?v={{ filemtime(public_path('js/traveller-chat.js')) }}"></script>
@endif
