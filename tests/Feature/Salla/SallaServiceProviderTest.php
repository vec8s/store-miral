<?php

declare(strict_types=1);

namespace Tests\Feature\Salla;

use App\Shared\Contracts\SallaClientContract;
use App\Shared\Salla\MockSallaClient;
use App\Shared\Salla\SallaClient;
use App\Shared\Salla\SallaManager;
use Tests\TestCase;

class SallaServiceProviderTest extends TestCase
{
    public function test_container_resolves_salla_manager(): void
    {
        $manager = $this->app->make(SallaManager::class);

        $this->assertInstanceOf(SallaManager::class, $manager);
    }

    public function test_contract_resolves_mock_client_when_driver_is_mock(): void
    {
        config()->set('salla.driver', 'mock');

        $client = $this->app->make(SallaClientContract::class);

        $this->assertInstanceOf(MockSallaClient::class, $client);
    }

    public function test_contract_resolves_http_client_when_driver_is_http(): void
    {
        config()->set('salla.driver', 'http');

        $client = $this->app->make(SallaClientContract::class);

        $this->assertInstanceOf(SallaClient::class, $client);
    }

    public function test_contract_resolves_mock_when_credentials_absent_and_driver_auto(): void
    {
        config()->set('salla.driver', 'auto');
        config()->set('salla.client_id', '');
        config()->set('salla.client_secret', '');

        $client = $this->app->make(SallaClientContract::class);

        $this->assertInstanceOf(MockSallaClient::class, $client);
    }

    public function test_contract_resolves_http_when_credentials_present_and_driver_auto(): void
    {
        config()->set('salla.driver', 'auto');
        config()->set('salla.client_id', 'real-client');
        config()->set('salla.client_secret', 'real-secret');

        $client = $this->app->make(SallaClientContract::class);

        $this->assertInstanceOf(SallaClient::class, $client);
    }

    public function test_endpoint_is_bindable_from_contract(): void
    {
        $manager = $this->app->make(SallaManager::class);

        $this->assertSame(
            $this->app->make(SallaClientContract::class)::class,
            $manager->client()::class,
        );
    }
}