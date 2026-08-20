<?php

declare(strict_types=1);

namespace App\Jobs;

use App\Shared\Contracts\SallaClientContract;
use App\Shared\Salla\Sync\ProductSyncService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

/**
 * Performs a full, paginated product sync from Salla into the local catalog.
 */
final class SyncSallaProducts implements ShouldQueue
{
    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;
    use SerializesModels;

    public function __construct(
        public int $perPage = 50,
        public ?int $page = null,
    ) {}

    public function handle(SallaClientContract $client, ProductSyncService $sync): void
    {
        $page = $this->page ?? 1;

        $response = $client->get('products', [
            'page' => $page,
            'per_page' => $this->perPage,
        ]);

        $items = (array) ($response['data'] ?? []);
        $pagination = (array) ($response['pagination'] ?? []);

        foreach ($items as $raw) {
            if (is_array($raw)) {
                $sync->syncFromSalla($raw);
            }
        }

        $lastPage = (int) ($pagination['last_page'] ?? $pagination['pages'] ?? $page);

        if ($page < $lastPage) {
            self::dispatch($this->perPage, $page + 1);
        }
    }
}
