<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Owner Bookings</title>
    <link rel="stylesheet" href="{{ asset('css/style.css') }}">
    <style>
        .owner-bookings-page{padding-bottom:48px}
        .owner-bookings-page .notifications-container{width:min(980px,calc(100% - 40px))!important;padding:78px 0 56px!important}
        .owner-bookings-page .notifications-container > h1{margin-top:0!important}
        .booking-toolbar{display:grid;grid-template-columns:1.2fr .9fr .9fr;gap:12px;margin:0 0 18px;padding:14px;border:1px solid #d7e8e3;border-radius:14px;background:#fff}
        .booking-toolbar label{display:grid;gap:6px;color:#48635c;font-size:12px;font-weight:800}
        .booking-toolbar select,.booking-toolbar input{width:100%;min-height:42px;padding:0 12px;border:1px solid #d5ebe4;border-radius:10px;background:#fff;color:#203531;font:inherit}
        .booking-summary{display:flex;flex-wrap:wrap;gap:8px;margin:0 0 16px}
        .booking-summary span{display:inline-flex;align-items:center;min-height:30px;padding:0 10px;border-radius:999px;background:#e8f5f1;color:#126a59;font-size:12px;font-weight:800}
        .booking-accordion{margin:0 0 12px;border:1px solid #d7e8e3;border-radius:14px;background:#fff;overflow:hidden}
        .booking-accordion[hidden]{display:none!important}
        .booking-accordion > summary{display:flex;align-items:center;justify-content:space-between;gap:12px;padding:14px 16px;cursor:pointer;list-style:none;background:#f6fbf9}
        .booking-accordion > summary::-webkit-details-marker{display:none}
        .booking-accordion > summary h2{margin:0;color:#17473f;font-size:16px}
        .booking-accordion > summary small{color:#607772;font-size:12px;font-weight:700}
        .booking-accordion > summary .count{display:inline-flex;align-items:center;justify-content:center;min-width:28px;min-height:28px;padding:0 8px;border-radius:999px;background:#167b69;color:#fff;font-size:12px;font-weight:800}
        .booking-accordion-body{padding:12px 14px 14px;border-top:1px solid #e7f0ed}
        .booking-card[hidden],.booking-empty[hidden]{display:none!important}
        .booking-empty{padding:18px;color:#607772;text-align:center}
        .owner-bookings-page .arrival-checkin{display:grid!important;grid-template-columns:minmax(0,1fr) auto;gap:12px;align-items:end;margin:0 0 16px;padding:14px;border:1px solid #d7e8e3;border-radius:14px;background:#fff}
        .owner-bookings-page .arrival-checkin label{display:grid!important;gap:6px;margin:0;color:#48635c;font-size:12px;font-weight:800}
        .owner-bookings-page .arrival-checkin input{width:100%;box-sizing:border-box;min-height:42px;margin:0;padding:0 12px;border:1px solid #d5ebe4;border-radius:10px;font:inherit}
        .owner-bookings-page .arrival-checkin-action{display:grid;gap:6px;align-content:end}
        .owner-bookings-page .arrival-checkin-action > span{display:block;min-height:1.2em;visibility:hidden;font-size:12px;font-weight:800;line-height:1.2}
        .owner-bookings-page .arrival-checkin button{width:auto!important;min-width:110px;min-height:42px!important;height:42px!important;margin:0!important;padding:0 16px!important;border:0!important;border-radius:10px!important;background:#167b69!important;color:#fff!important;font-weight:800!important;line-height:1!important;transform:none!important;cursor:pointer}
        @media(max-width:760px){
            .booking-toolbar{grid-template-columns:1fr}
            .owner-bookings-page .notifications-container{width:min(100% - 24px,980px)!important;padding-top:70px!important}
            .owner-bookings-page .arrival-checkin{grid-template-columns:1fr}
            .owner-bookings-page .arrival-checkin-action > span{display:none}
            .owner-bookings-page .arrival-checkin button{width:100%!important}
        }
    </style>
</head>
<body class="dashboard-body owner-bookings-page">
@php
    $pendingStatuses = ['pending', 'pending_payment', 'pending_verification'];
    $spotAction = $spotBookings->whereIn('status', $pendingStatuses)->count();
    $hotelAction = $hotelBookings->whereIn('status', $pendingStatuses)->count();
    $foodAction = $foodBookings->whereIn('status', $pendingStatuses)->count();
    $totalAction = $spotAction + $hotelAction + $foodAction;
@endphp
<div class="back-btn"><button type="button" onclick="window.history.back(); return false;">Back</button></div>
<div class="notifications-container">
    <h1>Booking Requests</h1>
    <p style="margin:0 0 16px;color:#607772">Filter by type and status, or open only the section you need.</p>

    @if(session('success'))<div class="toast-success">{{ session('success') }}</div>@endif
    @if(session('error'))<div class="toast-error">{{ session('error') }}</div>@endif

    <form method="POST" action="/owner/arrivals" class="arrival-checkin">
        @csrf
        <label>Guest arrival code<input type="text" name="arrival_code" placeholder="ST-ABC123" required></label>
        <div class="arrival-checkin-action">
            <span>Check in</span>
            <button type="submit">Check in</button>
        </div>
    </form>

    <div class="booking-toolbar" id="bookingFilters">
        <label>Search guest or place
            <input type="search" id="bookingSearch" placeholder="Name, spot, hotel, restaurant">
        </label>
        <label>Type
            <select id="bookingType">
                <option value="all">All types</option>
                <option value="spot">Spot bookings</option>
                <option value="hotel">Hotel bookings</option>
                <option value="food">Restaurant bookings</option>
            </select>
        </label>
        <label>Status
            <select id="bookingStatus">
                <option value="action">Needs action</option>
                <option value="approved">Approved</option>
                <option value="closed">Rejected / cancelled</option>
                <option value="all">All statuses</option>
            </select>
        </label>
    </div>

    <div class="booking-summary">
        <span>{{ $totalAction }} need action</span>
        <span>{{ $spotBookings->count() }} spots</span>
        <span>{{ $hotelBookings->count() }} hotels</span>
        <span>{{ $foodBookings->count() }} restaurants</span>
    </div>

    {{-- SPOT --}}
    <details class="booking-accordion" data-section="spot" {{ $spotAction > 0 ? 'open' : '' }}>
        <summary>
            <div>
                <h2>Spot bookings</h2>
                <small>{{ $spotAction }} waiting · {{ $spotBookings->count() }} total</small>
            </div>
            <span class="count">{{ $spotBookings->count() }}</span>
        </summary>
        <div class="booking-accordion-body">
            @forelse($spotBookings as $booking)
                @php
                    $group = in_array($booking->status, $pendingStatuses, true) ? 'action' : (in_array($booking->status, ['approved', 'completed'], true) ? 'approved' : 'closed');
                    $place = $booking->spot?->name ?? 'Removed tourist spot';
                @endphp
                <div class="booking-card"
                     data-type="spot"
                     data-status="{{ $group }}"
                     data-search="{{ strtolower(($booking->user->name ?? '').' '.$place) }}">
                    <div class="booking-top">
                        <div class="booking-user">
                            <img src="{{ $booking->user->profile_image ? asset('uploads/profile/'.$booking->user->profile_image) : 'https://cdn-icons-png.flaticon.com/512/3135/3135715.png' }}" class="booking-avatar" alt="">
                            <div>
                                <h3>{{ $booking->user->name }}</h3>
                                <small>Spot booking</small>
                            </div>
                        </div>
                        <div>
                            @if($group === 'action')
                                <span class="status {{ $booking->receipt ? 'approved' : 'pending' }}">{{ $booking->receipt ? 'Paid — review receipt' : 'Awaiting payment' }}</span>
                            @elseif($group === 'approved')
                                <span class="status approved">Approved</span>
                            @elseif($booking->status === 'cancelled')
                                <span class="status cancelled">Cancelled</span>
                            @else
                                <span class="status rejected">Rejected</span>
                            @endif
                        </div>
                    </div>
                    <div class="booking-details">
                        <p><strong>{{ $place }}</strong></p>
                        <p>Booking date: <strong>{{ \Carbon\Carbon::parse($booking->booking_date)->format('F d, Y') }}</strong></p>
                        <p>Quantity: <strong>{{ $booking->quantity }}</strong></p>
                        @if($booking->needs_guide)<p>Tourist guide: <strong>Requested</strong></p>@endif
                        @if($booking->package_name)<p>Package: <strong>{{ $booking->package_name }}</strong></p>@endif
                        <p>Total: <strong>PHP {{ number_format($booking->total_price, 2) }}</strong></p>
                        @include('owner.partials.payment-proof', ['booking' => $booking])
                        @include('owner.partials.arrival', ['booking' => $booking])
                    </div>
                    @if($group === 'action' && \App\Support\PaymentChoice::verified($booking))
                        <div class="booking-actions">
                            <a href="/owner/bookings/approve/{{ $booking->id }}" class="approve-btn">Approve</a>
                            <a href="/owner/bookings/reject/{{ $booking->id }}" class="reject-btn">Reject</a>
                        </div>
                    @endif
                </div>
            @empty
                <div class="booking-empty">No spot bookings found.</div>
            @endforelse
            <div class="booking-empty filter-empty" hidden>No spot bookings match these filters.</div>
        </div>
    </details>

    {{-- HOTEL --}}
    <details class="booking-accordion" data-section="hotel" {{ $hotelAction > 0 ? 'open' : '' }}>
        <summary>
            <div>
                <h2>Hotel bookings</h2>
                <small>{{ $hotelAction }} waiting · {{ $hotelBookings->count() }} total</small>
            </div>
            <span class="count">{{ $hotelBookings->count() }}</span>
        </summary>
        <div class="booking-accordion-body">
            @forelse($hotelBookings as $booking)
                @php
                    $group = in_array($booking->status, $pendingStatuses, true) ? 'action' : ($booking->status === 'approved' ? 'approved' : 'closed');
                    $place = $booking->hotel?->name ?? 'Removed hotel';
                @endphp
                <div class="booking-card"
                     data-type="hotel"
                     data-status="{{ $group }}"
                     data-search="{{ strtolower(($booking->user->name ?? '').' '.$place) }}">
                    <div class="booking-top">
                        <div class="booking-user">
                            <img src="{{ $booking->user->profile_image ? asset('uploads/profile/'.$booking->user->profile_image) : 'https://cdn-icons-png.flaticon.com/512/3135/3135715.png' }}" class="booking-avatar" alt="">
                            <div>
                                <h3>{{ $booking->user->name }}</h3>
                                <small>Hotel reservation</small>
                            </div>
                        </div>
                        <div>
                            @if($group === 'action')
                                <span class="status {{ $booking->receipt ? 'approved' : 'pending' }}">{{ $booking->receipt ? 'Paid — review receipt' : 'Awaiting payment' }}</span>
                            @elseif($group === 'approved')
                                <span class="status approved">Approved</span>
                            @elseif($booking->status === 'cancelled')
                                <span class="status cancelled">Cancelled</span>
                            @else
                                <span class="status rejected">Rejected</span>
                            @endif
                        </div>
                    </div>
                    <div class="booking-details">
                        <p><strong>{{ $place }}</strong></p>
                        <p>Check in: <strong>{{ \Carbon\Carbon::parse($booking->check_in)->format('F d, Y') }}</strong></p>
                        <p>Check out: <strong>{{ \Carbon\Carbon::parse($booking->check_out)->format('F d, Y') }}</strong></p>
                        <p>Rooms: <strong>{{ $booking->rooms }}@if($booking->room_name) · {{ $booking->room_name }}@endif</strong></p>
                        @if($booking->package_name)<p>Package: <strong>{{ $booking->package_name }}</strong></p>@endif
                        <p>Total: <strong>PHP {{ number_format($booking->total_price, 2) }}</strong></p>
                        @include('owner.partials.payment-proof', ['booking' => $booking])
                        @include('owner.partials.arrival', ['booking' => $booking])
                    </div>
                    @if($group === 'action' && \App\Support\PaymentChoice::verified($booking))
                        <div class="booking-actions">
                            <a href="/owner/hotel-bookings/approve/{{ $booking->id }}" class="approve-btn">Approve</a>
                            <a href="/owner/hotel-bookings/reject/{{ $booking->id }}" class="reject-btn">Reject</a>
                        </div>
                    @endif
                </div>
            @empty
                <div class="booking-empty">No hotel bookings found.</div>
            @endforelse
            <div class="booking-empty filter-empty" hidden>No hotel bookings match these filters.</div>
        </div>
    </details>

    {{-- FOOD --}}
    <details class="booking-accordion" data-section="food" {{ $foodAction > 0 ? 'open' : '' }}>
        <summary>
            <div>
                <h2>Restaurant bookings</h2>
                <small>{{ $foodAction }} waiting · {{ $foodBookings->count() }} total</small>
            </div>
            <span class="count">{{ $foodBookings->count() }}</span>
        </summary>
        <div class="booking-accordion-body">
            @forelse($foodBookings as $booking)
                @php
                    $group = in_array($booking->status, $pendingStatuses, true) ? 'action' : ($booking->status === 'approved' ? 'approved' : 'closed');
                    $place = $booking->food?->name ?? 'Removed restaurant';
                @endphp
                <div class="booking-card"
                     data-type="food"
                     data-status="{{ $group }}"
                     data-search="{{ strtolower(($booking->user->name ?? '').' '.$place) }}">
                    <div class="booking-top">
                        <div class="booking-user">
                            <img src="{{ $booking->user->profile_image ? asset('uploads/profile/'.$booking->user->profile_image) : 'https://cdn-icons-png.flaticon.com/512/3135/3135715.png' }}" class="booking-avatar" alt="">
                            <div>
                                <h3>{{ $booking->user->name }}</h3>
                                <small>Restaurant reservation</small>
                            </div>
                        </div>
                        <div>
                            @if($group === 'action')
                                <span class="status {{ $booking->receipt ? 'approved' : 'pending' }}">{{ $booking->receipt ? 'Paid — review receipt' : 'Awaiting payment' }}</span>
                            @elseif($group === 'approved')
                                <span class="status approved">Approved</span>
                            @elseif($booking->status === 'cancelled')
                                <span class="status cancelled">Cancelled</span>
                            @else
                                <span class="status rejected">Rejected</span>
                            @endif
                        </div>
                    </div>
                    <div class="booking-details">
                        <p><strong>{{ $place }}</strong></p>
                        <p>Date: <strong>{{ \Carbon\Carbon::parse($booking->reservation_date)->format('F d, Y') }}</strong></p>
                        <p>Time: <strong>{{ \Carbon\Carbon::parse($booking->reservation_time)->format('h:i A') }}</strong></p>
                        @if($booking->food)<p>Price per guest: <strong>PHP {{ number_format($booking->food->price_per_guest, 2) }}</strong></p>@endif
                        <p>Guests: <strong>{{ $booking->guests }}</strong></p>
                        @if($booking->package_name)<p>Package: <strong>{{ $booking->package_name }}</strong></p>@endif
                        <p>Total: <strong>PHP {{ number_format($booking->total_price, 2) }}</strong></p>
                        @if($booking->special_request)<p>Special request: <strong>{{ $booking->special_request }}</strong></p>@endif
                        @include('owner.partials.payment-proof', ['booking' => $booking])
                        @include('owner.partials.arrival', ['booking' => $booking])
                    </div>
                    @if($group === 'action' && \App\Support\PaymentChoice::verified($booking))
                        <div class="booking-actions">
                            <a href="/owner/food-bookings/approve/{{ $booking->id }}" class="approve-btn">Approve</a>
                            <a href="/owner/food-bookings/reject/{{ $booking->id }}" class="reject-btn">Reject</a>
                        </div>
                    @endif
                </div>
            @empty
                <div class="booking-empty">No restaurant bookings found.</div>
            @endforelse
            <div class="booking-empty filter-empty" hidden>No restaurant bookings match these filters.</div>
        </div>
    </details>
</div>

<script>
(function () {
    var typeFilter = document.getElementById('bookingType');
    var statusFilter = document.getElementById('bookingStatus');
    var searchFilter = document.getElementById('bookingSearch');
    var sections = Array.prototype.slice.call(document.querySelectorAll('.booking-accordion'));

    function applyFilters() {
        var type = typeFilter.value;
        var status = statusFilter.value;
        var query = (searchFilter.value || '').trim().toLowerCase();

        sections.forEach(function (section) {
            var sectionType = section.getAttribute('data-section');
            var showSection = type === 'all' || type === sectionType;
            section.hidden = !showSection;
            if (!showSection) return;

            var visible = 0;
            section.querySelectorAll('.booking-card').forEach(function (card) {
                var statusOk = status === 'all' || card.getAttribute('data-status') === status;
                var searchOk = !query || (card.getAttribute('data-search') || '').indexOf(query) !== -1;
                var show = statusOk && searchOk;
                card.hidden = !show;
                if (show) visible += 1;
            });

            var empty = section.querySelector('.filter-empty');
            if (empty) empty.hidden = visible > 0;
            if (visible > 0 && status === 'action') section.open = true;
        });
    }

    [typeFilter, statusFilter].forEach(function (el) {
        el.addEventListener('change', applyFilters);
    });
    searchFilter.addEventListener('input', applyFilters);
    applyFilters();

    document.querySelectorAll('.booking-actions a').forEach(function (action) {
        action.addEventListener('click', async function (event) {
            event.preventDefault();
            if (action.dataset.busy) return;
            action.dataset.busy = '1';
            try {
                var response = await fetch(action.href, { headers: { 'X-Requested-With': 'XMLHttpRequest' } });
                if (!response.ok) throw new Error('Unable to update booking');
                var card = action.closest('.booking-card');
                card.style.opacity = '.45';
                setTimeout(function () {
                    card.remove();
                    applyFilters();
                }, 180);
            } catch (error) {
                window.location.href = action.href;
            } finally {
                delete action.dataset.busy;
            }
        });
    });
})();
</script>
</body>
</html>
