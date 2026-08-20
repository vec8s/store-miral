<?php

declare(strict_types=1);

namespace Tests\Unit\Salla;

use App\Shared\Contracts\SallaClientContract;
use App\Shared\Salla\MockSallaClient;
use App\Shared\Salla\SallaAuthenticator;
use App\Shared\Salla\SallaClient;
use App\Shared\Salla\SallaManager;
use Tests\TestCase;

class SallaManagerTest extends TestCase
{
    private SallaAuthenticator $authenticator;

    protected function setUp(): void
    {
        parent::setUp();

        $this->authenticator = new SallaAuthenticator('client', 'secret', 'https://localhost/callback');
    }

    public function test_returns_mock_client_when_driver_is_mock(): void
    {
        config()->set('salla.driver', 'mock');

        $manager = new SallaManager($this->authenticator);

        $this->assertSame('mock', $manager->driver());
        $this->assertInstanceOf(MockSallaClient::class, $manager->client());
    }

    public function test_returns_http_client_when_driver_is_http(): void
    {
        config()->set('salla.driver', 'http');

        $manager = new SallaManager($this->authenticator);

        $this->assertSame('http', $manager->driver());
        $this->assertInstanceOf(SallaClient::class, $manager->client());
    }

    public function test_auto_uses_http_when_credentials_present(): void
    {
        config()->set('salla.driver', 'auto');
        config()->set('salla.client_id', 'real-client');
        config()->set('salla.client_secret', 'real-secret');

        $manager = new SallaManager($this->authenticator);

        $this->assertSame('http', $manager->driver());
        $this->assertInstanceOf(SallaClient::class, $manager->client());
    }

    public function test_auto_falls_back_to_mock_when_credentials_missing(): void
    {
        config()->set('salla.driver', 'auto');
        config()->set('salla.client_id', '');
        config()->set('salla.client_secret', '');

        $manager = new SallaManager($this->authenticator);

        $this->assertSame('mock', $manager->driver());
        $this->assertInstanceOf(MockSallaClient::class, $manager->client());
    }

    public function test_default_driver_is_auto(): void
    {
        config()->set('salla.driver', null);
        config()->set('salla.client_id', '');
        config()->set('salla.client_secret', '');

        $manager = new SallaManager($this->authenticator);

        $this->assertSame('mock', $manager->driver());
    }

    public function test_client_implements_contract(): void
    {
        config()->set('salla.driver', 'http');

        $manager = new SallaManager($this->authenticator);

        $this->assertInstanceOf(SallaClientContract::class, $manager->client());
    }

    public function test_unknown_driver_falls_back_to_mock(): void
    {
        config()->set('salla.driver', 'bogus');

        $manager = new SallaManager($this->authenticator);

        $this->assertSame('mock', $manager->driver());
        $this->assertInstanceOf(MockSallaClient::class, $manager->client());
    }
}