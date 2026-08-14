<!DOCTYPE html>

<html>
<head>

<title>Reservation Calendar</title>

<link rel="stylesheet"
href="{{ asset('css/style.css') }}">

</head>

<body class="dashboard-body">

<div class="back-btn">
    <button onclick="history.back()">
        ← 
    </button>
</div>

<div class="calendar-container">

<h1>

📅 Reservation Calendar

</h1>

<div id="calendar"></div>

</div>

<link rel="stylesheet"
href="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.11/index.global.min.css">

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

                events:
[

    {{-- ================= SPOT BOOKINGS ================= --}}

    @foreach($bookings as $booking)

    {

        title:
        '📍 {{ $booking->spot->name }}',

        start:
        '{{ $booking->booking_date }}',

        color:

        @if($booking->status=='approved')

        '#22c55e'

        @elseif($booking->status=='pending')

        '#f59e0b'

        @else

        '#ef4444'

        @endif,

        extendedProps:
        {
            type:'Spot',

            user:'{{ $booking->user->name }}',

            place:'{{ $booking->spot->name }}',

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
        '🏨 {{ $booking->hotel->name }}',

        start:
        '{{ $booking->check_in }}',

        end:
        '{{ $booking->check_out }}',

        color:

        @if($booking->status=='approved')

        '#3b82f6'

        @elseif($booking->status=='pending')

        '#f59e0b'

        @else

        '#ef4444'

        @endif,

        extendedProps:
        {
            type:'Hotel',

            user:'{{ $booking->user->name }}',

            place:'{{ $booking->hotel->name }}',

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
        '🍽 {{ $booking->food->name }}',

        start:
        '{{ $booking->reservation_date }}',

        color:

        @if($booking->status=='approved')

        '#ec4899'

        @elseif($booking->status=='pending')

        '#f59e0b'

        @else

        '#ef4444'

        @endif,

        extendedProps:
        {
            type:'Restaurant',

            user:'{{ $booking->user->name }}',

            place:'{{ $booking->food->name }}',

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

        '\nTotal: ₱' +
        info.event.extendedProps.total;
    }

    if(info.event.extendedProps.type == 'Hotel')
    {
        details +=

        '\nRooms: ' +
        info.event.extendedProps.rooms +

        '\nTotal: ₱' +
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
});

</script>

</body>
</html>