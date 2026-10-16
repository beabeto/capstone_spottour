<?php

namespace App\Support;

use Illuminate\Support\Facades\Http;
use RuntimeException;

class PayMongoCheckout
{
    public static function create(string $name, int $amountCentavos, string $method, string $reference, string $successUrl, string $email): array
    {
        $secret = config('services.paymongo.secret');

        if (blank($secret)) {
            throw new RuntimeException('PayMongo is not configured.');
        }

        if ($amountCentavos < 10000) {
            throw new RuntimeException('GCash and Maya checkout needs a total of at least PHP 100.00.');
        }

        $type = $method === 'maya' ? 'paymaya' : 'gcash';
        $intent = self::http($secret)->post('https://api.paymongo.com/v1/payment_intents', [
            'data' => [
                'attributes' => [
                    'amount' => $amountCentavos,
                    'currency' => 'PHP',
                    'payment_method_allowed' => [$type],
                    'description' => $name,
                    'statement_descriptor' => 'SpotTour '.$reference,
                ],
            ],
        ]);
        $intentId = $intent->json('data.id');

        if (! $intent->successful() || blank($intentId)) {
            throw new RuntimeException(self::errorDetail($intent) ?: 'PayMongo could not start the payment.');
        }

        $paymentMethod = self::http($secret)->post('https://api.paymongo.com/v1/payment_methods', [
            'data' => [
                'attributes' => [
                    'type' => $type,
                    'billing' => [
                        'name' => 'SpotTour guest',
                        'email' => $email,
                    ],
                ],
            ],
        ]);
        $methodId = $paymentMethod->json('data.id');

        if (! $paymentMethod->successful() || blank($methodId)) {
            throw new RuntimeException(self::errorDetail($paymentMethod) ?: 'PayMongo could not open the wallet.');
        }

        $attached = self::http($secret)->post('https://api.paymongo.com/v1/payment_intents/'.$intentId.'/attach', [
            'data' => [
                'attributes' => [
                    'payment_method' => $methodId,
                    'return_url' => $successUrl,
                ],
            ],
        ]);
        $url = $attached->json('data.attributes.next_action.redirect.url');

        if (! $attached->successful() || blank($url)) {
            throw new RuntimeException(self::errorDetail($attached) ?: 'PayMongo could not open the wallet.');
        }

        return ['id' => $intentId, 'url' => $url];
    }

    public static function payment(string $reference): ?array
    {
        $secret = config('services.paymongo.secret');

        if (blank($secret) || blank($reference)) {
            return null;
        }

        $path = str_starts_with($reference, 'pi_')
            ? 'https://api.paymongo.com/v1/payment_intents/'.$reference
            : 'https://api.paymongo.com/v1/checkout_sessions/'.$reference;
        $response = self::http($secret)->get($path);

        if (! $response->successful()) {
            return null;
        }

        $payments = $response->json('data.attributes.payments') ?? [];

        foreach ($payments as $payment) {
            if (($payment['attributes']['status'] ?? '') === 'paid') {
                return [
                    'id' => $payment['id'] ?? $reference,
                    'name' => null,
                ];
            }
        }

        return null;
    }

    private static function errorDetail($response): ?string
    {
        return $response->json('errors.0.detail');
    }

    private static function http(string $secret)
    {
        $request = Http::withBasicAuth($secret, '')
            ->acceptJson()
            ->timeout(20);

        $ca = ini_get('curl.cainfo') ?: ini_get('openssl.cafile');
        if (! is_string($ca) || ! is_file($ca)) {
            $ca = storage_path('app/cacert.pem');
        }

        if (is_file($ca)) {
            $request = $request->withOptions(['verify' => $ca]);
        }

        return $request;
    }
}
