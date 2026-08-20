<?php

declare(strict_types=1);

namespace App\Jobs;

use App\Domains\Webhook\Models\SallaWebhookEvent;
use App\Shared\Salla\Webhooks\SallaWebhookDispatcher;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Throwable;

/**
 * Consumes a persisted Salla webhook event from the queue and dispatches it
 * to the owning handler. Failures are recorded on the event for auditing and
 * the job is retried by the queue worker.
 */
final class ProcessSallaWebhook implements ShouldQueue
{
    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;
    use SerializesModels;

    public int $tries = 3;

    public int $backoff = 60;

    public function __construct(public readonly int $webhookEventId) {}

    public function handle(SallaWebhookDispatcher $dispatcher): void
    {
        $event = SallaWebhookEvent::find($this->webhookEventId);

        if ($event === null || $event->processed_at !== null) {
            return;
        }

        $event->increment('attempts');

        try {
            $dispatcher->dispatch((string) $event->event_name, $event->payload ?? []);

            $event->update([
                'processed_at' => now(),
                'error_message' => null,
            ]);
        } catch (Throwable $e) {
            $event->update([
                'failed_at' => now(),
                'error_message' => $e->getMessage(),
            ]);

            throw $e;
        }
    }
}
