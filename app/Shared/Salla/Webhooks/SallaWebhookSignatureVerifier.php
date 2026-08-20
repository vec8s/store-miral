<?php

declare(strict_types=1);

namespace App\Shared\Salla\Webhooks;

use Illuminate\Support\Str;

/**
 * Verifies Salla webhook signatures.
 *
 * Salla signs every webhook request with the HMAC-SHA256 of the raw request body
 * using the webhook secret configured in the Salla app dashboard. The signature is
 * sent in the `X-Salla-Signature` header.
 */
final class SallaWebhookSignatureVerifier
{
    public function __construct(private readonly string $secret) {}

    public function verify(string $signature, string $rawBody): bool
    {
        if ($this->secret === '' || $signature === '') {
            return false;
        }

        $expected = hash_hmac('sha256', $rawBody, $this->secret);

        if (Str::startsWith($signature, 'sha256=')) {
            $signature = Str::after($signature, 'sha256=');
        }

        return hash_equals($expected, $signature);
    }
}
