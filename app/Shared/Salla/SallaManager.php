<?php

declare(strict_types=1);

namespace App\Shared\Salla;

use App\Shared\Contracts\SallaClientContract;

/**
 * Selects the active Salla client implementation based on configuration.
 *
 * Supported drivers:
 *  - "mock": deterministic, network-free fixtures (default when credentials absent)
 *  - "http": real Salla Merchant API client
 *  - "auto": real client when credentials exist, mock otherwise
 */
final class SallaManager
{
    public function __construct(
        private readonly SallaAuthenticator $authenticator,
    ) {}

    public function driver(): string
    {
        $configured = strtolower((string) config('salla.driver', 'auto'));

        return match ($configured) {
            'mock', 'http' => $configured,
            'auto' => $this->hasCredentials() ? 'http' : 'mock',
            default => 'mock',
        };
    }

    public function client(): SallaClientContract
    {
        return $this->driver() === 'http'
            ? new SallaClient($this->authenticator)
            : new MockSallaClient();
    }

    private function hasCredentials(): bool
    {
        return (string) config('salla.client_id', '') !== ''
            && (string) config('salla.client_secret', '') !== '';
    }
}