<?php

declare(strict_types=1);

namespace App\Shared\Salla;

use App\Domains\Settings\Models\SallaToken;
use App\Shared\Salla\Exceptions\SallaAuthException;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;

final class SallaAuthenticator
{
    private const TOKEN_URL = 'https://accounts.salla.sa/oauth2/token';

    public function __construct(
        private readonly string $clientId,
        private readonly string $clientSecret,
        private readonly string $redirectUri,
    ) {}

    public function getAccessToken(): string
    {
        $cacheKey = (string) config('salla.cache.token_key');
        $ttl = (int) config('salla.cache.ttl');

        $cached = Cache::get($cacheKey);
        if (is_string($cached) && $cached !== '') {
            return $cached;
        }

        return $this->refreshAccessToken();
    }

    public function refreshAccessToken(): string
    {
        $tokenRecord = SallaToken::query()
            ->where('merchant_id', (string) config('salla.merchant_id'))
            ->firstOrFail();

        $response = Http::asForm()->post(self::TOKEN_URL, [
            'grant_type' => 'refresh_token',
            'client_id' => $this->clientId,
            'client_secret' => $this->clientSecret,
            'refresh_token' => (string) $tokenRecord->refresh_token,
        ]);

        if (! $response->successful()) {
            throw SallaAuthException::fromOAuthFailure(
                status: $response->status(),
                message: (string) ($response->json('message') ?? $response->body()),
            );
        }

        /** @var array<string, mixed> $data */
        $data = $response->json();

        $accessToken = (string) $data['access_token'];
        $refreshToken = (string) $data['refresh_token'];
        $expiresIn = (int) $data['expires_in'];

        $tokenRecord->update([
            'access_token' => $accessToken,
            'refresh_token' => $refreshToken,
            'access_token_expires_at' => now()->addSeconds($expiresIn),
        ]);

        Cache::put(
            key: (string) config('salla.cache.token_key'),
            value: $accessToken,
            ttl: (int) config('salla.cache.ttl'),
        );

        return $accessToken;
    }

    /**
     * @return array<string, mixed>
     */
    public function exchangeCode(string $code): array
    {
        $response = Http::asForm()->post(self::TOKEN_URL, [
            'grant_type' => 'authorization_code',
            'client_id' => $this->clientId,
            'client_secret' => $this->clientSecret,
            'code' => $code,
            'redirect_uri' => $this->redirectUri,
        ]);

        if (! $response->successful()) {
            throw SallaAuthException::fromOAuthFailure(
                status: $response->status(),
                message: (string) ($response->json('message') ?? $response->body()),
            );
        }

        /** @var array<string, mixed> $data */
        $data = $response->json();

        SallaToken::query()->updateOrCreate(
            ['merchant_id' => (string) config('salla.merchant_id')],
            [
                'access_token' => (string) $data['access_token'],
                'refresh_token' => (string) $data['refresh_token'],
                'access_token_expires_at' => now()->addSeconds((int) $data['expires_in']),
            ],
        );

        Cache::put(
            key: (string) config('salla.cache.token_key'),
            value: (string) $data['access_token'],
            ttl: (int) config('salla.cache.ttl'),
        );

        return $data;
    }

    public function invalidate(): void
    {
        Cache::forget((string) config('salla.cache.token_key'));

        SallaToken::query()
            ->where('merchant_id', (string) config('salla.merchant_id'))
            ->delete();
    }
}
