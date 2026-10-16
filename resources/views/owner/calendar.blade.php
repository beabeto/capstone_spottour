<!DOCTYPE html>

<html>
<head>

<title>Reservation Calendar</title>

<link rel="stylesheet"
href="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.11/index.global.min.css">

<link rel="stylesheet"
href="{{ asset('css/style.css') }}">

</head>

<body class="{{ !empty($isAdmin) ? 'admin-body' : 'owner-body' }} reservation-calendar-page">

<div class="back-btn">
    <button type="button" onclick="window.history.back(); return false;">Back</button>
</div>

<div class="calendar-container">

<header class="calendar-page-heading">
    <div>
        <span>{{ !empty($isAdmin) ? 'Administrator workspace' : 'Reservation workspace' }}</span>
        <h1>{{ !empty($isAdmin) ? 'All Reservations Calendar' : 'Reservation Calendar' }}</h1>
        <p>Spot, hotel, and restaurant reservations each have their own color. A solid block is confirmed, a dashed block is pending, and a faded block is cancelled or rejected.</p>
    </div>
    <div class="calendar-filter">
        <select id="calendarPeriod" aria-label="Calendar period">
            <option value="today">Today</option>
            <option value="week">This week</option>
            <option value="month" selected>This month</option>
            <option value="list">Upcoming list</option>
        </select>
    </div>
</header>

@php
    $pendingStatuses = ['pending', 'pending_payment', 'pending_verification'];
    $calendarStatus = function ($status) use ($pendingStatuses) {
        if (in_array($status, $pendingStatuses, true)) return 'pending';
        return $status === 'approved' ? 'approved' : 'cancelled';
    };
@endphp
<div class="calendar-summary" aria-label="Reservation type colors">
    <div><span class="calendar-dot spot"></span><strong>Tourist spot</strong><small>{{ $bookings->count() }} reservations</small></div>
    <div><span class="calendar-dot hotel"></span><strong>Hotel</strong><small>{{ $hotelBookings->count() }} reservations</small></div>
    <div><span class="calendar-dot restaurant"></span><strong>Restaurant</strong><small>{{ $foodBookings->count() }} reservations</small></div>
</div>
<div class="calendar-status-key" aria-label="Reservation status styles">
    <span class="cal-chip approved">Confirmed · {{ $bookings->where('status','approved')->count() + $hotelBookings->where('status','approved')->count() + $foodBookings->where('status','approved')->count() }}</span>
    <span class="cal-chip pending">Pending · {{ $bookings->whereIn('status',$pendingStatuses)->count() + $hotelBookings->whereIn('status',$pendingStatuses)->count() + $foodBookings->whereIn('status',$pendingStatuses)->count() }}</span>
    <span class="cal-chip cancelled">Cancelled or rejected · {{ $bookings->whereIn('status',['cancelled','rejected'])->count() + $hotelBookings->whereIn('status',['cancelled','rejected'])->count() + $foodBookings->whereIn('status',['cancelled','rejected'])->count() }}</span>
</div>

<div class="calendar-navigation" id="calendarNavigation"><button type="button" data-calendar-nav="prev">Previous</button><strong id="calendarTitle"></strong><button type="button" data-calendar-nav="today">Today</button><button type="button" data-calendar-nav="next">Next</button></div>

<div id="calendar"></div>

</div>

<script src="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.11/index.global.min.js"></script>

<script>

document.addEventListener(
'DOMContentLoaded',
function()
{
    let calendarEl =
        document.getElementById(
            'calendar'
        );

    let calendar =
        new FullCalendar.Calendar(
            calendarEl,
            {

                initialView:
                    'dayGridMonth',

                height:'auto',
                dayMaxEvents: 2,
                eventMaxStack: 2,
                moreLinkText: 'more reservations',
                headerToolbar: false,
                buttonText: { today: 'Today', month: 'Month', week: 'Week', list: 'List' },

                events:
[

    {{-- ================= SPOT BOOKINGS ================= --}}

    @foreach($bookings as $booking)

    {

        title:
        @json(optional($booking->spot)->name ?? 'Tourist Spot'),

        start:
        '{{ $booking->booking_date }}',

        classNames:['cal-spot','cal-{{ $calendarStatus($booking->status) }}'],

        extendedProps:
        {
            type:'Spot',

            user:@json(optional($booking->user)->name ?? 'Guest'),

            place:@json(optional($booking->spot)->name ?? 'Tourist Spot'),

            quantity:'{{ $booking->quantity }}',

            total:'{{ number_format($booking->total_price,2) }}',

            status:'{{ ucfirst($booking->status) }}'
        }

    },

    @endforeach



    {{-- ================= HOTEL BOOKINGS ================= --}}

    @foreach($hotelBookings as $booking)

    {

        title:
        @json(optional($booking->hotel)->name ?? 'Hotel'),

        start:
        '{{ $booking->check_in }}',

        end:
        '{{ $booking->check_out }}',

        classNames:['cal-hotel','cal-{{ $calendarStatus($booking->status) }}'],

        extendedProps:
        {
            type:'Hotel',

            user:@json(optional($booking->user)->name ?? 'Guest'),

            place:@json(optional($booking->hotel)->name ?? 'Hotel'),

            rooms:'{{ $booking->rooms }}',

            total:'{{ number_format($booking->total_price,2) }}',

            status:'{{ ucfirst($booking->status) }}'
        }

    },

    @endforeach



    {{-- ================= RESTAURANT BOOKINGS ================= --}}

    @foreach($foodBookings as $booking)

    {

        title:
        @json(optional($booking->food)->name ?? 'Restaurant'),

        start:
        '{{ $booking->reservation_date }}',

        classNames:['cal-restaurant','cal-{{ $calendarStatus($booking->status) }}'],

        extendedProps:
        {
            type:'Restaurant',

            user:@json(optional($booking->user)->name ?? 'Guest'),

            place:@json(optional($booking->food)->name ?? 'Restaurant'),

            guests:'{{ $booking->guests }}',

            time:'{{ $booking->reservation_time }}',

            status:'{{ ucfirst($booking->status) }}'
        }

    },

    @endforeach

],

                eventClick:function(info)
{
    let details =

        'Type: ' + info.event.extendedProps.type +

        '\nName: ' + info.event.extendedProps.place +

        '\nUser: ' + info.event.extendedProps.user +

        '\nStatus: ' + info.event.extendedProps.status;

    if(info.event.extendedProps.type == 'Spot')
    {
        details +=

        '\nQuantity: ' +
        info.event.extendedProps.quantity +

        '\nTotal: PHP ' +
        info.event.extendedProps.total;
    }

    if(info.event.extendedProps.type == 'Hotel')
    {
        details +=

        '\nRooms: ' +
        info.event.extendedProps.rooms +

        '\nTotal: PHP ' +
        info.event.extendedProps.total;
    }

    if(info.event.extendedProps.type == 'Restaurant')
    {
        details +=

        '\nGuests: ' +
        info.event.extendedProps.guests +

        '\nTime: ' +
        info.event.extendedProps.time;
    }

    alert(details);
}

            }
        );

    calendar.render();

    const calendarHeading = document.getElementById('calendarNavigation');
    const updateCalendarTitle = () => document.getElementById('calendarTitle').textContent = calendar.view.title;
    updateCalendarTitle();
    calendar.on('datesSet', updateCalendarTitle);
    calendarHeading.addEventListener('click', (event) => {
        const action = event.target.dataset.calendarNav;
        if (!action) return;
        calendar[action]();
    });

    document.getElementById('calendarPeriod').addEventListener('change', function () {
        if (this.value === 'today') { calendar.today(); calendar.changeView('timeGridDay'); }
        if (this.value === 'week') { calendar.changeView('timeGridWeek'); }
        if (this.value === 'month') { calendar.changeView('dayGridMonth'); }
        if (this.value === 'list') { calendar.changeView('listWeek'); }
    });
});

</script>

</body>
</html>
