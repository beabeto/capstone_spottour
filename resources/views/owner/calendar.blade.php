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

                    @foreach($bookings as $booking)

                    {

                        title:
                        '{{ $booking->spot->name }} - {{ $booking->user->name }}',

                        start:
                        '{{ $booking->booking_date }}',

                        color:

                        @if($booking->status == 'approved')

                        '#22c55e'

                        @elseif($booking->status == 'pending')

                        '#f59e0b'

                        @else

                        '#ef4444'

                        @endif,

                        extendedProps:
                        {
                            user:
                            '{{ $booking->user->name }}',

                            spot:
                            '{{ $booking->spot->name }}',

                            qty:
                            '{{ $booking->quantity }}',

                            total:
                            '{{ number_format($booking->total_price,2) }}',

                            status:
                            '{{ ucfirst($booking->status) }}'
                        }

                    },

                    @endforeach

                ],

                eventClick:function(info)
                {
                    alert(

                        'Spot: '
                        + info.event.extendedProps.spot +

                        '\nUser: '
                        + info.event.extendedProps.user +

                        '\nQuantity: '
                        + info.event.extendedProps.qty +

                        '\nTotal: ₱'
                        + info.event.extendedProps.total +

                        '\nStatus: '
                        + info.event.extendedProps.status

                    );
                }

            }
        );

    calendar.render();
});

</script>

</body>
</html>