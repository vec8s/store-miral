<?php

declare(strict_types=1);

namespace Tests\Unit\Salla\DTOs;

use App\Domains\Shared\DTOs\OrderDTO;
use PHPUnit\Framework\TestCase;

class OrderDTOTest extends TestCase
{
    public function test_from_salla_response_maps_fields(): void
    {
        $dto = OrderDTO::fromSallaResponse([
            'id' => 555,
            'reference_id' => 1001,
            'date' => ['date' => '2026-08-10T14:30:00+03:00', 'timezone' => '+03:00'],
            'source' => 'web',
            'status' => ['id' => 1, 'name' => 'Processing', 'slug' => 'processing'],
            'payment_method' => 'mada',
            'amounts' => [
                'sub_total' => ['amount' => 200.0, 'currency' => 'SAR'],
                'shipping_cost' => ['amount' => 25.0, 'currency' => 'SAR'],
                'tax' => ['amount' => 30.0, 'currency' => 'SAR'],
                'discounts' => ['amount' => 10.0, 'currency' => 'SAR'],
                'total' => ['amount' => 245.0, 'currency' => 'SAR'],
            ],
            'items' => [['id' => 1], ['id' => 2]],
            'total_weight' => 1.5,
            'currency' => 'SAR',
        ]);

        $this->assertSame(555, $dto->id);
        $this->assertSame(1001, $dto->referenceId);
        $this->assertSame('2026-08-10T14:30:00+03:00', $dto->placedAt);
        $this->assertSame('processing', $dto->statusSlug);
        $this->assertSame('Processing', $dto->statusName);
        $this->assertSame('mada', $dto->paymentMethod);
        $this->assertSame(200.0, $dto->subTotal);
        $this->assertSame(25.0, $dto->shippingCost);
        $this->assertSame(30.0, $dto->tax);
        $this->assertSame(10.0, $dto->discounts);
        $this->assertSame(245.0, $dto->total);
        $this->assertSame('SAR', $dto->currency);
        $this->assertSame(2, $dto->itemCount);
    }

    public function test_from_salla_response_handles_missing_optional_fields(): void
    {
        $dto = OrderDTO::fromSallaResponse(['id' => 1]);

        $this->assertSame(1, $dto->id);
        $this->assertNull($dto->referenceId);
        $this->assertNull($dto->placedAt);
        $this->assertNull($dto->statusSlug);
        $this->assertNull($dto->paymentMethod);
        $this->assertSame(0.0, $dto->total);
        $this->assertSame(0, $dto->itemCount);
    }
}