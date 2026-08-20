<?php

declare(strict_types=1);

namespace Tests\Unit\Salla\Sync;

use App\Domains\Catalog\Enums\ProductStatus;
use App\Domains\Catalog\Enums\ProductVisibility;
use App\Domains\Catalog\Enums\SyncStatus;
use App\Domains\Catalog\Models\Product;
use App\Shared\Salla\Sync\ProductSyncService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ProductSyncServiceTest extends TestCase
{
    use RefreshDatabase;

    private ProductSyncService $service;

    protected function setUp(): void
    {
        parent::setUp();

        $this->service = new ProductSyncService();
    }

    private function rawProduct(int $id = 101): array
    {
        return [
            'id' => $id,
            'name' => 'ساعة فاخرة',
            'sku' => 'SKU-'.$id,
            'mpn' => 'MPN-'.$id,
            'gtin' => '6291041500213',
            'status' => 'active',
            'price' => ['amount' => 520.0, 'currency' => 'SAR'],
            'sale_price' => ['amount' => 460.0, 'currency' => 'SAR'],
            'quantity' => 12,
            'description' => 'وصف المنتج',
            'images' => [
                ['id' => 1, 'url' => 'https://img.example/a.jpg', 'thumbnail' => 'https://img.example/a-thumb.jpg'],
                ['id' => 2, 'url' => 'https://img.example/b.jpg'],
            ],
            'options' => [
                [
                    'name' => 'الحجم',
                    'type' => 'size',
                    'is_required' => true,
                    'values' => ['M', 'L'],
                ],
            ],
            'variants' => [
                ['id' => 501, 'name' => 'M', 'sku' => 'SKU-101-M', 'price' => ['amount' => 520.0], 'quantity' => 6],
                ['id' => 502, 'name' => 'L', 'sku' => 'SKU-101-L', 'price' => ['amount' => 520.0], 'quantity' => 6],
            ],
            'updated_at' => '2026-08-15T10:00:00Z',
        ];
    }

    public function test_creates_product_from_salla_payload(): void
    {
        $product = $this->service->syncFromSalla($this->rawProduct());

        $this->assertInstanceOf(Product::class, $product);
        $this->assertSame('101', $product->salla_id);
        $this->assertSame('ساعة فاخرة', $product->name);
        $this->assertSame('SKU-101', $product->sku);
        $this->assertSame(52000, $product->price_minor);
        $this->assertSame(46000, $product->sale_price_minor);
        $this->assertSame('SAR', $product->currency);
        $this->assertSame(12, $product->quantity);
        $this->assertSame(ProductStatus::Active, $product->status);
        $this->assertSame(ProductVisibility::Visible, $product->visibility);
        $this->assertTrue($product->is_available);
        $this->assertSame(SyncStatus::Synced, $product->sync_status);
    }

    public function test_updates_existing_product_instead_of_duplicating(): void
    {
        $this->service->syncFromSalla($this->rawProduct(101));
        $second = $this->service->syncFromSalla(array_merge($this->rawProduct(101), [
            'name' => 'ساعة فاخرة معدلة',
            'price' => ['amount' => 600.0, 'currency' => 'SAR'],
        ]));

        $this->assertSame(1, Product::count());
        $this->assertSame('ساعة فاخرة معدلة', $second->name);
        $this->assertSame(60000, $second->price_minor);
    }

    public function test_syncs_images_with_main_flag_on_first(): void
    {
        $product = $this->service->syncFromSalla($this->rawProduct());

        $this->assertSame(2, $product->images()->count());
        $this->assertSame('https://img.example/a-thumb.jpg', $product->main_image_url);
        $this->assertTrue($product->images()->first()->is_main);
        $this->assertFalse($product->images()->skip(1)->first()->is_main);
    }

    public function test_syncs_options_and_values(): void
    {
        $product = $this->service->syncFromSalla($this->rawProduct());

        $option = $product->options()->first();

        $this->assertNotNull($option);
        $this->assertSame('الحجم', $option->name);
        $this->assertSame(2, $option->values()->count());
    }

    public function test_syncs_variants(): void
    {
        $product = $this->service->syncFromSalla($this->rawProduct());

        $this->assertSame(2, $product->variants()->count());
        $variantM = $product->variants()->where('sku', 'SKU-101-M')->first();
        $this->assertNotNull($variantM);
        $this->assertSame(52000, $variantM->price_minor);
    }

    public function test_replacing_children_removes_deleted_ones(): void
    {
        $this->service->syncFromSalla($this->rawProduct());

        $updated = $this->rawProduct(101);
        $updated['images'] = [['url' => 'https://img.example/c.jpg']];
        $updated['variants'] = [['id' => 501, 'name' => 'M', 'sku' => 'SKU-101-M', 'price' => ['amount' => 520.0]]];
        $updated['options'] = [];

        $product = $this->service->syncFromSalla($updated);

        $this->assertSame(1, $product->images()->count());
        $this->assertSame(1, $product->variants()->count());
        $this->assertSame(0, $product->options()->count());
    }

    public function test_draft_product_is_hidden_and_unavailable(): void
    {
        $raw = $this->rawProduct();
        $raw['status'] = 'draft';

        $product = $this->service->syncFromSalla($raw);

        $this->assertSame(ProductStatus::Draft, $product->status);
        $this->assertSame(ProductVisibility::Hidden, $product->visibility);
        $this->assertFalse($product->is_available);
    }

    public function test_zero_stock_product_is_unavailable(): void
    {
        $raw = $this->rawProduct();
        $raw['quantity'] = 0;

        $product = $this->service->syncFromSalla($raw);

        $this->assertFalse($product->is_available);
    }

    public function test_delete_marks_product_as_deleted(): void
    {
        $this->service->syncFromSalla($this->rawProduct());

        $result = $this->service->syncDeleted('101');

        $this->assertTrue($result);
        $this->assertTrue(Product::where('salla_id', '101')->withTrashed()->first()->trashed());
    }

    public function test_delete_returns_false_for_missing_product(): void
    {
        $this->assertFalse($this->service->syncDeleted('999'));
    }
}