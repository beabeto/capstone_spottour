<!DOCTYPE html>
<html>
<head>
    <title>Booking Monitoring</title>
    <link rel="stylesheet" href="{{ asset('css/style.css') }}">
</head>
<body class="admin-body admin-detail-page admin-bookings-page">
<div class="back-btn"><button type="button" onclick="window.history.back(); return false;">Back</button></div>
<main class="admin-main-full">
    <header class="management-heading"><span>Booking monitoring</span><h1>All booking requests</h1><p>See how many guests booked each place, plus the room, guide, or package they chose.</p></header>
    <section class="table-card booking-table-card">
        <h2>Bookings per place</h2>
        <div class="table-scroll"><table>
            <thead><tr><th>Place</th><th>Bookings</th><th>Units</th></tr></thead>
            <tbody>
            @foreach(['Spot' => $spotBookings, 'Hotel' => $hotelBookings, 'Restaurant' => $foodBookings] as $kind => $rows)
                @forelse($rows->groupBy(fn ($booking) => ($booking->spot ?? $booking->hotel ?? $booking->food)?->name ?? 'Removed listing') as $place => $group)
                    <tr>
                        <td><strong>{{ $place }}</strong><small>{{ $kind }}</small></td>
                        <td>{{ $group->count() }}</td>
                        <td>{{ $group->sum(fn ($booking) => $booking->quantity ?? $booking->rooms ?? $booking->guests ?? 1) }}</td>
                    </tr>
                @empty
                @endforelse
            @endforeach
            </tbody>
        </table></div>
    </section>
    @php $bookingGroups = ['Spot bookings' => $spotBookings, 'Hotel bookings' => $hotelBookings, 'Restaurant bookings' => $foodBookings]; @endphp
    @foreach($bookingGroups as $heading => $bookings)
    <section class="table-card booking-table-card">
        <h2>{{ $heading }}</h2>
        <div class="table-scroll"><table>
            <thead><tr><th>Guest</th><th>Listing</th><th>Schedule</th><th>Guests / units</th><th>Specification</th><th>Contact</th><th>Total</th><th>Status</th><th>Payment</th></tr></thead>
            <tbody>
            @forelse($bookings as $booking)
                @php
                    $listing = $booking->spot ?? $booking->hotel ?? $booking->food;
                    $schedule = isset($booking->booking_date) ? \Carbon\Carbon::parse($booking->booking_date)->format('M d, Y') : (isset($booking->check_in) ? \Carbon\Carbon::parse($booking->check_in)->format('M d, Y').' – '.\Carbon\Carbon::parse($booking->check_out)->format('M d, Y') : \Carbon\Carbon::parse($booking->reservation_date)->format('M d, Y').' · '.\Carbon\Carbon::parse($booking->reservation_time)->format('h:i A'));
                    $units = $booking->quantity ?? $booking->rooms ?? $booking->guests ?? 1;
                @endphp
                <tr>
                    <td><strong>{{ $booking->user?->name ?? 'Deleted user' }}</strong><small>{{ $booking->payer_name ?: '—' }}</small></td>
                    <td>{{ $listing?->name ?? 'Deleted listing' }}</td>
                    <td>{{ $schedule }}</td>
                    <td>{{ $units }}</td>
                    <td>
                        {{ $booking->room_name ?: (isset($booking->check_in) ? 'Standard room' : '—') }}
                        @if(!empty($booking->needs_guide))
                            <small>Tourist guide requested</small>
                        @endif
                        @if($booking->package_name)
                            <small>{{ $booking->package_name }}</small>
                        @endif
                    </td>
                    <td>{{ $booking->payer_number ?: ($booking->contact_email ?? '—') }}</td>
                    <td>PHP {{ number_format((float) $booking->total_price, 2) }}</td>
                    <td><span class="status {{ $booking->status }}">{{ ucfirst(str_replace('_', ' ', $booking->status)) }}</span></td>
                    <td>
                        {{ \App\Support\PaymentChoice::label($booking->payment_method) }}
                        @if(\App\Support\PaymentChoice::paidOnline($booking))
                            <small>PayMongo</small>
                        @elseif($booking->receipt)
                            <a class="view-btn" target="_blank" href="{{ asset('uploads/receipts/'.$booking->receipt) }}">Receipt</a>
                        @elseif($booking->payment_method === 'cash')
                            <small>Pay at the place</small>
                        @endif
                    </td>
                </tr>
            @empty
                <tr><td colspan="9" class="empty-table">No {{ strtolower($heading) }} found.</td></tr>
            @endforelse
            </tbody>
        </table></div>
    </section>
    @endforeach
</main>
</body>
</html>
