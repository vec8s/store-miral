<?php

declare(strict_types=1);

namespace Tests\Unit\Salla\DTOs;

use App\Domains\Shared\DTOs\BrandDTO;
use PHPUnit\Framework\TestCase;

class BrandDTOTest extends TestCase
{
    public function test_from_salla_response_maps_fields(): void
    {
        $dto = BrandDTO::fromSallaResponse([
            'id' => 9,
            'name' => 'Rafal',
            'logo' => 'https://img/logo.png',
            'cover' => 'https://img/cover.png',
            'url' => 'https://example.store/brands/rafal',
            'description' => 'Luxury brand',
            'status' => 'active',
            'created_at' => '2026-02-01T00:00:00+03:00',
            'updated_at' => '2026-07-01T00:00:00+03:00',
        ]);

        $this->assertSame(9, $dto->id);
        $this->assertSame('Rafal', $dto->name);
        $this->assertSame('https://img/logo.png', $dto->logo);
        $this->assertSame('https://img/cover.png', $dto->cover);
        $this->assertSame('https://example.store/brands/rafal', $dto->url);
        $this->assertSame('Luxury brand', $dto->description);
        $this->assertSame('active', $dto->status);
        $this->assertSame('2026-07-01T00:00:00+03:00', $dto->updatedAt);
    }

    public function test_from_salla_response_handles_missing_optional_fields(): void
    {
        $dto = BrandDTO::fromSallaResponse(['id' => 1, 'name' => 'Minimal']);

        $this->assertSame(1, $dto->id);
        $this->assertNull($dto->logo);
        $this->assertNull($dto->description);
        $this->assertNull($dto->status);
    }
}