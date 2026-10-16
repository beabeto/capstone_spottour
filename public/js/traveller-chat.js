(function () {
    var root = document.getElementById('traveller-chat');
    if (!root || root.dataset.bound === '1') return;
    root.dataset.bound = '1';

    var launcher = root.querySelector('.chat-launcher');
    var panel = root.querySelector('.chat-panel');
    var log = root.querySelector('.chat-log');
    var suggestions = root.querySelector('.chat-suggestions');
    var suggestionsToggle = root.querySelector('.chat-suggestions-toggle');
    var suggestionsWrap = root.querySelector('.chat-suggestions-wrap');
    var form = root.querySelector('.chat-form');
    var input = form && form.querySelector('input[name="message"]');
    var badge = root.querySelector('.chat-badge');
    var status = root.querySelector('.chat-status');
    var token = root.dataset.csrf;
    var open = false;
    var sending = false;
    var suggestionsVisible = true;
    try {
        suggestionsVisible = localStorage.getItem('spottour-chat-suggestions') !== '0';
    } catch (error) {}
    var defaultSuggestions = [
        'How do I book a hotel?',
        'How do I book a restaurant?',
        'How do I pay with GCash?',
        'Show my bookings',
        'Talk to an admin',
        'Talk to an owner'
    ];

    function setBadge(count) {
        if (!badge) return;
        badge.hidden = count < 1;
        badge.textContent = count > 9 ? '9+' : String(count);
    }

    function applySuggestionsVisibility() {
        if (!suggestions || !suggestionsToggle) return;
        suggestions.hidden = !suggestionsVisible;
        if (suggestionsVisible) suggestions.removeAttribute('hidden');
        else suggestions.setAttribute('hidden', 'hidden');
        if (suggestionsWrap) suggestionsWrap.classList.toggle('is-collapsed', !suggestionsVisible);
        suggestionsToggle.setAttribute('aria-expanded', suggestionsVisible ? 'true' : 'false');
        suggestionsToggle.textContent = suggestionsVisible ? 'Hide suggestions' : 'Show suggestions';
    }

    function paintSuggestions(items) {
        if (!suggestions) return;
        var list = (items && items.length) ? items : defaultSuggestions;
        suggestions.innerHTML = '';
        list.forEach(function (text) {
            var button = document.createElement('button');
            button.type = 'button';
            button.className = 'chat-suggestion';
            button.setAttribute('data-question', text);
            button.textContent = text;
            suggestions.appendChild(button);
        });
        applySuggestionsVisibility();
    }

    function paint(data) {
        if (!log) return;
        log.innerHTML = '';
        (data.messages || []).forEach(function (message) {
            var item = document.createElement('article');
            item.className = 'chat-bubble is-' + message.role;
            item.innerHTML = '<small></small><p></p>';
            item.querySelector('small').textContent = message.label || (
                message.role === 'traveller' ? 'You'
                    : (message.role === 'bot' ? 'SpotTour helper'
                        : (message.role === 'admin' ? 'Admin'
                            : (message.role === 'owner' ? 'Owner' : message.role)))
            );
            item.querySelector('p').textContent = message.body;
            log.appendChild(item);
        });
        log.scrollTop = log.scrollHeight;
        setBadge(open ? 0 : (data.unread || 0));
        paintSuggestions(data.suggestions || defaultSuggestions);
        if (!status) return;
        if (data.status === 'with_staff') {
            status.textContent = data.assignee === 'owner'
                ? 'Waiting for the owner' + (data.listing ? ' of ' + data.listing : '') + '. Replies are marked Owner.'
                : 'Waiting for an admin. Replies are marked Admin.';
        } else {
            status.textContent = 'Tap a suggestion or type your own question.';
        }
    }

    function load(markOpen) {
        return fetch('/chat' + (markOpen ? '?open=1' : ''), {
            headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' },
            credentials: 'same-origin'
        }).then(function (response) {
            if (!response.ok) throw new Error('chat');
            return response.json();
        }).then(paint);
    }

    function sendMessage(message) {
        message = String(message || '').trim();
        if (!message || sending || !input) return false;
        sending = true;
        input.value = '';
        fetch('/chat', {
            method: 'POST',
            headers: {
                'Accept': 'application/json',
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': token,
                'X-Requested-With': 'XMLHttpRequest'
            },
            credentials: 'same-origin',
            body: JSON.stringify({ message: message })
        }).then(function (response) {
            if (!response.ok) throw new Error('send');
            return response.json();
        }).then(paint).catch(function () {
            input.value = message;
        }).finally(function () {
            sending = false;
            if (open) input.focus();
        });
        return true;
    }

    function toggle(next) {
        open = !!next;
        if (panel) panel.hidden = !open;
        root.classList.toggle('is-open', open);
        if (launcher) launcher.setAttribute('aria-expanded', open ? 'true' : 'false');
        if (open) {
            load(true).then(function () { if (input) input.focus(); }).catch(function () {
                paintSuggestions(defaultSuggestions);
            });
        }
    }

    if (launcher) {
        launcher.addEventListener('click', function (event) {
            event.preventDefault();
            toggle(!open);
        });
    }

    var closer = root.querySelector('.chat-close');
    if (closer) {
        closer.addEventListener('click', function (event) {
            event.preventDefault();
            toggle(false);
        });
    }

    if (suggestionsToggle) {
        suggestionsToggle.addEventListener('click', function (event) {
            event.preventDefault();
            event.stopPropagation();
            suggestionsVisible = !suggestionsVisible;
            try {
                localStorage.setItem('spottour-chat-suggestions', suggestionsVisible ? '1' : '0');
            } catch (error) {}
            applySuggestionsVisibility();
        });
    }

    if (suggestions) {
        suggestions.addEventListener('click', function (event) {
            var button = event.target.closest('.chat-suggestion');
            if (!button || !suggestions.contains(button)) return;
            event.preventDefault();
            event.stopPropagation();
            var question = button.getAttribute('data-question') || button.textContent || '';
            if (!open) toggle(true);
            sendMessage(question);
        });
    }

    if (form) {
        form.addEventListener('submit', function (event) {
            event.preventDefault();
            sendMessage(input ? input.value : '');
        });
    }

    paintSuggestions(defaultSuggestions);

    if (root.dataset.open === '1') toggle(true);
    else load(false).catch(function () { paintSuggestions(defaultSuggestions); });

    setInterval(function () {
        if (document.hidden) return;
        load(open).catch(function () {});
    }, 15000);
})();
