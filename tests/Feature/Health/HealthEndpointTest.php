<?php

declare(strict_types=1);

namespace Tests\Feature\Health;

use Tests\TestCase;

class HealthEndpointTest extends TestCase
{
    public function test_up_endpoint_returns_ok_when_all_checks_pass(): void
    {
        $response = $this->getJson('/up');

        $response->assertOk();
        $response->assertJsonPath('status', 'ok');
    }

    public function test_up_endpoint_returns_detailed_checks_with_details_param(): void
    {
        $response = $this->getJson('/up?details=1');

        $response->assertOk();
        $response->assertJsonStructure([
            'status',
            'checks' => [
                'database' => ['status', 'name', 'message', 'duration_ms'],
                'cache',
                'queue',
                'storage',
                'mail',
                'salla',
            ],
        ]);
    }

    public function test_up_endpoint_without_details_hides_internal_details(): void
    {
        $response = $this->getJson('/up');

        $response->assertOk();
        $response->assertJsonMissingPath('checks.database');
    }

    public function test_up_endpoint_returns_503_when_a_check_fails(): void
    {
        config()->set('mail.default', 'smtp');
        config()->set('mail.mailers.smtp.transport', 'smtp');
        config()->set('mail.mailers.smtp.host', '');

        $response = $this->getJson('/up?details=1');

        $response->assertStatus(503);
        $response->assertJsonPath('status', 'degraded');
        $response->assertJsonPath('checks.mail.status', 'failed');
    }
}