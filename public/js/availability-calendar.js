(function () {
    var weekdays = ['Su', 'Mo', 'Tu', 'We', 'Th', 'Fr', 'Sa'];

    function boot() {
        document.querySelectorAll('.availability-date').forEach(attach);
    }

    function attach(input) {
        if (input.dataset.calendarReady || !input.dataset.listingId) return;
        input.dataset.calendarReady = '1';
        var wrap = document.createElement('div');
        wrap.className = 'open-calendar';
        var note = document.createElement('p');
        note.className = 'availability-note';
        note.textContent = 'Loading open dates…';
        wrap.appendChild(note);
        (input.closest('label') || input).after(wrap);

        fetch('/booking/availability?type=' + encodeURIComponent(input.dataset.listingType) + '&id=' + encodeURIComponent(input.dataset.listingId))
            .then(function (response) { return response.json(); })
            .then(function (data) { render(input, wrap, note, data); })
            .catch(function () { note.textContent = 'Availability will be confirmed when you submit the booking.'; });
    }

    function render(input, wrap, note, data) {
        var booked = {};
        (data.dates || []).forEach(function (row) { booked[row.date] = row; });
        var cursor = new Date();
        cursor.setDate(1);
        var selected = input.value;

        function paint() {
            wrap.innerHTML = '';
            var head = document.createElement('div');
            head.className = 'open-calendar-head';
            var prev = button('‹', function () { cursor.setMonth(cursor.getMonth() - 1); paint(); });
            var title = document.createElement('strong');
            title.textContent = cursor.toLocaleString('en-US', { month: 'long', year: 'numeric' });
            var next = button('›', function () { cursor.setMonth(cursor.getMonth() + 1); paint(); });
            head.append(prev, title, next);
            wrap.appendChild(head);

            var grid = document.createElement('div');
            grid.className = 'open-days';
            weekdays.forEach(function (name) {
                var label = document.createElement('span');
                label.textContent = name;
                grid.appendChild(label);
            });

            var first = new Date(cursor.getFullYear(), cursor.getMonth(), 1);
            var startPad = first.getDay();
            for (var blank = 0; blank < startPad; blank += 1) grid.appendChild(document.createElement('span'));

            var days = new Date(cursor.getFullYear(), cursor.getMonth() + 1, 0).getDate();
            var today = new Date();
            today.setHours(0, 0, 0, 0);
            for (var day = 1; day <= days; day += 1) {
                var date = new Date(cursor.getFullYear(), cursor.getMonth(), day);
                var key = iso(date);
                var info = booked[key] || { booked: 0, full: false };
                var past = date < today;
                var cell = button(String(day), function (picked) {
                    return function () {
                        input.value = picked;
                        input.setCustomValidity('');
                        selected = picked;
                        paint();
                    };
                }(key));
                cell.classList.add('open-day');
                if (past) cell.disabled = true;
                if (info.full) {
                    cell.disabled = true;
                    cell.classList.add('is-full');
                    cell.title = 'Fully booked';
                } else if (info.booked > 0) {
                    cell.classList.add('is-busy');
                    cell.title = info.booked + ' booked';
                }
                if (selected === key) cell.classList.add('is-selected');
                grid.appendChild(cell);
            }
            wrap.appendChild(grid);
            wrap.appendChild(note);
            note.textContent = summary(data.limit, selected, booked[selected]);
            note.dataset.state = booked[selected] && booked[selected].full ? 'unavailable' : '';
        }

        input.addEventListener('change', function () {
            selected = input.value;
            if (booked[selected] && booked[selected].full) {
                input.setCustomValidity('That day is fully booked.');
            } else {
                input.setCustomValidity('');
            }
            paint();
        });
        paint();
    }

    function summary(limit, selected, info) {
        if (!limit) return 'This place has no daily booking limit. Every open date can still be reserved.';
        if (!selected) return 'Pick a date. This place accepts ' + limit + ' booking' + (limit === 1 ? '' : 's') + ' per day.';
        if (info && info.full) return 'That day is fully booked.';
        var taken = info ? info.booked : 0;
        return taken + ' of ' + limit + ' bookings taken. This day is still open.';
    }

    function button(text, onClick) {
        var el = document.createElement('button');
        el.type = 'button';
        el.textContent = text;
        el.addEventListener('click', onClick);
        return el;
    }

    function iso(date) {
        var month = String(date.getMonth() + 1).padStart(2, '0');
        var day = String(date.getDate()).padStart(2, '0');
        return date.getFullYear() + '-' + month + '-' + day;
    }

    if (document.readyState === 'loading') document.addEventListener('DOMContentLoaded', boot);
    else boot();
})();
