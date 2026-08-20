<?php

declare(strict_types=1);

namespace Tests\Unit\Salla\Sync;

use App\Domains\Catalog\Models\Product;
use App\Domains\Commerce\Enums\OrderStatus;
use App\Domains\Commerce\Enums\PaymentMethod;
use App\Domains\Commerce\Enums\PaymentStatus;
use App\Domains\Commerce\Models\Order;
use App\Domains\Commerce\Models\OrderSnapshot;
use App\Shared\Salla\Sync\OrderSyncService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class OrderSyncServiceTest extends TestCase
{
    use RefreshDatabase;

    private OrderSyncService $service;

    protected function setUp(): void
    {
        parent::setUp();

        $this->service = new OrderSyncService();
    }

    private function rawOrder(int $id = 1000): array
    {
        return [
            'id' => $id,
            'reference_id' => 500,
            'date' => ['date' => '2026-08-15 10:00:00'],
            'source' => 'salla',
            'status' => ['slug' => 'pending', 'name' => 'قيد الانتظار'],
            'payment_method' => 'mada',
            'amounts' => [
                'sub_total' => ['amount' => 520.0, 'currency' => 'SAR'],
                'shipping_cost' => ['amount' => 20.0, 'currency' => 'SAR'],
                'tax' => ['amount' => 27.3, 'currency' => 'SAR'],
                'discounts' => ['amount' => 0.0, 'currency' => 'SAR'],
                'total' => ['amount' => 567.3, 'currency' => 'SAR'],
            ],
            'currency' => 'SAR',
            'items' => [
                [
                    'id' => 9001,
                    'product' => ['id' => 101, 'name' => 'ساعة فاخرة'],
                    'sku' => 'SKU-101',
                    'quantity' => 1,
                    'unit_price' => ['amount' => 520.0],
                    'total' => ['amount' => 520.0],
                ],
            ],
        ];
    }

    public function test_creates_order_from_salla_payload(): void
    {
        $order = $this->service->syncFromSalla($this->rawOrder());

        $this->assertInstanceOf(Order::class, $order);
        $this->assertSame('1000', $order->salla_id);
        $this->assertSame('500', $order->reference_id);
        $this->assertSame(OrderStatus::Pending, $order->local_status);
        $this->assertSame('pending', $order->salla_status);
        $this->assertSame(PaymentStatus::Pending, $order->payment_status);
        $this->assertSame(PaymentMethod::Mada, $order->payment_method);
        $this->assertSame(52000, $order->subtotal_minor);
        $this->assertSame(56730, $order->total_minor);
        $this->assertSame(1, $order->items()->count());
        $this->assertNotNull($order->placed_at);
    }

    public function test_updates_existing_order_instead_of_duplicating(): void
    {
        $this->service->syncFromSalla($this->rawOrder());

        $updated = $this->rawOrder();
        $updated['status'] = ['slug' => 'delivered', 'name' => 'تم التسليم'];
        $updated['amounts']['total'] = ['amount' => 567.3, 'currency' => 'SAR'];

        $order = $this->service->syncFromSalla($updated);

        $this->assertSame(1, Order::count());
        $this->assertSame(OrderStatus::Delivered, $order->local_status);
        $this->assertSame(PaymentStatus::Paid, $order->payment_status);
    }

    public function test_creates_items_with_product_link(): void
    {
        Product::create([
            'salla_id' => '101',
            'salla_product_id' => '101',
            'name' => 'ساعة فاخرة',
            'slug' => 'sa3a-fakhira-101',
            'price_minor' => 52000,
            'quantity' => 5,
        ]);

        $order = $this->service->syncFromSalla($this->rawOrder());

        $item = $order->items()->first();

        $this->assertSame('ساعة فاخرة', $item->name);
        $this->assertSame('101', $item->salla_product_id);
        $this->assertNotNull($item->product_id);
    }

    public function test_records_snapshot_on_first_sync(): void
    {
        $order = $this->service->syncFromSalla($this->rawOrder());

        $this->assertSame(1, $order->snapshots()->count());
        $snapshot = $order->snapshots()->first();
        $this->assertSame(OrderStatus::Pending->value, $snapshot->status);
        $this->assertSame(56730, $snapshot->total);
    }

    public function test_does_not_record_duplicate_snapshot_for_unchanged_order(): void
    {
        $this->service->syncFromSalla($this->rawOrder());
        $this->service->syncFromSalla($this->rawOrder());

        $order = Order::where('salla_id', '1000')->first();

        $this->assertSame(1, $order->snapshots()->count());
    }

    public function test_records_new_snapshot_when_status_changes(): void
    {
        $this->service->syncFromSalla($this->rawOrder());

        $updated = $this->rawOrder();
        $updated['status'] = ['slug' => 'completed', 'name' => 'مكتمل'];

        $order = $this->service->syncFromSalla($updated);

        $this->assertSame(2, $order->snapshots()->count());
        $this->assertSame(OrderStatus::Completed, $order->local_status);
    }

    public function test_maps_cancelled_status(): void
    {
        $raw = $this->rawOrder();
        $raw['status'] = ['slug' => 'cancelled', 'name' => 'ملغي'];

        $order = $this->service->syncFromSalla($raw);

        $this->assertSame(OrderStatus::Cancelled, $order->local_status);
        $this->assertSame(PaymentStatus::Failed, $order->payment_status);
    }

    public function test_maps_refunded_status(): void
    {
        $raw = $this->rawOrder();
        $raw['status'] = ['slug' => 'refunded', 'name' => 'مسترجع'];

        $order = $this->service->syncFromSalla($raw);

        $this->assertSame(OrderStatus::Refunded, $order->local_status);
        $this->assertSame(PaymentStatus::Refunded, $order->payment_status);
    }

    public function test_maps_payment_info_from_payment_payload(): void
    {
        $raw = $this->rawOrder();
        $raw['payment'] = ['status' => ['slug' => 'paid', 'name' => 'مدفوع']];

        $order = $this->service->syncFromSalla($raw);

        $this->assertSame(PaymentStatus::Paid, $order->payment_status);
    }

    public function test_unknown_payment_method_maps_to_other(): void
    {
        $raw = $this->rawOrder();
        $raw['payment_method'] = 'some_future_method';

        $order = $this->service->syncFromSalla($raw);

        $this->assertSame(PaymentMethod::Other, $order->payment_method);
    }
}