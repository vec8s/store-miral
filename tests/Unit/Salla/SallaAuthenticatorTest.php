<?php

declare(strict_types=1);

namespace Tests\Unit\Salla;

use App\Domains\Settings\Models\SallaToken;
use App\Shared\Salla\SallaAuthenticator;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class SallaAuthenticatorTest extends TestCase
{
    use RefreshDatabase;

    private SallaAuthenticator $authenticator;

    protected function setUp(): void
    {
        parent::setUp();

        config()->set('salla.client_id', 'test-client');
        config()->set('salla.client_secret', 'test-secret');
        config()->set('salla.redirect_uri', 'https://localhost/callback');
        config()->set('salla.merchant_id', 'm-123');
        config()->set('salla.cache.token_key', 'salla_access_token');
        config()->set('salla.cache.ttl', 3540);

        $this->authenticator = new SallaAuthenticator('test-client', 'test-secret', 'https://localhost/callback');
    }

    public function test_refresh_access_token_updates_token_record_and_cache(): void
    {
        SallaToken::query()->create([
            'merchant_id' => 'm-123',
            'access_token' => 'old-token',
            'refresh_token' => 'refresh-abc',
        ]);

        Http::fake([
            'https://accounts.salla.sa/oauth2/token' => Http::response([
                'access_token' => 'new-token',
                'refresh_token' => 'refresh-new',
                'expires_in' => 14400,
                'token_type' => 'Bearer',
            ], 200),
        ]);

        $token = $this->authenticator->refreshAccessToken();

        $this->assertSame('new-token', $token);
        $this->assertSame('new-token', Cache::get('salla_access_token'));

        $record = SallaToken::query()->where('merchant_id', 'm-123')->first();
        $this->assertNotNull($record);
        $this->assertSame('new-token', $record->access_token);
        $this->assertSame('refresh-new', $record->refresh_token);
        $this->assertNotNull($record->access_token_expires_at);
    }

    public function test_get_access_token_returns_cached_token_without_refresh(): void
    {
        Cache::put('salla_access_token', 'cached-token', 3600);

        Http::fake();

        $this->assertSame('cached-token', $this->authenticator->getAccessToken());

        Http::assertNothingSent();
    }

    public function test_invalidate_clears_cache_and_record(): void
    {
        Cache::put('salla_access_token', 'cached-token', 3600);
        SallaToken::query()->create([
            'merchant_id' => 'm-123',
            'access_token' => 'old-token',
            'refresh_token' => 'refresh-abc',
        ]);

        $this->authenticator->invalidate();

        $this->assertNull(Cache::get('salla_access_token'));
        $this->assertSame(0, SallaToken::query()->count());
    }
}