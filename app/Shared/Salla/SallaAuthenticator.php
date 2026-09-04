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
        $merchantId = (string) config('salla.merchant_id');
        $tokenRecord = $merchantId !== ''
            ? SallaToken::query()->where('merchant_id', $merchantId)->first()
            : SallaToken::query()->latest()->first();

        if ($tokenRecord === null) {
            $tokenRecord = SallaToken::query()->latest()->first();
        }

        if ($tokenRecord === null) {
            throw SallaAuthException::fromOAuthFailure(
                status: 401,
                message: 'لم يتم العثور على توكن سلة في قاعدة البيانات. يرجى ربط المتجر عبر OAuth أولاً.',
            );
        }

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
            'metadata' => $data,
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

        $merchantId = (string) (config('salla.merchant_id')
            ?: ($data['merchant_id'] ?? $data['user']['merchant']['id'] ?? $data['merchant'] ?? 'default_merchant'));

        SallaToken::query()->updateOrCreate(
            ['merchant_id' => $merchantId],
            [
                'access_token' => (string) $data['access_token'],
                'refresh_token' => (string) $data['refresh_token'],
                'token_type' => (string) ($data['token_type'] ?? 'Bearer'),
                'scope' => (string) ($data['scope'] ?? ''),
                'access_token_expires_at' => now()->addSeconds((int) ($data['expires_in'] ?? 3600)),
                'metadata' => $data,
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

        $merchantId = (string) config('salla.merchant_id');
        if ($merchantId !== '') {
            SallaToken::query()->where('merchant_id', $merchantId)->delete();
        } else {
            SallaToken::query()->truncate();
        }
    }
}
