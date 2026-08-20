<?php

declare(strict_types=1);

namespace Tests\Unit\Salla\DTOs;

use App\Domains\Shared\DTOs\CategoryDTO;
use PHPUnit\Framework\TestCase;

class CategoryDTOTest extends TestCase
{
    public function test_from_salla_response_maps_fields(): void
    {
        $dto = CategoryDTO::fromSallaResponse([
            'id' => 42,
            'name' => 'Accessories',
            'description' => 'Rings, necklaces',
            'icon' => '💍',
            'sort' => 3,
            'products_count' => 27,
            'status' => 'active',
            'created_at' => '2026-01-01T00:00:00+03:00',
            'updated_at' => '2026-07-15T00:00:00+03:00',
        ]);

        $this->assertSame(42, $dto->id);
        $this->assertSame('Accessories', $dto->name);
        $this->assertSame('Rings, necklaces', $dto->description);
        $this->assertSame('💍', $dto->icon);
        $this->assertSame(3, $dto->sort);
        $this->assertSame(27, $dto->productsCount);
        $this->assertSame('active', $dto->status);
        $this->assertSame('2026-07-15T00:00:00+03:00', $dto->updatedAt);
    }

    public function test_from_salla_response_handles_missing_optional_fields(): void
    {
        $dto = CategoryDTO::fromSallaResponse(['id' => 1, 'name' => 'Minimal']);

        $this->assertSame(1, $dto->id);
        $this->assertSame('Minimal', $dto->name);
        $this->assertNull($dto->description);
        $this->assertNull($dto->icon);
        $this->assertSame(0, $dto->sort);
        $this->assertSame(0, $dto->productsCount);
        $this->assertNull($dto->status);
    }
}