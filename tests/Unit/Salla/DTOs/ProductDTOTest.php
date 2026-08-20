<?php

declare(strict_types=1);

namespace Tests\Unit\Salla\DTOs;

use App\Domains\Shared\DTOs\ProductDTO;
use PHPUnit\Framework\TestCase;

class ProductDTOTest extends TestCase
{
    public function test_from_salla_response_maps_all_fields(): void
    {
        $dto = ProductDTO::fromSallaResponse([
            'id' => 12345,
            'name' => 'Classic Black T-Shirt',
            'sku' => 'TSHIRT-BLK-L',
            'mpn' => 'MPN-1',
            'gtin' => '6291041500213',
            'type' => 'product',
            'status' => 'sale',
            'price' => ['amount' => 99.0, 'currency' => 'SAR'],
            'sale_price' => ['amount' => 79.0, 'currency' => 'SAR'],
            'cost_price' => ['amount' => 40.0, 'currency' => 'SAR'],
            'quantity' => 50,
            'description' => 'A comfortable black tee',
            'url' => 'https://example.store/product/12345',
            'categories' => [1, 2],
            'brand_id' => 7,
            'tags' => ['tshirt', 'black'],
            'images' => [
                ['id' => 1, 'original' => 'https://img/o.jpg', 'thumbnail' => 'https://img/t.jpg', 'alt' => 'tee', 'sort' => 0],
            ],
            'updated_at' => '2026-08-01T10:00:00+03:00',
        ]);

        $this->assertSame(12345, $dto->id);
        $this->assertSame('Classic Black T-Shirt', $dto->name);
        $this->assertSame('TSHIRT-BLK-L', $dto->sku);
        $this->assertSame('MPN-1', $dto->mpn);
        $this->assertSame('6291041500213', $dto->gtin);
        $this->assertSame('product', $dto->type);
        $this->assertSame('sale', $dto->status);
        $this->assertSame(99.0, $dto->price);
        $this->assertSame('SAR', $dto->currency);
        $this->assertSame(79.0, $dto->salePrice);
        $this->assertSame(40.0, $dto->costPrice);
        $this->assertSame(50, $dto->quantity);
        $this->assertSame([1, 2], $dto->categoryIds);
        $this->assertSame(7, $dto->brandId);
        $this->assertSame(['tshirt', 'black'], $dto->tags);
        $this->assertSame('https://img/t.jpg', $dto->thumbnail);
        $this->assertSame('2026-08-01T10:00:00+03:00', $dto->updatedAt);
    }

    public function test_from_salla_response_handles_missing_optional_fields(): void
    {
        $dto = ProductDTO::fromSallaResponse(['id' => 1, 'name' => 'Minimal', 'price' => ['amount' => 10.0]]);

        $this->assertSame(1, $dto->id);
        $this->assertSame('Minimal', $dto->name);
        $this->assertSame(10.0, $dto->price);
        $this->assertNull($dto->sku);
        $this->assertNull($dto->salePrice);
        $this->assertSame([], $dto->categoryIds);
        $this->assertSame([], $dto->tags);
        $this->assertNull($dto->thumbnail);
    }

    public function test_price_defaults_to_zero_when_missing(): void
    {
        $dto = ProductDTO::fromSallaResponse(['id' => 1, 'name' => 'No Price']);

        $this->assertSame(0.0, $dto->price);
    }
}