<?php

declare(strict_types=1);

namespace App\Http\Controllers\Salla;

use App\Domains\Webhook\Models\SallaWebhookEvent;
use App\Jobs\ProcessSallaWebhook;
use App\Shared\Salla\Webhooks\SallaWebhookSignatureVerifier;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

/**
 * Public ingress for Salla webhooks.
 *
 * The route is CSRF-exempt; authenticity is established through the
 * HMAC-SHA256 signature Salla sends in the X-Salla-Signature header.
 *
 * The raw request body is hashed to form a unique event_key, making webhook
 * deliveries idempotent (Salla retries on failure).
 */
final class SallaWebhookController
{
    public function __construct(private readonly SallaWebhookSignatureVerifier $verifier) {}

    public function __invoke(Request $request): JsonResponse
    {
        $rawBody = (string) $request->getContent();
        $signature = (string) $request->header('X-Salla-Signature', '');

        if (! $this->verifier->verify($signature, $rawBody)) {
            return response()->json(['message' => 'Invalid signature.'], 401);
        }

        $payload = json_decode($rawBody, true);

        if (! is_array($payload)) {
            return response()->json(['message' => 'Invalid payload.'], 422);
        }

        $eventName = (string) ($payload['event'] ?? $payload['name'] ?? '');

        if ($eventName === '') {
            return response()->json(['message' => 'Unknown event.'], 422);
        }

        $data = is_array($payload['data'] ?? null) ? $payload['data'] : [];
        $orderId = $this->extractOrderId($data, $eventName);
        $eventKey = hash('sha256', $rawBody);

        $event = SallaWebhookEvent::firstOrCreate(
            ['event_key' => $eventKey],
            [
                'event_name' => $eventName,
                'salla_order_id' => $orderId,
                'payload' => $data,
                'payload_hash' => hash('sha256', json_encode($data) ?: ''),
                'signature_valid' => true,
                'received_at' => now(),
            ],
        );

        if ($event->wasRecentlyCreated) {
            ProcessSallaWebhook::dispatch($event->id);
        }

        return response()->json(['status' => 'accepted']);
    }

    /**
     * @param  array<string, mixed>  $data
     */
    private function extractOrderId(array $data, string $eventName): ?string
    {
        if (! str_starts_with($eventName, 'order.')) {
            return null;
        }

        $id = $data['id'] ?? $data['order']['id'] ?? null;

        return $id !== null ? (string) $id : null;
    }
}
