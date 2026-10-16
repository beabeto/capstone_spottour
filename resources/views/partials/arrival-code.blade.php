@if($booking->status === 'approved')
    @php $arrivalCode = \App\Support\ArrivalCode::ensure($booking); @endphp
    <div class="booking-info"><strong>Arrival code</strong>{{ $arrivalCode }}</div>
    <div class="booking-info"><strong>Check-in</strong>{{ $booking->arrived_at ? 'Arrived '.$booking->arrived_at->format('M d, Y g:i A') : 'Show this code at the establishment' }}</div>
@endif
