<?php

declare(strict_types=1);

namespace App\Console\Commands;

use App\Shared\Contracts\SallaClientContract;
use App\Shared\Salla\Sync\OrderSyncService;
use App\Shared\Salla\Sync\ProductSyncService;
use Illuminate\Console\Command;
use Throwable;

final class SallaSyncCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'salla:sync 
                            {--type=all : The type of resources to sync (all, products, orders)} 
                            {--per-page=50 : Number of records to fetch per page}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Synchronize catalog products and orders directly from Salla into the local database';

    /**
     * Execute the console command.
     */
    public function handle(
        SallaClientContract $client,
        ProductSyncService $productSync,
        OrderSyncService $orderSync
    ): int {
        $type = (string) ($this->option('type') ?: 'all');
        $perPage = (int) ($this->option('per-page') ?: 50);

        $this->info('====================================================');
        $this->info(' 🚀 بدء عملية مزامنة متجر ميرال مع منصة سلة (Salla) ');
        $this->info('====================================================');
        $this->line("• نوع المزامنة: <fg=cyan>{$type}</>");
        $this->line("• عدد العناصر بالصفحة: <fg=cyan>{$perPage}</>");
        $this->newLine();

        $syncedProductsCount = 0;
        $syncedOrdersCount = 0;

        // 1. Sync Products
        if (in_array($type, ['all', 'products'], true)) {
            $this->comment('📦 جارٍ جلب ومزامنة المنتجات من منصة سلة...');
            $syncedProductsCount = $this->syncProducts($client, $productSync, $perPage);
            $this->info("✔ اكتملت مزامنة المنتجات: تم تحديث {$syncedProductsCount} منتج بنجاح.");
            $this->newLine();
        }

        // 2. Sync Orders
        if (in_array($type, ['all', 'orders'], true)) {
            $this->comment('🛒 جارٍ جلب ومزامنة الطلبات من منصة سلة...');
            $syncedOrdersCount = $this->syncOrders($client, $orderSync, $perPage);
            $this->info("✔ اكتملت مزامنة الطلبات: تم تحديث {$syncedOrdersCount} طلب بنجاح.");
            $this->newLine();
        }

        $this->info('====================================================');
        $this->info(' ✨ اكتملت عملية المزامنة بنجاح تام! ');
        $this->table(
            ['المورد (Resource)', 'العدد الإجمالي المُزامن'],
            [
                ['المنتجات (Products)', $syncedProductsCount],
                ['الطلبات (Orders)', $syncedOrdersCount],
                ['إجمالي سجلات المنتجات المحلية', $productSync->count()],
            ]
        );
        $this->info('====================================================');

        return Command::SUCCESS;
    }

    private function syncProducts(SallaClientContract $client, ProductSyncService $sync, int $perPage): int
    {
        $page = 1;
        $totalSynced = 0;

        do {
            try {
                $this->line("  - جلب صفحة المنتجات رقم #{$page}...");
                $response = $client->get('products', [
                    'page' => $page,
                    'per_page' => $perPage,
                ]);

                $items = (array) ($response['data'] ?? []);
                $pagination = (array) ($response['pagination'] ?? []);

                if (empty($items)) {
                    break;
                }

                $bar = $this->output->createProgressBar(count($items));
                $bar->start();

                foreach ($items as $raw) {
                    if (is_array($raw)) {
                        $sync->syncFromSalla($raw);
                        $totalSynced++;
                    }
                    $bar->advance();
                }

                $bar->finish();
                $this->newLine();

                $lastPage = (int) ($pagination['last_page'] ?? $pagination['pages'] ?? $page);
                $page++;
            } catch (Throwable $e) {
                $this->error("  ✖ حدث خطأ أثناء مزامنة صفحة المنتجات #{$page}: ".$e->getMessage());
                break;
            }
        } while ($page <= $lastPage);

        return $totalSynced;
    }

    private function syncOrders(SallaClientContract $client, OrderSyncService $sync, int $perPage): int
    {
        $page = 1;
        $totalSynced = 0;

        do {
            try {
                $this->line("  - جلب صفحة الطلبات رقم #{$page}...");
                $response = $client->get('orders', [
                    'page' => $page,
                    'per_page' => $perPage,
                ]);

                $items = (array) ($response['data'] ?? []);
                $pagination = (array) ($response['pagination'] ?? []);

                if (empty($items)) {
                    break;
                }

                $bar = $this->output->createProgressBar(count($items));
                $bar->start();

                foreach ($items as $raw) {
                    if (is_array($raw)) {
                        $sync->syncFromSalla($raw);
                        $totalSynced++;
                    }
                    $bar->advance();
                }

                $bar->finish();
                $this->newLine();

                $lastPage = (int) ($pagination['last_page'] ?? $pagination['pages'] ?? $page);
                $page++;
            } catch (Throwable $e) {
                $this->error("  ✖ حدث خطأ أثناء مزامنة صفحة الطلبات #{$page}: ".$e->getMessage());
                break;
            }
        } while ($page <= $lastPage);

        return $totalSynced;
    }
}
