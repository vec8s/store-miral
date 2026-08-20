<?php

declare(strict_types=1);

namespace Tests\Unit\Salla\DTOs;

use App\Domains\Shared\DTOs\CouponDTO;
use PHPUnit\Framework\TestCase;

class CouponDTOTest extends TestCase
{
    public function test_from_salla_response_maps_fields(): void
    {
        $dto = CouponDTO::fromSallaResponse([
            'id' => 33,
            'name' => 'Ramadan Sale',
            'code' => 'RAMADAN15',
            'value' => 15,
            'type' => 'percentage',
            'starts_at' => '2026-03-01T00:00:00+03:00',
            'ends_at' => '2026-03-31T23:59:59+03:00',
            'min_total' => ['amount' => 100.0, 'currency' => 'SAR'],
            'discount_limit' => ['amount' => 50.0, 'currency' => 'SAR'],
            'total_usage' => 120,
            'limit_per_user' => 1,
            'status' => 'active',
            'created_at' => '2026-02-20T00:00:00+03:00',
            'updated_at' => '2026-03-15T00:00:00+03:00',
        ]);

        $this->assertSame(33, $dto->id);
        $this->assertSame('Ramadan Sale', $dto->name);
        $this->assertSame('RAMADAN15', $dto->code);
        $this->assertSame(15.0, $dto->value);
        $this->assertSame('percentage', $dto->type);
        $this->assertSame('2026-03-31T23:59:59+03:00', $dto->endsAt);
        $this->assertSame(100.0, $dto->minTotal);
        $this->assertSame(50.0, $dto->discountLimit);
        $this->assertSame(120, $dto->totalUsage);
        $this->assertSame(1, $dto->limitPerUser);
        $this->assertSame('active', $dto->status);
    }

    public function test_from_salla_response_handles_missing_optional_fields(): void
    {
        $dto = CouponDTO::fromSallaResponse(['id' => 1, 'code' => 'GIFT']);

        $this->assertSame(1, $dto->id);
        $this->assertSame('GIFT', $dto->code);
        $this->assertNull($dto->name);
        $this->assertSame(0.0, $dto->value);
        $this->assertNull($dto->type);
        $this->assertNull($dto->endsAt);
        $this->assertSame(0.0, $dto->minTotal);
        $this->assertSame(0.0, $dto->discountLimit);
    }
}