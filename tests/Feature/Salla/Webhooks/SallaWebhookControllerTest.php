<?php

declare(strict_types=1);

namespace Tests\Feature\Salla\Webhooks;

use App\Domains\Commerce\Enums\OrderStatus;
use App\Domains\Commerce\Models\Order;
use App\Domains\Webhook\Models\SallaWebhookEvent;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Queue;
use Tests\TestCase;

class SallaWebhookControllerTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        config()->set('salla.webhooks.secret', 'test-webhook-secret');
        config()->set('salla.driver', 'mock');
        config()->set('queue.default', 'sync');
    }

    private function sign(array $payload): string
    {
        $body = json_encode($payload, JSON_UNESCAPED_UNICODE);

        return hash_hmac('sha256', $body, 'test-webhook-secret');
    }

    public function test_accepts_validly_signed_event(): void
    {
        Queue::fake();

        $payload = ['event' => 'product.updated', 'data' => ['id' => 1, 'name' => 'ساعة']];
        $body = json_encode($payload, JSON_UNESCAPED_UNICODE);

        $response = $this->call('POST', route('salla.webhook'), content: $body, server: [
            'HTTP_X-Salla-Signature' => $this->sign($payload),
            'CONTENT_TYPE' => 'application/json',
        ]);

        $response->assertStatus(200);
        $response->assertJson(['status' => 'accepted']);

        $this->assertSame(1, SallaWebhookEvent::count());
        $event = SallaWebhookEvent::first();
        $this->assertSame('product.updated', $event->event_name);
        $this->assertTrue($event->signature_valid);
        $this->assertNotNull($event->received_at);
    }

    public function test_rejects_invalid_signature(): void
    {
        $payload = ['event' => 'product.updated', 'data' => ['id' => 1]];
        $body = json_encode($payload, JSON_UNESCAPED_UNICODE);

        $response = $this->call('POST', route('salla.webhook'), content: $body, server: [
            'HTTP_X-Salla-Signature' => 'invalid-signature',
            'CONTENT_TYPE' => 'application/json',
        ]);

        $response->assertStatus(401);
        $this->assertSame(0, SallaWebhookEvent::count());
    }

    public function test_duplicate_delivery_is_idempotent(): void
    {
        Queue::fake();

        $payload = ['event' => 'order.created', 'data' => ['id' => 1000]];
        $body = json_encode($payload, JSON_UNESCAPED_UNICODE);
        $server = [
            'HTTP_X-Salla-Signature' => $this->sign($payload),
            'CONTENT_TYPE' => 'application/json',
        ];

        $this->call('POST', route('salla.webhook'), content: $body, server: $server)->assertStatus(200);
        $this->call('POST', route('salla.webhook'), content: $body, server: $server)->assertStatus(200);

        $this->assertSame(1, SallaWebhookEvent::count());

        Queue::assertPushed(\App\Jobs\ProcessSallaWebhook::class, 1);
    }

    public function test_invalid_json_payload_is_rejected(): void
    {
        $rawBody = 'not-json';

        $response = $this->call('POST', route('salla.webhook'), content: $rawBody, server: [
            'HTTP_X-Salla-Signature' => hash_hmac('sha256', $rawBody, 'test-webhook-secret'),
            'CONTENT_TYPE' => 'application/json',
        ]);

        $response->assertStatus(422);
    }

    public function test_unknown_event_name_is_rejected(): void
    {
        $payload = ['event' => '', 'data' => ['id' => 1]];
        $body = json_encode($payload, JSON_UNESCAPED_UNICODE);

        $response = $this->call('POST', route('salla.webhook'), content: $body, server: [
            'HTTP_X-Salla-Signature' => $this->sign($payload),
            'CONTENT_TYPE' => 'application/json',
        ]);

        $response->assertStatus(422);
    }

    public function test_order_event_stores_salla_order_id(): void
    {
        Queue::fake();

        $payload = ['event' => 'order.created', 'data' => ['id' => 1000]];
        $body = json_encode($payload, JSON_UNESCAPED_UNICODE);

        $this->call('POST', route('salla.webhook'), content: $body, server: [
            'HTTP_X-Salla-Signature' => $this->sign($payload),
            'CONTENT_TYPE' => 'application/json',
        ])->assertStatus(200);

        $event = SallaWebhookEvent::first();
        $this->assertSame('1000', $event->salla_order_id);
    }

    public function test_end_to_end_order_webhook_updates_local_order(): void
    {
        config()->set('queue.default', 'sync');

        $order = [
            'id' => 1000,
            'reference_id' => 500,
            'date' => ['date' => '2026-08-15 10:00:00'],
            'status' => ['slug' => 'processing', 'name' => 'قيد المعالجة'],
            'payment_method' => 'mada',
            'amounts' => [
                'sub_total' => ['amount' => 520.0, 'currency' => 'SAR'],
                'shipping_cost' => ['amount' => 20.0, 'currency' => 'SAR'],
                'tax' => ['amount' => 27.3, 'currency' => 'SAR'],
                'discounts' => ['amount' => 0.0, 'currency' => 'SAR'],
                'total' => ['amount' => 567.3, 'currency' => 'SAR'],
            ],
            'currency' => 'SAR',
            'items' => [],
        ];

        $payload = ['event' => 'order.created', 'data' => $order];
        $body = json_encode($payload, JSON_UNESCAPED_UNICODE);

        $this->call('POST', route('salla.webhook'), content: $body, server: [
            'HTTP_X-Salla-Signature' => $this->sign($payload),
            'CONTENT_TYPE' => 'application/json',
        ])->assertStatus(200);

        $event = SallaWebhookEvent::first();
        $this->assertNotNull($event);
        $this->assertNull($event->failed_at, $event->error_message ?? 'no error');
        $stored = Order::where('salla_id', '1000')->first();
        $this->assertNotNull($stored);
        $this->assertSame(OrderStatus::Processing, $stored->local_status);
        $this->assertSame('1000', $stored->salla_order_id);
        $this->assertSame(1, $stored->snapshots()->count());
    }
}