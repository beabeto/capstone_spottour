@php
    $method = $booking->payment_method;
    $label = \App\Support\PaymentChoice::label($method);
    $cash = $method === 'cash';
    $paidOnline = \App\Support\PaymentChoice::paidOnline($booking);
    $hosted = \App\Support\PaymentChoice::hosted($method);
    $waiting = in_array($booking->status, ['pending', 'pending_payment', 'pending_verification'], true);
@endphp
<div class="payment-proof {{ $cash || $paidOnline || $booking->receipt ? 'is-paid' : 'is-unpaid' }}">
    <strong>{{ $paidOnline ? $label.' paid through PayMongo' : ($cash ? 'Cash payment' : ($booking->receipt ? $label.' payment received' : 'No payment yet')) }}</strong>
    <span>
        @if($paidOnline && $waiting)
            PayMongo confirmed this {{ $label }} payment. You can approve the booking.
        @elseif($hosted && $waiting)
            The guest has not finished GCash or Maya checkout yet.
        @elseif($cash && $waiting)
            The guest will pay at the place. Verify the cash before you approve.
        @elseif(! $booking->receipt)
            Approval stays locked until the guest uploads a payment receipt.
        @elseif($waiting)
            Open the {{ $label }} receipt and confirm the amount and reference before you approve.
        @else
            The guest paid by {{ $label }} and the payment was verified.
        @endif
    </span>
    @if($booking->payer_name || $booking->payer_number || ($booking->payment_reference && ! $hosted))
        <small>
            {{ $booking->payer_name ?: 'Guest' }}
            @if($booking->payer_number)
                · {{ $label }} {{ $booking->payer_number }}
            @endif
            @if($booking->payment_reference && ! $hosted)
                · Ref {{ $booking->payment_reference }}
            @endif
        </small>
    @endif
    @if($booking->receipt)
        <a href="{{ asset('uploads/receipts/'.$booking->receipt) }}" target="_blank">Open payment receipt</a>
    @endif
</div>
