<?php

declare(strict_types=1);

namespace Tests\Unit\Salla\Webhooks;

use App\Shared\Salla\Webhooks\SallaWebhookSignatureVerifier;
use Tests\TestCase;

class SallaWebhookSignatureTest extends TestCase
{
    private SallaWebhookSignatureVerifier $verifier;

    protected function setUp(): void
    {
        parent::setUp();

        $this->verifier = new SallaWebhookSignatureVerifier('webhook-secret-value');
    }

    public function test_valid_signature_is_accepted(): void
    {
        $body = '{"event":"order.created","data":{"id":1000}}';

        $signature = hash_hmac('sha256', $body, 'webhook-secret-value');

        $this->assertTrue($this->verifier->verify($signature, $body));
    }

    public function test_signature_with_sha256_prefix_is_accepted(): void
    {
        $body = '{"event":"order.created","data":{"id":1000}}';

        $signature = 'sha256='.hash_hmac('sha256', $body, 'webhook-secret-value');

        $this->assertTrue($this->verifier->verify($signature, $body));
    }

    public function test_tampered_body_is_rejected(): void
    {
        $signature = hash_hmac('sha256', '{"event":"order.created","data":{"id":1000}}', 'webhook-secret-value');

        $this->assertFalse($this->verifier->verify($signature, '{"event":"order.created","data":{"id":9999}}'));
    }

    public function test_wrong_secret_is_rejected(): void
    {
        $body = '{"event":"order.created","data":{"id":1000}}';

        $signature = hash_hmac('sha256', $body, 'another-secret');

        $this->assertFalse($this->verifier->verify($signature, $body));
    }

    public function test_empty_signature_is_rejected(): void
    {
        $body = '{"event":"order.created","data":{"id":1000}}';

        $this->assertFalse($this->verifier->verify('', $body));
    }

    public function test_empty_secret_rejects_all_signatures(): void
    {
        $verifier = new SallaWebhookSignatureVerifier('');

        $body = '{"event":"order.created","data":{"id":1000}}';
        $signature = hash_hmac('sha256', $body, '');

        $this->assertFalse($verifier->verify($signature, $body));
    }
}