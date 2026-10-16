<?php

namespace App\Support;

use Illuminate\Http\Request;

class PaymentChoice
{
    public const METHODS = [
        'cash' => 'Cash',
        'gcash' => 'GCash',
        'maya' => 'Maya',
        'bank' => 'Bank transfer',
    ];

    public static function rules(): array
    {
        return [
            'payment_method' => 'required|in:cash,gcash,maya,bank',
            'payer_name' => 'required_if:payment_method,bank|nullable|string|max:100',
            'payer_number' => 'required_if:payment_method,bank|nullable|string|max:30',
            'payment_reference' => 'required_if:payment_method,bank|nullable|string|max:80',
            'receipt' => 'required_if:payment_method,bank|nullable|image|mimes:jpeg,png,webp|max:5120',
            'contact_email' => 'nullable|email|max:255',
        ];
    }

    public static function validateNumbers(Request $request, $validator): void
    {
        $validator->after(function ($validator) use ($request) {
            $method = $request->input('payment_method');
            $number = (string) $request->input('payer_number');

            if ($method === 'bank' && $number !== '' && strlen($number) < 5) {
                $validator->errors()->add('payer_number', 'Enter the account number that sent the transfer.');
            }
        });
    }

    public static function store(Request $request): array
    {
        $method = $request->input('payment_method');
        $receipt = null;

        if ($method === 'bank' && $request->hasFile('receipt')) {
            $file = $request->file('receipt');
            $receipt = time().'_'.$file->getClientOriginalName();
            $file->move(public_path('uploads/receipts'), $receipt);
        }

        return [
            'payment_method' => $method,
            'payment_reference' => $method === 'bank' ? (string) $request->input('payment_reference') : '',
            'payer_name' => $method === 'bank'
                ? (string) $request->input('payer_name')
                : (string) ($request->user()->name ?? 'Guest'),
            'payer_number' => $method === 'bank'
                ? (string) $request->input('payer_number')
                : '',
            'receipt' => $receipt,
            'contact_email' => $request->input('contact_email') ?: $request->user()->email,
            'status' => self::hosted($method) ? 'pending_payment' : 'pending_verification',
        ];
    }

    public static function hosted(?string $method): bool
    {
        return in_array($method, ['gcash', 'maya'], true);
    }

    public static function paidOnline(object $booking): bool
    {
        return self::hosted($booking->payment_method)
            && str_starts_with((string) $booking->payment_reference, 'pay_');
    }

    public static function label(?string $method): string
    {
        return self::METHODS[$method] ?? 'GCash';
    }

    public static function notice(string $guest, string $place, ?string $method): string
    {
        if ($method === 'cash') {
            return $guest.' chose to pay cash for "'.$place.'". Verify the payment before approving.';
        }

        if (self::hosted($method)) {
            return $guest.' paid for "'.$place.'" with '.self::label($method).' through PayMongo. You can approve the booking.';
        }

        return $guest.' sent a '.self::label($method).' payment for "'.$place.'". Verify the receipt before approving.';
    }

    public static function verified(object $booking): bool
    {
        if ($booking->payment_method === 'cash' || self::paidOnline($booking)) {
            return true;
        }

        if (self::hosted($booking->payment_method)) {
            return false;
        }

        if (blank($booking->receipt)) {
            return false;
        }

        if (blank($booking->payment_method)) {
            return true;
        }

        return filled($booking->payment_reference);
    }
}
