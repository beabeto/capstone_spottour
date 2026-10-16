@if($booking->status === 'approved')
    @php
        $arrivalCode = \App\Support\ArrivalCode::ensure($booking);
        $arrivalType = $booking instanceof \App\Models\HotelBooking ? 'hotel' : ($booking instanceof \App\Models\FoodBooking ? 'food' : 'spot');
    @endphp
    <p>Arrival code: <strong>{{ $arrivalCode }}</strong></p>
    @if($booking->arrived_at)
        <p>Checked in {{ $booking->arrived_at->format('M d, Y g:i A') }}</p>
    @else
        <p><a class="approve-btn" href="/owner/bookings/arrive/{{ $arrivalType }}/{{ $booking->id }}" onclick="return confirm('Mark this guest as arrived?')">Mark arrived</a></p>
    @endif
@endif
